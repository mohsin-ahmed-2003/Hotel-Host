<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'admin_login|' . Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many attempts. Try again in {$seconds} seconds."
            ])->withInput($request->only('email'));
        }

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput($request->only('email'));
        }

        if (!in_array($user->role, ['admin', 'sub_admin'])) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['email' => 'You do not have admin access.'])->withInput($request->only('email'));
        }

        RateLimiter::clear($throttleKey);

        session()->put('admin_id', $user->id);
        session()->put('admin_user', (object) $user->only([
            'id', 'name', 'email', 'phone', 'role', 'profile_image',
            'gender', 'country', 'country_id', 'date_of_birth', 'permissions'
        ]));

        return redirect()->route('admin.dashboard');
    }

    public function logout()
    {
        session()->forget('admin_id');
        session()->forget('admin_user');
        return redirect()->route('admin.login')->with('success', 'Logged out from admin panel.');
    }
}
