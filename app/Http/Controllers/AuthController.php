<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use App\Models\PendingRegistration;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showAuthPage()
    {
        $countries = Country::orderBy('country_name', 'asc')
            ->get(['id', 'country_name', 'short_name', 'phone_code', 'currency'])
            ->toArray();

        return view('auth.login', compact('countries'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'email'            => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone'            => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/', 'unique:users,phone'],
            'password'         => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'password_confirm' => ['required', 'same:password'],
            'date_of_birth'    => ['required', 'date', 'before:' . now()->subYears(18)->toDateString()],
            'gender'           => ['required', 'in:male,female,other'],
            'country_id'       => ['required', 'integer', 'exists:countries,id'],
            'terms'            => ['required', 'accepted'],
        ], [
            'name.regex'            => 'Name may only contain letters, spaces, and hyphens.',
            'phone.regex'           => 'Please enter a valid phone number.',
            'date_of_birth.before'  => 'You must be at least 18 years old to register.',
            'country_id.exists'     => 'Please select a valid country.',
            'terms.accepted'        => 'You must accept the Terms & Conditions and Privacy Policy.',
            'password_confirm.same' => 'Passwords do not match.',
        ]);

        $country = Country::findOrFail($validated['country_id']);

        // If Twilio is enabled — store pending and redirect to phone verify
        if (TwilioService::isEnabled()) {
            $token = Str::random(64);

            PendingRegistration::create([
                'token'      => $token,
                'phone'      => trim($validated['phone']),
                'expires_at' => now()->addMinutes(30),
                'data'       => [
                    'name'          => trim($validated['name']),
                    'email'         => strtolower(trim($validated['email'])),
                    'phone'         => trim($validated['phone']),
                    'password'      => Hash::make($validated['password']),
                    'date_of_birth' => $validated['date_of_birth'],
                    'gender'        => $validated['gender'],
                    'country'       => $country->short_name,
                    'country_id'    => $country->id,
                ],
            ]);

            return redirect()->route('phone.verify', $token);
        }

        // Twilio disabled — create user directly
        $user = User::create([
            'name'               => trim($validated['name']),
            'email'              => strtolower(trim($validated['email'])),
            'phone'              => trim($validated['phone']),
            'password'           => Hash::make($validated['password']),
            'date_of_birth'      => $validated['date_of_birth'],
            'gender'             => $validated['gender'],
            'country'            => $country->short_name,
            'country_id'         => $country->id,
            'role'               => 'user',
            'profile_image'      => 'images/image.png',
            'email_verify_token' => Str::random(64),
        ]);

        \Illuminate\Support\Facades\Auth::login($user);
        session()->put('user_id', $user->id);
        session()->put('user', (object) $user->only([
            'id','name','email','phone','role','profile_image',
            'gender','country','country_id','date_of_birth',
            'email_verified','phone_verified'
        ]));

        try {
            dispatch(function () use ($user) {
                EmailController::sendWelcome($user);
            })->afterResponse();
        } catch (\Exception $e) {
            \Log::error('Welcome email failed: ' . $e->getMessage());
        }

        return redirect('/')->with('success', 'Welcome, ' . $user->name . '! Your account has been created.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email_phone' => ['required', 'string', 'max:255'],
            'password'    => ['required', 'string'],
        ]);

        $throttleKey = Str::lower($request->input('email_phone')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withErrors(['email_phone' => "Too many login attempts. Please try again in {$seconds} seconds."])
                ->withInput($request->only('email_phone'));
        }

        $input = trim($request->input('email_phone'));

        $user = User::where('email', strtolower($input))
                    ->orWhere('phone', $input)
                    ->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            return back()
                ->withErrors(['email_phone' => 'Invalid email/phone or password.'])
                ->withInput($request->only('email_phone'));
        }

        if (!$user->is_active) {
            return redirect()->route('inactive', ['name' => $user->name]);
        }

        RateLimiter::clear($throttleKey);

        \Illuminate\Support\Facades\Auth::login($user);
        session()->put('user_id', $user->id);
        session()->put('user', (object) $user->only([
            'id','name','email','phone','role','profile_image',
            'gender','country','country_id','date_of_birth',
            'email_verified','phone_verified'
        ]));

        return redirect('/')->with('success', 'Welcome back, ' . $user->name . '!');
    }

    public function logout()
    {
        \Illuminate\Support\Facades\Auth::logout();
        session()->forget(['user_id', 'user']);
        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
