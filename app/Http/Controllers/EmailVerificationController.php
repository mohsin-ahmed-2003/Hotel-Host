<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmailVerificationController extends Controller
{
    // Send verification email (AJAX)
    public function send(Request $request)
    {
        $user = User::find(session('user_id'));
        if (!$user) return response()->json(['success' => false, 'message' => 'Not authenticated.'], 401);

        if ($user->email_verified) {
            return response()->json(['success' => false, 'message' => 'Email already verified.']);
        }

        $token = Str::random(64);
        $user->update(['email_verify_token' => $token]);

        try {
            dispatch(function () use ($user) {
                EmailController::sendVerification($user);
            })->afterResponse();
        } catch (\Exception $e) {
            \Log::error('Verification email failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Verification email sent! Check your inbox.']);
    }

    // Handle verification link click
    public function verify(string $token)
    {
        $user = User::where('email_verify_token', $token)->first();

        if (!$user) {
            return redirect('/auth?toast=Invalid+or+expired+verification+link.');
        }

        if ($user->email_verified) {
            // Already verified — just log in and redirect
            $this->loginUser($user);
            return redirect('/profile?toast=Email+already+verified.');
        }

        $user->update([
            'email_verified'      => true,
            'email_verified_at'   => now(),
            'email_verify_token'  => null,
        ]);

        // Send thank you email
        try {
            dispatch(function () use ($user) {
                EmailController::sendVerificationSuccess($user);
            })->afterResponse();
        } catch (\Exception $e) {
            \Log::error('Verification success email failed: ' . $e->getMessage());
        }

        $this->loginUser($user);
        return redirect('/profile?toast=Email+verified+successfully!+Welcome+to+the+verified+community.');
    }

    private function loginUser(User $user): void
    {
        \Illuminate\Support\Facades\Auth::login($user);
        session()->put('user_id', $user->id);
        session()->put('user', (object) $user->only([
            'id','name','email','phone','role','profile_image',
            'gender','country','country_id','date_of_birth','email_verified'
        ]));
    }
}
