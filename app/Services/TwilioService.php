<?php

namespace App\Services;

use App\Models\Country;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    public static function isEnabled(): bool
    {
        return SiteSetting::get('twilio_enabled', '0') === '1'
            && !empty(SiteSetting::get('twilio_sid'))
            && !empty(SiteSetting::get('twilio_token'))
            && !empty(SiteSetting::get('twilio_service_sid'));
    }

    /**
     * Ensure phone is in E.164 format (+[country_code][number]).
     * If it already starts with '+' it is returned as-is.
     * Otherwise we look up the country_id stored in session to get the dial code.
     */
    public static function formatPhone(string $phone, ?int $countryId = null): string
    {
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        // Strip a leading 0 (local format)
        $phone = ltrim($phone, '0');

        $dialCode = null;

        if ($countryId) {
            $country  = Country::find($countryId);
            $dialCode = $country?->phone_code; // e.g. "+66" or "66"
        }

        if (!$dialCode) {
            // Fall back to session country_id if available
            $sessionCountryId = session('user')?->country_id ?? session('pending_country_id');
            if ($sessionCountryId) {
                $country  = Country::find($sessionCountryId);
                $dialCode = $country?->phone_code;
            }
        }

        if ($dialCode) {
            $dialCode = '+' . ltrim($dialCode, '+');
            return $dialCode . $phone;
        }

        // Cannot determine country code — return with + prefix as best-effort
        Log::warning('TwilioService::formatPhone — could not resolve country code for phone: ' . $phone);
        return '+' . $phone;
    }

    public static function sendOtp(string $phone, ?int $countryId = null): array
    {
        if (!self::isEnabled()) {
            Log::warning('TwilioService::sendOtp — Twilio is disabled or credentials are missing.');
            return ['success' => false, 'message' => 'SMS service is not configured.'];
        }

        $formatted = self::formatPhone($phone, $countryId);

        try {
            $sid        = SiteSetting::get('twilio_sid');
            $token      = SiteSetting::get('twilio_token');
            $serviceSid = SiteSetting::get('twilio_service_sid');

            Log::info('TwilioService::sendOtp — sending to: ' . $formatted);

            $resp = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://verify.twilio.com/v2/Services/{$serviceSid}/Verifications", [
                    'To'      => $formatted,
                    'Channel' => 'sms',
                ]);

            if ($resp->successful()) {
                Log::info('TwilioService::sendOtp — success for: ' . $formatted);
                return ['success' => true];
            }

            Log::error('TwilioService::sendOtp — HTTP ' . $resp->status() . ' for ' . $formatted, [
                'response_body' => $resp->body(),
                'twilio_code'   => $resp->json('code'),
                'twilio_message'=> $resp->json('message'),
                'more_info'     => $resp->json('more_info'),
            ]);

            return [
                'success' => false,
                'message' => $resp->json('message', 'Failed to send OTP.'),
            ];

        } catch (\Exception $e) {
            Log::error('TwilioService::sendOtp — exception', [
                'phone'     => $formatted,
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);
            return ['success' => false, 'message' => 'SMS service error. Please try again.'];
        }
    }

    public static function verifyOtp(string $phone, string $code, ?int $countryId = null): array
    {
        if (!self::isEnabled()) {
            Log::warning('TwilioService::verifyOtp — Twilio is disabled or credentials are missing.');
            return ['success' => false, 'message' => 'SMS service is not configured.'];
        }

        $formatted = self::formatPhone($phone, $countryId);

        try {
            $sid        = SiteSetting::get('twilio_sid');
            $token      = SiteSetting::get('twilio_token');
            $serviceSid = SiteSetting::get('twilio_service_sid');

            Log::info('TwilioService::verifyOtp — checking code for: ' . $formatted);

            $resp = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://verify.twilio.com/v2/Services/{$serviceSid}/VerificationCheck", [
                    'To'   => $formatted,
                    'Code' => $code,
                ]);

            if ($resp->successful() && $resp->json('status') === 'approved') {
                Log::info('TwilioService::verifyOtp — approved for: ' . $formatted);
                return ['success' => true];
            }

            Log::error('TwilioService::verifyOtp — HTTP ' . $resp->status() . ' for ' . $formatted, [
                'response_body'  => $resp->body(),
                'twilio_code'    => $resp->json('code'),
                'twilio_message' => $resp->json('message'),
                'verify_status'  => $resp->json('status'),
                'more_info'      => $resp->json('more_info'),
            ]);

            return ['success' => false, 'message' => 'Invalid or expired OTP code.'];

        } catch (\Exception $e) {
            Log::error('TwilioService::verifyOtp — exception', [
                'phone'  => $formatted,
                'error'  => $e->getMessage(),
                'trace'  => $e->getTraceAsString(),
            ]);
            return ['success' => false, 'message' => 'Verification error. Please try again.'];
        }
    }
}
