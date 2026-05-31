<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordController extends Controller
{
    private function verifyRecaptcha(string $token): bool
    {
        if (SiteSetting::get('recaptcha_enabled', '0') !== '1') return true;

        $secret = SiteSetting::get('recaptcha_secret_key', '');
        if (empty($secret)) return true;

        $response = \Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $secret,
            'response' => $token,
        ]);

        return $response->json('success', false);
    }

    public function show()
    {
        return view('auth.forgot_password');
    }

    public function sendCode(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if ($user) {
            $code    = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expires = now()->addMinutes(15);
            $user->update(['reset_code' => $code, 'reset_code_expires_at' => $expires]);

            try {
                dispatch(function () use ($user, $code) {
                    EmailController::sendResetCode($user, $code);
                })->afterResponse();
            } catch (\Exception $e) {
                \Log::error('Reset code email failed: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => 'If this email exists, a reset code has been sent.']);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code'  => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user || $user->reset_code !== $request->code) {
            return response()->json(['success' => false, 'message' => 'Invalid email or code.'], 422);
        }

        if (!$user->reset_code_expires_at || now()->isAfter($user->reset_code_expires_at)) {
            return response()->json(['success' => false, 'message' => 'Reset code has expired. Please request a new one.'], 422);
        }

        return response()->json(['success' => true]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => ['required', 'email'],
            'code'                  => ['required', 'string', 'size:6'],
            'password'              => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'password_confirmation' => ['required', 'same:password'],
        ], ['password_confirmation.same' => 'Passwords do not match.']);

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user || $user->reset_code !== $request->code) {
            return response()->json(['success' => false, 'message' => 'Invalid request.'], 422);
        }

        if (!$user->reset_code_expires_at || now()->isAfter($user->reset_code_expires_at)) {
            return response()->json(['success' => false, 'message' => 'Reset code has expired.'], 422);
        }

        $user->update([
            'password'              => Hash::make($request->password),
            'reset_code'            => null,
            'reset_code_expires_at' => null,
        ]);

        // Send success email
        try {
            dispatch(function () use ($user) {
                EmailController::sendResetSuccess($user);
            })->afterResponse();
        } catch (\Exception $e) {
            \Log::error('Reset success email failed: ' . $e->getMessage());
        }

        // Logout user if logged in
        session()->forget(['user_id', 'user']);

        return response()->json(['success' => true, 'message' => 'Password reset successfully.']);
    }
}
