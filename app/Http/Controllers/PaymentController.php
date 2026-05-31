<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\SiteSetting;
use App\Models\RoomCalendar;
use App\Helpers\price_calculation;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PaymentController extends Controller
{
    private function getPaypalBaseUrl()
    {
        $mode = SiteSetting::get('paypal_mode', 'sandbox');
        return $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
    }

    private function getPaypalAccessToken()
    {
        $clientId = SiteSetting::get('paypal_client_id');
        $secret = SiteSetting::get('paypal_secret');
        $baseUrl = $this->getPaypalBaseUrl();

        $response = Http::withBasicAuth($clientId, $secret)
            ->asForm()
            ->post("{$baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials'
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        return null;
    }

    public function initiatePaypal(Request $request)
    {
        // 1. Validate inputs
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'checkin' => 'required|date',
            'checkout' => 'required|date|after:checkin',
            'guests' => 'required|integer|min:1',
            'enhancement_ids' => 'nullable|string',
            'enhancement_dates' => 'nullable|string',
        ]);

        $room = Room::findOrFail($request->room_id);

        $enhancementIds = array_filter(explode(',', $request->enhancement_ids ?? ''));
        $enhancementDates = json_decode($request->enhancement_dates ?? '{}', true) ?: [];

        // 2. Calculate actual price backend
        $priceData = price_calculation::calculate(
            $room,
            $request->checkin,
            $request->checkout,
            $request->guests,
            $enhancementIds,
            $enhancementDates
        );

        if (!$priceData['success']) {
            return redirect()->back()->with('error', $priceData['message']);
        }

        $finalTotal = $priceData['finalTotal'];

        // 3. Create Pending Reservation
        $reservation = Reservation::create([
            'room_id' => $room->id,
            'user_id' => auth()->id() ?? 1, // Fallback if not logged in
            'checkin' => $request->checkin,
            'checkout' => $request->checkout,
            'guests' => $request->guests,
            'base_amount' => $priceData['discountedBasePrice'],
            'service_fee' => $priceData['serviceFeeAmt'],
            'tax' => $priceData['taxAmt'],
            'food_amount' => $priceData['totalEnhancementFee'],
            'total_amount' => $finalTotal,
            'enhancements_data' => $priceData['selectedEnhancements'] ?? [],
            'payment_type' => 'PayPal',
            'status' => 'pending'
        ]);

        // 4. Hit PayPal API to create Order
        $token = $this->getPaypalAccessToken();
        if (!$token) {
            return redirect()->back()->with('error', 'PayPal is not configured correctly or unavailable.');
        }

        $currency = SiteSetting::get('default_currency', 'USD');
        $paypalAmount = $finalTotal;

        // PayPal sandbox and many international accounts do not support INR. 
        // We convert to USD roughly for the PayPal gateway.
        if (strtoupper($currency) === 'INR') {
            $currency = 'USD';
            $paypalAmount = $finalTotal / 83.0; // approximate exchange rate
        }

        $response = Http::withToken($token)->post($this->getPaypalBaseUrl() . '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => 'RES_' . $reservation->id,
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => number_format($paypalAmount, 2, '.', '')
                    ],
                    'description' => "Booking for " . ($room->title ?: $room->name)
                ]
            ],
            'application_context' => [
                'return_url' => route('payment.paypal.success', ['reservation' => $reservation->id]),
                'cancel_url' => route('payment.paypal.cancel', ['reservation' => $reservation->id]),
                'user_action' => 'PAY_NOW'
            ]
        ]);

        if ($response->successful()) {
            $links = $response->json()['links'];
            foreach ($links as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect($link['href']);
                }
            }
        }

        // Extract detailed error message from PayPal if available
        $errorDetail = $response->json()['details'][0]['description'] ?? $response->json()['message'] ?? 'Failed to create PayPal order.';
        
        return redirect()->back()->with('error', 'PayPal API Error: ' . $errorDetail);
    }

    public function paypalSuccess(Request $request)
    {
        $reservation = Reservation::findOrFail($request->reservation);
        
        $token = $request->query('token'); // PayPal order token
        $payerId = $request->query('PayerID');

        if (!$token || (!$payerId && $reservation->status !== 'success')) {
            $reservation->update(['status' => 'failed']);
            return redirect()->route('rooms.show', $reservation->room_id)->with('error', 'Payment failed or cancelled.');
        }

        // Capture payment
        $accessToken = $this->getPaypalAccessToken();
        $response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post($this->getPaypalBaseUrl() . "/v2/checkout/orders/{$token}/capture", []);

        if ($response->successful()) {
            $data = $response->json();
            $status = $data['status'] ?? '';
            
            if ($status === 'COMPLETED') {
                // Payment successful
                $captureId = $data['purchase_units'][0]['payments']['captures'][0]['id'] ?? '';
                
                $reservation->update([
                    'status' => 'success',
                    'reservation_status' => 'accepted',
                    'transaction_id' => $captureId
                ]);

                // Block the dates
                $start = Carbon::parse($reservation->checkin);
                $end = Carbon::parse($reservation->checkout);
                while ($start->lt($end)) {
                    RoomCalendar::updateOrCreate(
                        ['room_id' => $reservation->room_id, 'date' => $start->format('Y-m-d')],
                        ['is_blocked' => true]
                    );
                    $start->addDay();
                }

                return redirect()->route('rooms.show', $reservation->room_id)->with('success', 'Booking confirmed and payment successful!');
            }
        }

        $reservation->update(['status' => 'failed']);
        return redirect()->route('rooms.show', $reservation->room_id)->with('error', 'Payment capture failed.');
    }

    public function paypalCancel(Request $request)
    {
        $reservation = Reservation::findOrFail($request->reservation);
        $reservation->update(['status' => 'failed']);
        return redirect()->route('rooms.show', $reservation->room_id)->with('error', 'You cancelled the payment.');
    }
    // ─────────────────────────────────────────────────────────────────
    // STRIPE INTEGRATION
    // ─────────────────────────────────────────────────────────────────

    public function initiateStripe(Request $request)
    {
        // 1. Validate inputs
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'checkin' => 'required|date',
            'checkout' => 'required|date|after:checkin',
            'guests' => 'required|integer|min:1',
            'enhancement_ids' => 'nullable|string',
            'enhancement_dates' => 'nullable|string',
        ]);

        $room = Room::findOrFail($request->room_id);

        $enhancementIds = array_filter(explode(',', $request->enhancement_ids ?? ''));
        $enhancementDates = json_decode($request->enhancement_dates ?? '{}', true) ?: [];

        // 2. Calculate actual price backend
        $priceData = price_calculation::calculate(
            $room,
            $request->checkin,
            $request->checkout,
            $request->guests,
            $enhancementIds,
            $enhancementDates
        );

        if (!$priceData['success']) {
            return redirect()->back()->with('error', $priceData['message']);
        }

        $finalTotal = $priceData['finalTotal'];

        // 3. Create Pending Reservation
        $reservation = Reservation::create([
            'room_id' => $room->id,
            'user_id' => auth()->id() ?? 1, // Fallback if not logged in
            'checkin' => $request->checkin,
            'checkout' => $request->checkout,
            'guests' => $request->guests,
            'base_amount' => $priceData['discountedBasePrice'],
            'service_fee' => $priceData['serviceFeeAmt'],
            'tax' => $priceData['taxAmt'],
            'food_amount' => $priceData['totalEnhancementFee'],
            'total_amount' => $finalTotal,
            'enhancements_data' => $priceData['selectedEnhancements'] ?? [],
            'payment_type' => 'Stripe',
            'status' => 'pending'
        ]);

        // 4. Hit Stripe API to create Order
        $stripeSecret = SiteSetting::get('stripe_secret');
        if (!$stripeSecret) {
            return redirect()->back()->with('error', 'Stripe is not configured correctly.');
        }

        \Stripe\Stripe::setApiKey($stripeSecret);

        $currency = SiteSetting::get('default_currency', 'usd');

        try {
            $checkout_session = \Stripe\Checkout\Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($currency),
                        'product_data' => [
                            'name' => "Booking for " . ($room->title ?: $room->name),
                        ],
                        'unit_amount' => (int) round($finalTotal * 100), // Stripe uses cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.stripe.cancel', ['reservation' => $reservation->id]),
                'client_reference_id' => $reservation->id,
            ]);

            return redirect($checkout_session->url);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Stripe Error: ' . $e->getMessage());
        }
    }

    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('home')->with('error', 'Invalid payment session.');
        }

        $stripeSecret = SiteSetting::get('stripe_secret');
        \Stripe\Stripe::setApiKey($stripeSecret);

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            $reservationId = $session->client_reference_id;
            
            $reservation = Reservation::findOrFail($reservationId);

            if ($session->payment_status === 'paid') {
                $reservation->update([
                    'status' => 'success',
                    'reservation_status' => 'accepted',
                    'transaction_id' => $session->payment_intent
                ]);

                // Block the dates
                $start = Carbon::parse($reservation->checkin);
                $end = Carbon::parse($reservation->checkout);
                while ($start->lt($end)) {
                    RoomCalendar::updateOrCreate(
                        ['room_id' => $reservation->room_id, 'date' => $start->format('Y-m-d')],
                        ['is_blocked' => true]
                    );
                    $start->addDay();
                }

                return redirect()->route('rooms.show', $reservation->room_id)->with('success', 'Booking confirmed and payment successful via Stripe!');
            } else {
                $reservation->update(['status' => 'failed']);
                return redirect()->route('rooms.show', $reservation->room_id)->with('error', 'Payment was not successful.');
            }

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Stripe Verification Error: ' . $e->getMessage());
        }
    }

    public function stripeCancel(Request $request)
    {
        $reservation = Reservation::findOrFail($request->reservation);
        $reservation->update(['status' => 'failed']);
        return redirect()->route('rooms.show', $reservation->room_id)->with('error', 'You cancelled the Stripe payment.');
    }

    // ─────────────────────────────────────────────────────────────────
    // EASEBUZZ INTEGRATION
    // ─────────────────────────────────────────────────────────────────

    public function initiateEasebuzz(Request $request)
    {
        // 1. Validate inputs
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'checkin' => 'required|date',
            'checkout' => 'required|date|after:checkin',
            'guests' => 'required|integer|min:1',
            'enhancement_ids' => 'nullable|string',
            'enhancement_dates' => 'nullable|string',
        ]);

        $room = Room::findOrFail($request->room_id);

        $enhancementIds = array_filter(explode(',', $request->enhancement_ids ?? ''));
        $enhancementDates = json_decode($request->enhancement_dates ?? '{}', true) ?: [];

        // 2. Calculate actual price backend
        $priceData = price_calculation::calculate(
            $room,
            $request->checkin,
            $request->checkout,
            $request->guests,
            $enhancementIds,
            $enhancementDates
        );

        if (!$priceData['success']) {
            return redirect()->back()->with('error', $priceData['message']);
        }

        $finalTotal = $priceData['finalTotal'];

        // 3. Create Pending Reservation
        $reservation = Reservation::create([
            'room_id' => $room->id,
            'user_id' => auth()->id() ?? 1,
            'checkin' => $request->checkin,
            'checkout' => $request->checkout,
            'guests' => $request->guests,
            'base_amount' => $priceData['discountedBasePrice'],
            'service_fee' => $priceData['serviceFeeAmt'],
            'tax' => $priceData['taxAmt'],
            'food_amount' => $priceData['totalEnhancementFee'],
            'total_amount' => $finalTotal,
            'enhancements_data' => $priceData['selectedEnhancements'] ?? [],
            'payment_type' => 'Easebuzz',
            'status' => 'pending'
        ]);

        $key = SiteSetting::get('easebuzz_merchant_key');
        $salt = SiteSetting::get('easebuzz_salt');
        $env = SiteSetting::get('easebuzz_env', 'sandbox');
        
        if (!$key || !$salt) {
            return redirect()->back()->with('error', 'Easebuzz is not configured correctly.');
        }

        $txnid = 'RES_' . $reservation->id . '_' . time();
        $amount = number_format($finalTotal, 2, '.', '');
        $productinfo = "Booking for " . ($room->title ?: $room->name);
        $firstname = auth()->user()->name ?? 'Guest';
        $email = auth()->user()->email ?? 'guest@example.com';
        $phone = auth()->user()->phone ?? '9999999999';
        
        $surl = route('payment.easebuzz.success');
        $furl = route('payment.easebuzz.cancel');
        
        $udf1 = $reservation->id;
        $udf2 = ''; $udf3 = ''; $udf4 = ''; $udf5 = ''; $udf6 = ''; $udf7 = ''; $udf8 = ''; $udf9 = ''; $udf10 = '';

        $hashString = "{$key}|{$txnid}|{$amount}|{$productinfo}|{$firstname}|{$email}|{$udf1}|{$udf2}|{$udf3}|{$udf4}|{$udf5}|{$udf6}|{$udf7}|{$udf8}|{$udf9}|{$udf10}|{$salt}";
        $hash = hash('sha512', $hashString);

        $baseUrl = $env === 'live' ? 'https://pay.easebuzz.in' : 'https://testpay.easebuzz.in';

        $response = Http::asForm()->post("{$baseUrl}/payment/initiateLink", [
            'key' => $key,
            'txnid' => $txnid,
            'amount' => $amount,
            'productinfo' => $productinfo,
            'firstname' => $firstname,
            'phone' => $phone,
            'email' => $email,
            'surl' => $surl,
            'furl' => $furl,
            'hash' => $hash,
            'udf1' => $udf1,
            'udf2' => $udf2, 'udf3' => $udf3, 'udf4' => $udf4, 'udf5' => $udf5, 'udf6' => $udf6, 'udf7' => $udf7, 'udf8' => $udf8, 'udf9' => $udf9, 'udf10' => $udf10
        ]);

        if ($response->successful() && isset($response->json()['data'])) {
            $accessKey = $response->json()['data'];
            return redirect("{$baseUrl}/pay/v1/ui/#/{$accessKey}/");
        }

        return redirect()->back()->with('error', 'Easebuzz API Error: ' . ($response->json()['error_desc'] ?? 'Failed to initiate payment'));
    }

    public function easebuzzSuccess(Request $request)
    {
        $status = $request->input('status');
        $reservationId = $request->input('udf1');
        $reservation = Reservation::findOrFail($reservationId);

        $key = SiteSetting::get('easebuzz_merchant_key');
        $salt = SiteSetting::get('easebuzz_salt');

        $udf1 = $request->input('udf1');
        $udf2 = $request->input('udf2');
        $udf3 = $request->input('udf3');
        $udf4 = $request->input('udf4');
        $udf5 = $request->input('udf5');
        $udf6 = $request->input('udf6');
        $udf7 = $request->input('udf7');
        $udf8 = $request->input('udf8');
        $udf9 = $request->input('udf9');
        $udf10 = $request->input('udf10');

        $email = $request->input('email');
        $firstname = $request->input('firstname');
        $productinfo = $request->input('productinfo');
        $amount = $request->input('amount');
        $txnid = $request->input('txnid');

        $reverseHashString = "{$salt}|{$status}|{$udf10}|{$udf9}|{$udf8}|{$udf7}|{$udf6}|{$udf5}|{$udf4}|{$udf3}|{$udf2}|{$udf1}|{$email}|{$firstname}|{$productinfo}|{$amount}|{$txnid}|{$key}";
        $calculatedHash = hash('sha512', $reverseHashString);

        if ($calculatedHash !== $request->input('hash')) {
            return redirect()->route('rooms.show', $reservation->room_id)->with('error', 'Payment verification failed.');
        }

        if ($status === 'success') {
            $reservation->update([
                'status' => 'success',
                'reservation_status' => 'accepted',
                'transaction_id' => $request->input('easepayid')
            ]);

            $start = Carbon::parse($reservation->checkin);
            $end = Carbon::parse($reservation->checkout);
            while ($start->lt($end)) {
                RoomCalendar::updateOrCreate(
                    ['room_id' => $reservation->room_id, 'date' => $start->format('Y-m-d')],
                    ['is_blocked' => true]
                );
                $start->addDay();
            }

            return redirect()->route('rooms.show', $reservation->room_id)->with('success', 'Booking confirmed and payment successful via Easebuzz!');
        }

        $reservation->update(['status' => 'failed']);
        return redirect()->route('rooms.show', $reservation->room_id)->with('error', 'Payment failed.');
    }

    public function easebuzzCancel(Request $request)
    {
        $reservationId = $request->input('udf1');
        if ($reservationId) {
            $reservation = Reservation::find($reservationId);
            if ($reservation) {
                $reservation->update(['status' => 'failed']);
                return redirect()->route('rooms.show', $reservation->room_id)->with('error', 'You cancelled the Easebuzz payment.');
            }
        }
        return redirect()->route('home')->with('error', 'Payment cancelled.');
    }

    // ─────────────────────────────────────────────────────────────────
    // RAZORPAY INTEGRATION
    // ─────────────────────────────────────────────────────────────────

    public function initiateRazorpay(Request $request)
    {
        // 1. Validate inputs
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'checkin' => 'required|date',
            'checkout' => 'required|date|after:checkin',
            'guests' => 'required|integer|min:1',
            'enhancement_ids' => 'nullable|string',
            'enhancement_dates' => 'nullable|string',
        ]);

        $room = Room::findOrFail($request->room_id);

        $enhancementIds = array_filter(explode(',', $request->enhancement_ids ?? ''));
        $enhancementDates = json_decode($request->enhancement_dates ?? '{}', true) ?: [];

        $priceData = price_calculation::calculate(
            $room,
            $request->checkin,
            $request->checkout,
            $request->guests,
            $enhancementIds,
            $enhancementDates
        );

        if (!$priceData['success']) {
            return redirect()->back()->with('error', $priceData['message']);
        }

        $finalTotal = $priceData['finalTotal'];

        // Create Pending Reservation
        $reservation = Reservation::create([
            'room_id' => $room->id,
            'user_id' => auth()->id() ?? 1,
            'checkin' => $request->checkin,
            'checkout' => $request->checkout,
            'guests' => $request->guests,
            'base_amount' => $priceData['discountedBasePrice'],
            'service_fee' => $priceData['serviceFeeAmt'],
            'tax' => $priceData['taxAmt'],
            'food_amount' => $priceData['totalEnhancementFee'],
            'total_amount' => $finalTotal,
            'enhancements_data' => $priceData['selectedEnhancements'] ?? [],
            'payment_type' => 'Razorpay',
            'status' => 'pending'
        ]);

        $key = SiteSetting::get('razorpay_key');
        $secret = SiteSetting::get('razorpay_secret');
        
        if (!$key || !$secret) {
            return redirect()->back()->with('error', 'Razorpay is not configured correctly.');
        }

        // Razorpay expects amount in paise
        $amountInPaise = intval(round($finalTotal * 100));
        
        // Create order via Razorpay API using HTTP Client
        $response = Http::withBasicAuth($key, $secret)
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'receipt' => 'RES_' . $reservation->id
            ]);

        if ($response->successful()) {
            $order = $response->json();
            return view('rooms.razorpay_checkout', [
                'order_id' => $order['id'],
                'amount' => $amountInPaise,
                'key' => $key,
                'reservation_id' => $reservation->id,
                'room' => $room
            ]);
        }

        return redirect()->back()->with('error', 'Razorpay API Error: ' . ($response->json()['error']['description'] ?? 'Failed to initiate payment'));
    }

    public function razorpaySuccess(Request $request)
    {
        $reservation = Reservation::findOrFail($request->input('reservation_id'));
        
        $payment_id = $request->input('razorpay_payment_id');
        $order_id = $request->input('razorpay_order_id');
        $signature = $request->input('razorpay_signature');
        $secret = SiteSetting::get('razorpay_secret');

        $generated_signature = hash_hmac('sha256', $order_id . "|" . $payment_id, $secret);

        if ($generated_signature === $signature) {
            $reservation->update([
                'status' => 'success',
                'reservation_status' => 'accepted',
                'transaction_id' => $payment_id
            ]);

            // Block the dates
            $start = Carbon::parse($reservation->checkin);
            $end = Carbon::parse($reservation->checkout);
            while ($start->lt($end)) {
                RoomCalendar::updateOrCreate(
                    ['room_id' => $reservation->room_id, 'date' => $start->format('Y-m-d')],
                    ['is_blocked' => true]
                );
                $start->addDay();
            }

            return redirect()->route('rooms.show', $reservation->room_id)->with('success', 'Booking confirmed and payment successful via Razorpay!');
        }

        $reservation->update(['status' => 'failed']);
        return redirect()->route('rooms.show', $reservation->room_id)->with('error', 'Razorpay Payment verification failed.');
    }

    public function razorpayCancel(Request $request)
    {
        $reservationId = $request->input('reservation_id');
        if ($reservationId) {
            $reservation = Reservation::find($reservationId);
            if ($reservation) {
                $reservation->update(['status' => 'failed']);
                return redirect()->route('rooms.show', $reservation->room_id)->with('error', 'You cancelled the Razorpay payment.');
            }
        }
        return redirect()->route('home')->with('error', 'Payment cancelled.');
    }
}
