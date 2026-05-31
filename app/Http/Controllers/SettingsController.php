<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::pluck('value', 'key');
        $currencies = Currency::all();
        return view('admin.settings.index', compact('settings', 'currencies'));
    }

    // ── Site Management ──────────────────────────────────────────────────────

    public function updateSite(Request $request)
    {
        // Dynamically adjust execution limits to prevent Gateway Timeout or limit exceedance
        @set_time_limit(600); // 10 minutes maximum execution time
        @ini_set('max_execution_time', 600);
        @ini_set('memory_limit', '512M');

        $request->validate([
            'site_name'     => ['required', 'string', 'max:100'],
            'site_logo'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'site_favicon'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,ico,webp', 'max:512'],
            'default_currency' => ['nullable', 'string', 'max:10'],
            'hero_title'    => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'hero_media_type' => ['nullable', 'in:image,video'],
            'hero_media_file' => [
                'nullable', 
                'file',
                function ($attribute, $value, $fail) use ($request) {
                    $type = $request->input('hero_media_type');
                    $mimes = $type === 'video' ? ['mp4', 'webm', 'mov'] : ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    $maxSize = $type === 'video' ? 102400 : 5120; // 100MB for video, 5MB for image

                    if (!in_array(strtolower($value->getClientOriginalExtension()), $mimes)) {
                        $fail("The hero media must be a file of type: " . implode(', ', $mimes) . ".");
                    }
                    if ($value->getSize() > $maxSize * 1024) {
                        $fail("The hero media may not be greater than {$maxSize} kilobytes.");
                    }
                }
            ],
        ]);

        SiteSetting::set('site_name', trim($request->site_name));
        
        if ($request->filled('default_currency')) {
            SiteSetting::set('default_currency', trim($request->default_currency));
        }

        if ($request->has('hero_title')) {
            SiteSetting::set('hero_title', trim($request->hero_title));
        }

        if ($request->has('hero_subtitle')) {
            SiteSetting::set('hero_subtitle', trim($request->hero_subtitle));
        }

        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('site', 'public');
            SiteSetting::set('site_logo', $path);
        }

        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('site', 'public');
            SiteSetting::set('site_favicon', $path);
        }

        if ($request->filled('hero_media_type')) {
            SiteSetting::set('hero_media_type', $request->hero_media_type);
        }

        if ($request->hasFile('hero_media_file')) {
            $path = $request->file('hero_media_file')->store('site/hero', 'public');
            SiteSetting::set('hero_media_file', $path);
        }

        return back()->with('success', 'Site settings saved successfully.');
    }

    // ── Map Settings ─────────────────────────────────────────────────────────

    public function updateMap(Request $request)
    {
        $request->validate([
            'map_key'    => ['nullable', 'string'],
            'map_radius' => ['nullable', 'integer', 'min:10', 'max:50000'],
        ]);

        if ($request->filled('map_key')) {
            SiteSetting::set('map_key', trim($request->map_key));
        } else {
            SiteSetting::where('key', 'map_key')->delete();
        }

        if ($request->filled('map_radius')) {
            SiteSetting::set('map_radius', (int) trim($request->map_radius));
        } else {
            SiteSetting::where('key', 'map_radius')->delete();
        }

        return back()->with('success', 'Map settings updated successfully.');
    }

    // ── Email ─────────────────────────────────────────────────────────────────

    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_username'   => ['required', 'email'],
            'mail_password'   => ['required', 'string', 'min:4'],
            'mail_from'       => ['required', 'email'],
            'mail_from_name'  => ['required', 'string', 'max:100'],
            'mail_email_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'mail_username.email' => 'Enter a valid Gmail address.',
            'mail_from.email'     => 'Enter a valid From email address.',
        ]);

        SiteSetting::set('mail_username',  trim($request->mail_username));
        SiteSetting::set('mail_password',  $request->mail_password);
        SiteSetting::set('mail_from',      trim($request->mail_from));
        SiteSetting::set('mail_from_name', trim($request->mail_from_name));

        if ($request->hasFile('mail_email_logo')) {
            $path = $request->file('mail_email_logo')->store('site', 'public');
            SiteSetting::set('mail_email_logo', $path);
        }

        return back()->with('success', 'Email settings saved successfully.');
    }

    public function toggleEmail(Request $request)
    {
        $current = SiteSetting::get('mail_enabled', '0');
        SiteSetting::set('mail_enabled', $current === '1' ? '0' : '1');
        return response()->json(['enabled' => SiteSetting::get('mail_enabled') === '1']);
    }

    public function updateRecaptcha(Request $request)
    {
        $request->validate([
            'recaptcha_site_key'   => ['required', 'string'],
            'recaptcha_secret_key' => ['required', 'string'],
        ]);

        SiteSetting::set('recaptcha_site_key',   trim($request->recaptcha_site_key));
        SiteSetting::set('recaptcha_secret_key', trim($request->recaptcha_secret_key));

        return back()->with('success', 'reCAPTCHA settings saved successfully.');
    }

    public function toggleRecaptcha(Request $request)
    {
        $current = SiteSetting::get('recaptcha_enabled', '0');
        SiteSetting::set('recaptcha_enabled', $current === '1' ? '0' : '1');
        return response()->json(['enabled' => SiteSetting::get('recaptcha_enabled') === '1']);
    }

    public function updateTwilio(Request $request)
    {
        $request->validate([
            'twilio_sid'         => ['required', 'string'],
            'twilio_token'       => ['required', 'string'],
            'twilio_service_sid' => ['required', 'string'],
        ]);

        SiteSetting::set('twilio_sid',         trim($request->twilio_sid));
        SiteSetting::set('twilio_token',       trim($request->twilio_token));
        SiteSetting::set('twilio_service_sid', trim($request->twilio_service_sid));

        return back()->with('success', 'Twilio settings saved successfully.');
    }

    public function toggleTwilio(Request $request)
    {
        $current = SiteSetting::get('twilio_enabled', '0');
        SiteSetting::set('twilio_enabled', $current === '1' ? '0' : '1');
        return response()->json(['enabled' => SiteSetting::get('twilio_enabled') === '1']);
    }

    // ── Social Login ──────────────────────────────────────────────────────────

    public function updateSocial(Request $request)
    {
        $request->validate([
            'google_client_id'       => ['nullable', 'string', 'max:255'],
            'google_client_secret'   => ['nullable', 'string', 'max:255'],
            'facebook_client_id'     => ['nullable', 'string', 'max:255'],
            'facebook_client_secret' => ['nullable', 'string', 'max:255'],
            'apple_client_id'        => ['nullable', 'string', 'max:255'],
            'apple_client_secret'    => ['nullable', 'string', 'max:500'],
        ]);

        $keys = [
            'google_client_id', 'google_client_secret',
            'facebook_client_id', 'facebook_client_secret',
            'apple_client_id', 'apple_client_secret',
        ];

        foreach ($keys as $key) {
            if ($request->filled($key)) {
                SiteSetting::set($key, trim($request->input($key)));
            }
        }

        return back()->with('success', 'Social login credentials saved successfully.');
    }

    public function toggleSocial(Request $request, string $provider)
    {
        if (!in_array($provider, ['google', 'facebook', 'apple'])) abort(404);
        $key     = "{$provider}_login_enabled";
        $current = SiteSetting::get($key, '0');
        SiteSetting::set($key, $current === '1' ? '0' : '1');
        return response()->json(['enabled' => SiteSetting::get($key) === '1']);
    }

    public function updateFees(Request $request)
    {
        $request->validate([
            'service_fee' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        SiteSetting::set('service_fee', trim($request->service_fee));

        return back()->with('success', 'Fee settings saved successfully.');
    }

    // ── Payment API ───────────────────────────────────────────────────────────

    public function updatePayment(Request $request)
    {
        $request->validate([
            'paypal_mode'      => ['required', 'in:sandbox,live'],
            'paypal_client_id' => ['nullable', 'string', 'max:255'],
            'paypal_secret'    => ['nullable', 'string', 'max:255'],
            'stripe_key'       => ['nullable', 'string', 'max:255'],
            'stripe_secret'    => ['nullable', 'string', 'max:255'],
            'easebuzz_env'     => ['nullable', 'in:sandbox,live'],
            'easebuzz_merchant_key' => ['nullable', 'string', 'max:255'],
            'easebuzz_salt'    => ['nullable', 'string', 'max:255'],
            'razorpay_key'     => ['nullable', 'string', 'max:255'],
            'razorpay_secret'  => ['nullable', 'string', 'max:255'],
        ]);

        SiteSetting::set('paypal_mode', $request->paypal_mode);
        if ($request->filled('paypal_client_id')) {
            SiteSetting::set('paypal_client_id', trim($request->paypal_client_id));
        }
        if ($request->filled('paypal_secret')) {
            SiteSetting::set('paypal_secret', trim($request->paypal_secret));
        }
        
        if ($request->filled('stripe_key')) {
            SiteSetting::set('stripe_key', trim($request->stripe_key));
        }
        if ($request->filled('stripe_secret')) {
            SiteSetting::set('stripe_secret', trim($request->stripe_secret));
        }
        
        if ($request->filled('easebuzz_env')) {
            SiteSetting::set('easebuzz_env', $request->easebuzz_env);
        }
        if ($request->filled('easebuzz_merchant_key')) {
            SiteSetting::set('easebuzz_merchant_key', trim($request->easebuzz_merchant_key));
        }
        if ($request->filled('easebuzz_salt')) {
            SiteSetting::set('easebuzz_salt', trim($request->easebuzz_salt));
        }

        if ($request->filled('razorpay_key')) {
            SiteSetting::set('razorpay_key', trim($request->razorpay_key));
        }
        if ($request->filled('razorpay_secret')) {
            SiteSetting::set('razorpay_secret', trim($request->razorpay_secret));
        }

        return back()->with('success', 'Payment API settings saved successfully.');
    }

    public function togglePayment(Request $request, $provider)
    {
        if ($provider === 'paypal') {
            $current = SiteSetting::get('paypal_enabled', '0');
            SiteSetting::set('paypal_enabled', $current === '1' ? '0' : '1');
            return response()->json(['enabled' => SiteSetting::get('paypal_enabled') === '1']);
        } elseif ($provider === 'stripe') {
            $current = SiteSetting::get('stripe_enabled', '0');
            SiteSetting::set('stripe_enabled', $current === '1' ? '0' : '1');
            return response()->json(['enabled' => SiteSetting::get('stripe_enabled') === '1']);
        } elseif ($provider === 'easebuzz') {
            $current = SiteSetting::get('easebuzz_enabled', '0');
            SiteSetting::set('easebuzz_enabled', $current === '1' ? '0' : '1');
            return response()->json(['enabled' => SiteSetting::get('easebuzz_enabled') === '1']);
        } elseif ($provider === 'razorpay') {
            $current = SiteSetting::get('razorpay_enabled', '0');
            SiteSetting::set('razorpay_enabled', $current === '1' ? '0' : '1');
            return response()->json(['enabled' => SiteSetting::get('razorpay_enabled') === '1']);
        }
        return response()->json(['error' => 'Invalid provider'], 400);
    }

    // ── User Status ───────────────────────────────────────────────────────────

    public function toggleUserStatus(Request $request, \App\Models\User $user)
    {
        $request->validate(['is_active' => ['required', 'in:0,1']]);
        $user->update(['is_active' => (bool) $request->is_active]);
        return response()->json(['is_active' => (bool) $user->is_active]);
    }
}
