<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingRegistration extends Model
{
    protected $fillable = ['token', 'data', 'phone', 'otp', 'otp_sent_at', 'expires_at'];

    protected $casts = [
        'data'        => 'array',
        'otp_sent_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    public function canResend(): bool
    {
        if (!$this->otp_sent_at) return true;
        return now()->timestamp - $this->otp_sent_at->timestamp >= 30;
    }

    public function secondsUntilResend(): int
    {
        if (!$this->otp_sent_at) return 0;
        $elapsed = now()->timestamp - $this->otp_sent_at->timestamp;
        return max(0, 30 - $elapsed);
    }
}
