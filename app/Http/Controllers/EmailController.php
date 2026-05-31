<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    // ── Shared email config ───────────────────────────────────────────────────

    public static function configureMailer(): bool
    {
        $enabled  = SiteSetting::get('mail_enabled', '0');
        $username = SiteSetting::get('mail_username', '');
        $password = SiteSetting::get('mail_password', '');
        $from     = SiteSetting::get('mail_from', '');
        $fromName = SiteSetting::get('mail_from_name', 'Hotel Host');

        if ($enabled !== '1' || empty($username) || empty($password) || empty($from)) {
            Log::warning('EmailController: Mailer configuration failed. Check site settings.', [
                'enabled' => $enabled,
                'has_username' => !empty($username),
                'has_password' => !empty($password),
                'has_from' => !empty($from)
            ]);
            return false;
        }

        config([
            'mail.default'                      => 'smtp',
            'mail.mailers.smtp.host'            => 'smtp.gmail.com',
            'mail.mailers.smtp.port'            => 587,
            'mail.mailers.smtp.encryption'      => 'tls',
            'mail.mailers.smtp.username'        => $username,
            'mail.mailers.smtp.password'        => $password,
            'mail.from.address'                 => $from,
            'mail.from.name'                    => $fromName,
        ]);

        return true;
    }

    public static function siteData(): array
    {
        $logoPath = SiteSetting::get('mail_email_logo') ?: SiteSetting::get('site_logo');
        return [
            'siteName' => SiteSetting::get('site_name', 'Hotel Host'),
            'siteLogo' => $logoPath ? asset('storage/' . $logoPath) : null,
        ];
    }

    // ── Email verification ────────────────────────────────────────────────────

    public static function sendVerification(User $user): void
    {
        if (!self::configureMailer()) return;

        $data = array_merge(self::siteData(), [
            'userName'  => $user->name,
            'verifyUrl' => url('/email/verify/' . $user->email_verify_token),
        ]);

        Mail::send('emails.verify_email', $data, function ($msg) use ($user, $data) {
            $msg->to($user->email, $user->name)
                ->subject('Verify Your Email — ' . $data['siteName']);
        });
    }

    public static function sendVerificationSuccess(User $user): void
    {
        if (!self::configureMailer()) return;

        $data = array_merge(self::siteData(), ['userName' => $user->name]);

        Mail::send('emails.verify_success', $data, function ($msg) use ($user, $data) {
            $msg->to($user->email, $user->name)
                ->subject('Email Verified — Welcome to ' . $data['siteName']);
        });
    }

    // ── Welcome email ─────────────────────────────────────────────────────────

    public static function sendWelcome(User $user): void
    {
        if (!self::configureMailer()) return;

        $data = array_merge(self::siteData(), ['userName' => $user->name]);

        Mail::send('emails.welcome', $data, function ($msg) use ($user, $data) {
            $msg->to($user->email, $user->name)
                ->subject('Welcome to ' . $data['siteName'] . ' 🏨');
        });
    }

    // ── Forgot password email ─────────────────────────────────────────────────

    public static function sendResetCode(User $user, string $code): void
    {
        if (!self::configureMailer()) return;

        $data = array_merge(self::siteData(), [
            'userName'  => $user->name,
            'resetCode' => $code,
        ]);

        Mail::send('emails.reset_password', $data, function ($msg) use ($user, $data) {
            $msg->to($user->email, $user->name)
                ->subject('Your Password Reset Code — ' . $data['siteName']);
        });
    }

    // ── Password reset success email ──────────────────────────────────────────

    public static function sendResetSuccess(User $user): void
    {
        if (!self::configureMailer()) return;

        $data = array_merge(self::siteData(), ['userName' => $user->name]);

        Mail::send('emails.password_reset_success', $data, function ($msg) use ($user, $data) {
            $msg->to($user->email, $user->name)
                ->subject('Password Reset Successful — ' . $data['siteName']);
        });
    }

    // ── Room notifications ────────────────────────────────────────────────────

    public static function sendRoomCreated($room): void
    {
        if (!self::configureMailer()) return;

        $user = $room->user;
        if (!$user) {
            Log::error('EmailController: Cannot send room created email. Room has no user.', ['room_id' => $room->id]);
            return;
        }

        $data = array_merge(self::siteData(), [
            'userName' => $user->name,
            'room' => $room,
        ]);

        try {
            Mail::send('emails.room_created', $data, function ($msg) use ($user, $data) {
                $msg->to($user->email, $user->name)
                    ->subject('Property Listing Created Successfully — ' . $data['siteName']);
            });
            Log::info('EmailController: Room created email sent successfully.', ['user_email' => $user->email, 'room_id' => $room->id]);
        } catch (\Exception $e) {
            Log::error('EmailController: Failed to send room created email.', [
                'error' => $e->getMessage(),
                'user_email' => $user->email,
                'room_id' => $room->id
            ]);
        }
    }

    public static function sendRoomApproved($room): void
    {
        if (!self::configureMailer()) return;

        $user = $room->user;
        if (!$user) {
            Log::error('EmailController: Cannot send room approved email. Room has no user.', ['room_id' => $room->id]);
            return;
        }

        $data = array_merge(self::siteData(), [
            'userName' => $user->name,
            'room' => $room,
        ]);

        try {
            Mail::send('emails.room_approved', $data, function ($msg) use ($user, $data) {
                $msg->to($user->email, $user->name)
                    ->subject('Congratulations! Your Property is Approved — ' . $data['siteName']);
            });
            Log::info('EmailController: Room approved email sent successfully.', ['user_email' => $user->email, 'room_id' => $room->id]);
        } catch (\Exception $e) {
            Log::error('EmailController: Failed to send room approved email.', [
                'error' => $e->getMessage(),
                'user_email' => $user->email,
                'room_id' => $room->id
            ]);
        }
    }
}
