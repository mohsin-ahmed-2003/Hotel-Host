<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SyncAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Automatically sync the custom session login to Laravel's native Auth
        if (session()->has('user_id') && !Auth::check()) {
            Auth::loginUsingId(session('user_id'));
        }

        return $next($request);
    }
}
