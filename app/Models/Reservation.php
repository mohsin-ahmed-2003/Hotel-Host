<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'room_id', 'user_id', 'checkin', 'checkout', 'guests', 
        'base_amount', 'service_fee', 'tax', 'security_deposit', 'food_amount', 'total_amount',
        'enhancements_data', 'payment_type', 'transaction_id', 'status', 'reservation_status'
    ];

    protected $casts = [
        'enhancements_data' => 'array',
        'checkin' => 'date',
        'checkout' => 'date',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::updated(function ($reservation) {
            // Increment room book_count when a reservation is successfully completed
            if ($reservation->isDirty('status') && $reservation->status === 'success') {
                if ($reservation->room) {
                    $reservation->room->increment('book_count');
                }
            }
        });
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
