<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserProfileController extends Controller
{
    private function authUser(): User
    {
        $user = User::find(session('user_id'));
        if (!$user) {
            abort(redirect('/auth'));
        }
        return $user;
    }

    public function show()
    {
        $user      = $this->authUser();
        $countries = Country::orderBy('country_name')->get();
        return view('profile.index', compact('user', 'countries'));
    }

    public function update(Request $request)
    {
        $user = $this->authUser();

        $validated = $request->validate([
            'name'          => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'email'         => ['required', 'email:rfc', 'max:255', 'unique:users,email,' . $user->id],
            'phone'         => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
            'date_of_birth' => ['required', 'date', 'before:' . now()->subYears(18)->toDateString()],
            'gender'        => ['required', 'in:male,female,other'],
            'country_id'    => ['required', 'integer', 'exists:countries,id'],
        ]);

        $country = Country::findOrFail($validated['country_id']);

        $user->update([
            'name'          => trim($validated['name']),
            'email'         => strtolower(trim($validated['email'])),
            'phone'         => trim($validated['phone']),
            'date_of_birth' => $validated['date_of_birth'],
            'gender'        => $validated['gender'],
            'country'       => $country->short_name,
            'country_id'    => $country->id,
        ]);

        session()->put('user', (object) $user->fresh()->only([
            'id','name','email','phone','role','profile_image','gender','country','country_id','date_of_birth'
        ]));

        return response()->json(['success' => true, 'message' => 'Profile updated successfully.']);
    }

    public function updatePassword(Request $request)
    {
        $user = $this->authUser();

        // Verify reCAPTCHA if enabled
        $recaptchaEnabled = \App\Models\SiteSetting::get('recaptcha_enabled', '0') === '1';
        if ($recaptchaEnabled) {
            $secret   = \App\Models\SiteSetting::get('recaptcha_secret_key', '');
            $token    = $request->input('recaptcha_token', '');
            if (!empty($secret) && !empty($token)) {
                $resp = \Illuminate\Support\Facades\Http::asForm()->post(
                    'https://www.google.com/recaptcha/api/siteverify',
                    ['secret' => $secret, 'response' => $token]
                );
                if (!$resp->json('success', false)) {
                    return response()->json(['success' => false, 'message' => 'reCAPTCHA verification failed. Please try again.'], 422);
                }
            }
        }

        $request->validate([
            'password'              => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'password_confirmation' => ['required', 'same:password'],
        ], [
            'password_confirmation.same' => 'Passwords do not match.',
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        // Send success email
        try {
            dispatch(function () use ($user) {
                \App\Http\Controllers\EmailController::sendResetSuccess($user);
            })->afterResponse();
        } catch (\Exception $e) {
            \Log::error('Reset success email: ' . $e->getMessage());
        }

        // Logout user
        session()->forget(['user_id', 'user']);

        return response()->json([
            'success'  => true,
            'message'  => 'Password reset successfully.',
            'redirect' => route('auth') . '?toast=Password+reset+successfully.+Login+now',
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $user = $this->authUser();

        $request->validate([
            'profile_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $file     = $request->file('profile_image');
        $filename = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images'), $filename);

        $user->update(['profile_image' => 'images/' . $filename]);

        session()->put('user', (object) $user->fresh()->only([
            'id','name','email','phone','role','profile_image','gender','country','country_id','date_of_birth'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profile photo updated.',
            'url'     => asset('images/' . $filename),
        ]);
    }
}
