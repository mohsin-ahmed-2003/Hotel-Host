<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminId = session('admin_id');

        if (!$adminId) {
            return redirect()->route('admin.login')->with('error', 'Please login to access the admin panel.');
        }

        $user = User::find($adminId);

        if (!$user || !in_array($user->role, ['admin', 'sub_admin'])) {
            session()->forget('admin_id');
            session()->forget('admin_user');
            return redirect()->route('admin.login')->with('error', 'Unauthorized access.');
        }

        // Refresh admin session data
        session()->put('admin_user', (object) $user->only([
            'id', 'name', 'email', 'phone', 'role', 'profile_image',
            'gender', 'country', 'country_id', 'date_of_birth', 'permissions'
        ]));

        return $next($request);
    }
}
