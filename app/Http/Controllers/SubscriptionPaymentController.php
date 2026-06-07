<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class SubscriptionPaymentController extends Controller
{
    private function createPendingSubscription(SubscriptionPlan $plan, $paymentType)
    {
        return UserSubscription::create([
            'user_id' => auth()->id() ?? 1, // fallback for testing if needed
            'subscription_plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'price' => $plan->price,
            'currency' => $plan->currency,
            'duration_days' => $plan->duration_days,
            'hosting_allowed' => $plan->hosting_allowed,
            'cancellations_allowed' => $plan->cancellations_allowed,
            'cancellation_fee_reduction' => $plan->cancellation_fee_reduction,
            'payment_type' => $paymentType,
            'status' => 'pending'
        ]);
    }

    private function activateSubscription(UserSubscription $sub, $transactionId)
    {
        // Check if there's an already active subscription, depending on logic we either 
        // cancel the old one or stack. We will just activate this one.
        
        $sub->update([
            'status' => 'active',
            'transaction_id' => $transactionId,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays($sub->duration_days)
        ]);

        // Mark previous active subscriptions for this user as expired
        UserSubscription::where('user_id', $sub->user_id)
            ->where('id', '!=', $sub->id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);
    }

    // ─────────────────────────────────────────────────────────────────
    // STRIPE
    // ─────────────────────────────────────────────────────────────────
    public function initiateStripe(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:subscription_plans,id']);
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        $sub = $this->createPendingSubscription($plan, 'Stripe');

        $stripeSecret = SiteSetting::get('stripe_secret');
        if (!$stripeSecret) return redirect()->back()->with('error', 'Stripe is not configured.');

        Stripe::setApiKey($stripeSecret);

        try {
            $checkout_session = StripeSession::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($plan->currency),
                        'product_data' => [
                            'name' => "Subscription: " . $plan->name,
                        ],
                        'unit_amount' => (int) round($plan->price * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.subscription.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.subscription.stripe.cancel', ['sub_id' => $sub->id]),
                'client_reference_id' => $sub->id,
            ]);

            return redirect($checkout_session->url);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Stripe Error: ' . $e->getMessage());
        }
    }

    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) return redirect()->route('subscriptions.index')->with('error', 'Invalid payment session.');

        Stripe::setApiKey(SiteSetting::get('stripe_secret'));

        try {
            $session = StripeSession::retrieve($sessionId);
            $sub = UserSubscription::findOrFail($session->client_reference_id);

            if ($session->payment_status === 'paid') {
                $this->activateSubscription($sub, $session->payment_intent);
                return redirect()->route('subscriptions.index')->with('success', "Successfully subscribed to {$sub->plan_name}!");
            } else {
                $sub->update(['status' => 'failed']);
                return redirect()->route('subscriptions.index')->with('error', 'Payment was not successful.');
            }
        } catch (\Exception $e) {
            return redirect()->route('subscriptions.index')->with('error', 'Stripe Verification Error: ' . $e->getMessage());
        }
    }

    public function stripeCancel(Request $request)
    {
        $sub = UserSubscription::findOrFail($request->sub_id);
        $sub->update(['status' => 'failed']);
        return redirect()->route('subscriptions.index')->with('error', 'You cancelled the Stripe payment.');
    }

    // ─────────────────────────────────────────────────────────────────
    // PAYPAL
    // ─────────────────────────────────────────────────────────────────
    private function getPaypalBaseUrl()
    {
        $mode = SiteSetting::get('paypal_mode', 'sandbox');
        return $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
    }

    private function getPaypalAccessToken()
    {
        $response = Http::withBasicAuth(SiteSetting::get('paypal_client_id'), SiteSetting::get('paypal_secret'))
            ->asForm()->post($this->getPaypalBaseUrl() . "/v1/oauth2/token", ['grant_type' => 'client_credentials']);
        return $response->successful() ? $response->json()['access_token'] : null;
    }

    public function initiatePaypal(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:subscription_plans,id']);
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        $sub = $this->createPendingSubscription($plan, 'PayPal');

        $token = $this->getPaypalAccessToken();
        if (!$token) return redirect()->back()->with('error', 'PayPal is not configured correctly.');

        $currency = $plan->currency;
        $paypalAmount = $plan->price;

        if (strtoupper($currency) === 'INR') {
            $currency = 'USD';
            $paypalAmount = $plan->price / 83.0; // Approximation for sandbox
        }

        $response = Http::withToken($token)->post($this->getPaypalBaseUrl() . '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => 'SUB_' . $sub->id,
                'amount' => [
                    'currency_code' => $currency,
                    'value' => number_format($paypalAmount, 2, '.', '')
                ],
                'description' => "Subscription: " . $plan->name
            ]],
            'application_context' => [
                'return_url' => route('payment.subscription.paypal.success', ['sub_id' => $sub->id]),
                'cancel_url' => route('payment.subscription.paypal.cancel', ['sub_id' => $sub->id]),
                'user_action' => 'PAY_NOW'
            ]
        ]);

        if ($response->successful()) {
            foreach ($response->json()['links'] as $link) {
                if ($link['rel'] === 'approve') return redirect($link['href']);
            }
        }
        return redirect()->back()->with('error', 'PayPal API Error.');
    }

    public function paypalSuccess(Request $request)
    {
        $sub = UserSubscription::findOrFail($request->sub_id);
        
        $token = $request->query('token');
        if (!$token) {
            $sub->update(['status' => 'failed']);
            return redirect()->route('subscriptions.index')->with('error', 'Payment failed.');
        }

        $response = Http::withToken($this->getPaypalAccessToken())
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($this->getPaypalBaseUrl() . "/v2/checkout/orders/{$token}/capture", []);

        if ($response->successful() && ($response->json()['status'] ?? '') === 'COMPLETED') {
            $captureId = $response->json()['purchase_units'][0]['payments']['captures'][0]['id'] ?? '';
            $this->activateSubscription($sub, $captureId);
            return redirect()->route('subscriptions.index')->with('success', "Successfully subscribed to {$sub->plan_name} via PayPal!");
        }

        $sub->update(['status' => 'failed']);
        return redirect()->route('subscriptions.index')->with('error', 'Payment capture failed.');
    }

    public function paypalCancel(Request $request)
    {
        $sub = UserSubscription::findOrFail($request->sub_id);
        $sub->update(['status' => 'failed']);
        return redirect()->route('subscriptions.index')->with('error', 'You cancelled the PayPal payment.');
    }

    // ─────────────────────────────────────────────────────────────────
    // RAZORPAY
    // ─────────────────────────────────────────────────────────────────
    public function initiateRazorpay(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:subscription_plans,id']);
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        $sub = $this->createPendingSubscription($plan, 'Razorpay');

        $key = SiteSetting::get('razorpay_key');
        $secret = SiteSetting::get('razorpay_secret');
        if (!$key || !$secret) return redirect()->back()->with('error', 'Razorpay not configured.');

        $amountInPaise = intval(round($plan->price * 100));
        
        $response = Http::withBasicAuth($key, $secret)
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'receipt' => 'SUB_' . $sub->id
            ]);

        if ($response->successful()) {
            return view('subscriptions.razorpay_checkout', [
                'order_id' => $response->json()['id'],
                'amount' => $amountInPaise,
                'key' => $key,
                'sub_id' => $sub->id,
                'plan' => $plan
            ]);
        }

        return redirect()->back()->with('error', 'Razorpay API Error.');
    }

    public function razorpaySuccess(Request $request)
    {
        $sub = UserSubscription::findOrFail($request->input('sub_id'));
        $payment_id = $request->input('razorpay_payment_id');
        $order_id = $request->input('razorpay_order_id');
        
        $signature = hash_hmac('sha256', $order_id . "|" . $payment_id, SiteSetting::get('razorpay_secret'));

        if ($signature === $request->input('razorpay_signature')) {
            $this->activateSubscription($sub, $payment_id);
            return redirect()->route('subscriptions.index')->with('success', "Subscribed via Razorpay!");
        }

        $sub->update(['status' => 'failed']);
        return redirect()->route('subscriptions.index')->with('error', 'Razorpay Verification failed.');
    }

    public function razorpayCancel(Request $request)
    {
        $sub = UserSubscription::findOrFail($request->input('sub_id'));
        $sub->update(['status' => 'failed']);
        return redirect()->route('subscriptions.index')->with('error', 'Cancelled Razorpay payment.');
    }

    // ─────────────────────────────────────────────────────────────────
    // EASEBUZZ
    // ─────────────────────────────────────────────────────────────────
    public function initiateEasebuzz(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:subscription_plans,id']);
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        $sub = $this->createPendingSubscription($plan, 'Easebuzz');

        $key = SiteSetting::get('easebuzz_merchant_key');
        $salt = SiteSetting::get('easebuzz_salt');
        if (!$key || !$salt) return redirect()->back()->with('error', 'Easebuzz not configured.');

        $txnid = 'SUB_' . $sub->id . '_' . time();
        $amount = number_format($plan->price, 2, '.', '');
        $productinfo = "Subscription: " . $plan->name;
        $firstname = auth()->user()->name ?? 'Guest';
        $email = auth()->user()->email ?? 'guest@example.com';
        $phone = auth()->user()->phone ?? '9999999999';
        
        $hashString = "{$key}|{$txnid}|{$amount}|{$productinfo}|{$firstname}|{$email}|{$sub->id}||||||||||{$salt}";
        $hash = hash('sha512', $hashString);

        $env = SiteSetting::get('easebuzz_env', 'sandbox');
        $baseUrl = $env === 'live' ? 'https://pay.easebuzz.in' : 'https://testpay.easebuzz.in';

        $response = Http::asForm()->post("{$baseUrl}/payment/initiateLink", [
            'key' => $key, 'txnid' => $txnid, 'amount' => $amount, 'productinfo' => $productinfo,
            'firstname' => $firstname, 'phone' => $phone, 'email' => $email,
            'surl' => route('payment.subscription.easebuzz.success'),
            'furl' => route('payment.subscription.easebuzz.cancel'),
            'hash' => $hash, 'udf1' => $sub->id
        ]);

        if ($response->successful() && isset($response->json()['data'])) {
            return redirect("{$baseUrl}/pay/v1/ui/#/" . $response->json()['data'] . "/");
        }

        return redirect()->back()->with('error', 'Easebuzz API Error.');
    }

    public function easebuzzSuccess(Request $request)
    {
        $sub = UserSubscription::findOrFail($request->input('udf1'));

        $salt = SiteSetting::get('easebuzz_salt');
        $reverseHashString = "{$salt}|{$request->input('status')}||||||||||{$sub->id}|{$request->input('email')}|{$request->input('firstname')}|{$request->input('productinfo')}|{$request->input('amount')}|{$request->input('txnid')}|" . SiteSetting::get('easebuzz_merchant_key');
        
        if (hash('sha512', $reverseHashString) === $request->input('hash') && $request->input('status') === 'success') {
            $this->activateSubscription($sub, $request->input('easepayid'));
            return redirect()->route('subscriptions.index')->with('success', "Subscribed via Easebuzz!");
        }

        $sub->update(['status' => 'failed']);
        return redirect()->route('subscriptions.index')->with('error', 'Easebuzz payment failed.');
    }

    public function easebuzzCancel(Request $request)
    {
        $sub = UserSubscription::findOrFail($request->input('udf1'));
        $sub->update(['status' => 'failed']);
        return redirect()->route('subscriptions.index')->with('error', 'Cancelled Easebuzz payment.');
    }
}
