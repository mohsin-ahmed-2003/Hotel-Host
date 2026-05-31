<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PendingRegistration;
use App\Models\Country;
use App\Models\SiteSetting;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PhoneVerificationController extends Controller
{
    // Show verify page (after signup redirect)
    public function show(string $token)
    {
        $pending = PendingRegistration::where('token', $token)->first();

        if (!$pending || $pending->isExpired()) {
            return redirect()->route('auth')->with('error', 'Registration session expired. Please sign up again.');
        }

        $countries = Country::orderBy('country_name')->get(['id', 'country_name', 'phone_code', 'short_name']);

        return view('auth.verify_phone', compact('pending', 'countries'));
    }

    // Send OTP
    public function sendOtp(Request $request, string $token)
    {
        $pending = PendingRegistration::where('token', $token)->first();

        if (!$pending || $pending->isExpired()) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please sign up again.'], 422);
        }

        if (!$pending->canResend()) {
            $wait = $pending->secondsUntilResend();
            return response()->json(['success' => false, 'message' => "Please wait {$wait} seconds before resending."], 429);
        }

        // Update phone if changed
        if ($request->filled('phone')) {
            $request->validate([
                'phone'      => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
                'country_id' => ['required', 'integer', 'exists:countries,id'],
            ]);

            $country = Country::findOrFail($request->country_id);
            $data    = $pending->data;
            $data['phone']      = trim($request->phone);
            $data['country']    = $country->short_name;
            $data['country_id'] = $country->id;

            $pending->update([
                'phone' => trim($request->phone),
                'data'  => $data,
            ]);
        }

        $phone = $pending->phone;

        // If Twilio disabled — use fallback 6-digit code stored in DB
        if (!TwilioService::isEnabled()) {
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $pending->update(['otp' => $otp, 'otp_sent_at' => now()]);
            // In dev: log the OTP
            \Log::info("DEV OTP for {$phone}: {$otp}");
            return response()->json(['success' => true, 'message' => 'OTP sent successfully.', 'resend_after' => 30, 'dev_otp' => config('app.debug') ? $otp : null]);
        }

        $result = TwilioService::sendOtp($phone, $pending->data['country_id'] ?? null);

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        $pending->update(['otp_sent_at' => now()]);

        return response()->json(['success' => true, 'message' => 'OTP sent to ' . $phone, 'resend_after' => 30]);
    }

    // Verify OTP and complete registration
    public function verifyOtp(Request $request, string $token)
    {
        $request->validate(['otp' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/']]);

        $pending = PendingRegistration::where('token', $token)->first();

        if (!$pending || $pending->isExpired()) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please sign up again.'], 422);
        }

        $phone = $pending->phone;
        $otp   = $request->otp;

        // Verify OTP
        if (TwilioService::isEnabled()) {
            $result = TwilioService::verifyOtp($phone, $otp, $pending->data['country_id'] ?? null);
            if (!$result['success']) {
                return response()->json(['success' => false, 'message' => $result['message']], 422);
            }
        } else {
            // Fallback: check stored OTP
            if ($pending->otp !== $otp) {
                return response()->json(['success' => false, 'message' => 'Invalid OTP code.'], 422);
            }
        }

        // Check phone not already taken
        if (User::where('phone', $phone)->exists()) {
            return response()->json(['success' => false, 'message' => 'This phone number is already registered.'], 422);
        }

        // Create user
        $data = $pending->data;
        $user = User::create([
            'name'               => $data['name'],
            'email'              => $data['email'],
            'phone'              => $phone,
            'password'           => $data['password'],
            'date_of_birth'      => $data['date_of_birth'],
            'gender'             => $data['gender'],
            'country'            => $data['country'],
            'country_id'         => $data['country_id'],
            'role'               => 'user',
            'profile_image'      => 'images/image.png',
            'email_verify_token' => Str::random(64),
            'phone_verified'     => true,
            'phone_verified_at'  => now(),
        ]);

        // Delete pending record
        $pending->delete();

        // Set session
        session()->put('user_id', $user->id);
        session()->put('user', (object) $user->only([
            'id','name','email','phone','role','profile_image',
            'gender','country','country_id','date_of_birth',
            'email_verified','phone_verified'
        ]));

        // Send welcome email
        try {
            dispatch(function () use ($user) {
                \App\Http\Controllers\EmailController::sendWelcome($user);
            })->afterResponse();
        } catch (\Exception $e) {
            \Log::error('Welcome email: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'redirect' => route('homepage')]);
    }

    // Profile: send OTP to verify phone
    public function sendProfileOtp(Request $request)
    {
        $user = User::find(session('user_id'));
        if (!$user) return response()->json(['success' => false, 'message' => 'Not authenticated.'], 401);

        $request->validate([
            'phone' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
        ]);

        $phone = trim($request->phone);

        // Throttle: 30s between sends
        $sentAt = session('profile_otp_sent_at');
        if ($sentAt) {
            $elapsed = now()->timestamp - $sentAt;
            if ($elapsed < 30) {
                $wait = 30 - $elapsed;
                return response()->json(['success' => false, 'message' => "Please wait {$wait} seconds before resending."], 429);
            }
        }

        if (!TwilioService::isEnabled()) {
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            session()->put('profile_otp', $otp);
            session()->put('profile_otp_phone', $phone);
            session()->put('profile_otp_sent_at', now()->timestamp);
            \Log::info("DEV Profile OTP for {$phone}: {$otp}");
            return response()->json(['success' => true, 'message' => 'OTP sent.', 'resend_after' => 30, 'dev_otp' => config('app.debug') ? $otp : null]);
        }

        $result = TwilioService::sendOtp($phone, $user->country_id);
        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        session()->put('profile_otp_phone', $phone);
        session()->put('profile_otp_sent_at', now()->timestamp);

        return response()->json(['success' => true, 'message' => 'OTP sent to ' . $phone, 'resend_after' => 30]);
    }

    // Profile: verify OTP
    public function verifyProfileOtp(Request $request)
    {
        $user = User::find(session('user_id'));
        if (!$user) return response()->json(['success' => false, 'message' => 'Not authenticated.'], 401);

        $request->validate(['otp' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/']]);

        $phone = session('profile_otp_phone');
        if (!$phone) return response()->json(['success' => false, 'message' => 'No OTP session found.'], 422);

        if (TwilioService::isEnabled()) {
            $result = TwilioService::verifyOtp($phone, $request->otp, $user->country_id);
            if (!$result['success']) {
                return response()->json(['success' => false, 'message' => $result['message']], 422);
            }
        } else {
            if (session('profile_otp') !== $request->otp) {
                return response()->json(['success' => false, 'message' => 'Invalid OTP code.'], 422);
            }
        }

        $user->update(['phone_verified' => true, 'phone_verified_at' => now()]);
        session()->forget(['profile_otp', 'profile_otp_phone', 'profile_otp_sent_at']);

        // Refresh session
        session()->put('user', (object) $user->fresh()->only([
            'id','name','email','phone','role','profile_image',
            'gender','country','country_id','date_of_birth',
            'email_verified','phone_verified'
        ]));

        return response()->json(['success' => true, 'message' => 'Phone number verified successfully!']);
    }
}
