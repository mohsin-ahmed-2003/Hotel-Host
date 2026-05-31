<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class SocialLoginController extends Controller
{
    // ── Redirect to provider ──────────────────────────────────────────────────

    public function redirect(string $provider)
    {
        $this->abortIfDisabled($provider);

        $clientId    = SiteSetting::get("{$provider}_client_id");
        $redirectUri = route('social.callback', $provider);

        $url = match ($provider) {
            'google'   => $this->googleAuthUrl($clientId, $redirectUri),
            'facebook' => $this->facebookAuthUrl($clientId, $redirectUri),
            'apple'    => $this->appleAuthUrl($clientId, $redirectUri),
            default    => abort(404),
        };

        return redirect($url);
    }

    // ── Handle callback ───────────────────────────────────────────────────────

    public function callback(Request $request, string $provider)
    {
        $this->abortIfDisabled($provider);

        if ($request->has('error') || !$request->has('code')) {
            return redirect()->route('auth')->withErrors(['social' => 'Social login was cancelled or failed.']);
        }

        try {
            $socialUser = match ($provider) {
                'google'   => $this->fetchGoogleUser($request->code, route('social.callback', $provider)),
                'facebook' => $this->fetchFacebookUser($request->code, route('social.callback', $provider)),
                'apple'    => $this->fetchAppleUser($request, route('social.callback', $provider)),
                default    => abort(404),
            };
        } catch (\Exception $e) {
            \Log::error("Social login [{$provider}] error: " . $e->getMessage());
            return redirect()->route('auth')->withErrors(['social' => 'Social login failed. Please try again.']);
        }

        if (empty($socialUser['email'])) {
            return redirect()->route('auth')->withErrors(['social' => 'Could not retrieve email from ' . ucfirst($provider) . '. Please use email/password login.']);
        }

        // Find or create user
        $user = User::where('email', strtolower($socialUser['email']))->first();

        if ($user) {
            // Check active status
            if (!$user->is_active) {
                return redirect()->route('inactive', ['name' => $user->name]);
            }
            // Update social info if not already set
            if (!$user->social_id) {
                $user->update([
                    'social_id'       => $socialUser['id'],
                    'social_provider' => $provider,
                    'login_type'      => $provider,
                ]);
            }
        } else {
            // Create new user
            $user = User::create([
                'name'               => $socialUser['name'] ?? explode('@', $socialUser['email'])[0],
                'email'              => strtolower($socialUser['email']),
                'phone'              => null,
                'password'           => Hash::make(Str::random(32)),
                'date_of_birth'      => null,
                'gender'             => null,
                'country'            => null,
                'country_id'         => null,
                'role'               => 'user',
                'profile_image'      => $socialUser['avatar'] ?? 'images/image.png',
                'email_verified'     => true,
                'email_verified_at'  => now(),
                'is_active'          => true,
                'login_type'         => $provider,
                'social_id'          => $socialUser['id'],
                'social_provider'    => $provider,
                'email_verify_token' => Str::random(64),
            ]);

            // Send welcome email async
            try {
                dispatch(function () use ($user) {
                    EmailController::sendWelcome($user);
                })->afterResponse();
            } catch (\Exception $e) {}
        }

        // Store session
        session()->put('user_id', $user->id);
        session()->put('user', (object) $user->only([
            'id','name','email','phone','role','profile_image',
            'gender','country','country_id','date_of_birth',
            'email_verified','phone_verified','is_active','login_type',
        ]));

        return redirect('/')->with('success', 'Welcome, ' . $user->name . '!');
    }

    // ── Google ────────────────────────────────────────────────────────────────

    private function googleAuthUrl(string $clientId, string $redirectUri): string
    {
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'online',
            'prompt'        => 'select_account',
        ]);
    }

    private function fetchGoogleUser(string $code, string $redirectUri): array
    {
        $tokenRes = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code'          => $code,
            'client_id'     => SiteSetting::get('google_client_id'),
            'client_secret' => SiteSetting::get('google_client_secret'),
            'redirect_uri'  => $redirectUri,
            'grant_type'    => 'authorization_code',
        ])->throw()->json();

        $userRes = Http::withToken($tokenRes['access_token'])
            ->get('https://www.googleapis.com/oauth2/v3/userinfo')
            ->throw()->json();

        return [
            'id'     => $userRes['sub'],
            'name'   => $userRes['name'] ?? null,
            'email'  => $userRes['email'] ?? null,
            'avatar' => $userRes['picture'] ?? null,
        ];
    }

    // ── Facebook ──────────────────────────────────────────────────────────────

    private function facebookAuthUrl(string $clientId, string $redirectUri): string
    {
        return 'https://www.facebook.com/v19.0/dialog/oauth?' . http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'email,public_profile',
        ]);
    }

    private function fetchFacebookUser(string $code, string $redirectUri): array
    {
        $tokenRes = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
            'client_id'     => SiteSetting::get('facebook_client_id'),
            'client_secret' => SiteSetting::get('facebook_client_secret'),
            'redirect_uri'  => $redirectUri,
            'code'          => $code,
        ])->throw()->json();

        $userRes = Http::withToken($tokenRes['access_token'])
            ->get('https://graph.facebook.com/me', ['fields' => 'id,name,email,picture'])
            ->throw()->json();

        return [
            'id'     => $userRes['id'],
            'name'   => $userRes['name'] ?? null,
            'email'  => $userRes['email'] ?? null,
            'avatar' => $userRes['picture']['data']['url'] ?? null,
        ];
    }

    // ── Apple ─────────────────────────────────────────────────────────────────

    private function appleAuthUrl(string $clientId, string $redirectUri): string
    {
        return 'https://appleid.apple.com/auth/authorize?' . http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'name email',
            'response_mode' => 'form_post',
        ]);
    }

    private function fetchAppleUser(Request $request, string $redirectUri): array
    {
        $tokenRes = Http::asForm()->post('https://appleid.apple.com/auth/token', [
            'client_id'     => SiteSetting::get('apple_client_id'),
            'client_secret' => SiteSetting::get('apple_client_secret'),
            'code'          => $request->code,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $redirectUri,
        ])->throw()->json();

        // Decode JWT id_token (no signature verification needed for basic claims)
        $parts   = explode('.', $tokenRes['id_token']);
        $payload = json_decode(base64_decode(str_pad($parts[1], strlen($parts[1]) + (4 - strlen($parts[1]) % 4) % 4, '=')), true);

        // Apple sends name only on first login via POST user field
        $nameData = $request->has('user') ? json_decode($request->user, true) : [];
        $name     = trim(($nameData['name']['firstName'] ?? '') . ' ' . ($nameData['name']['lastName'] ?? ''));

        return [
            'id'     => $payload['sub'],
            'name'   => $name ?: null,
            'email'  => $payload['email'] ?? null,
            'avatar' => null,
        ];
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function abortIfDisabled(string $provider): void
    {
        $enabled = SiteSetting::get("{$provider}_login_enabled", '0');
        if ($enabled !== '1') abort(404);
    }
}
