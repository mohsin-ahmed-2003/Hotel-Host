<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'reservation_id',
        'room_id',
        'user_id',
        'room_space',
        'room_amenities',
        'room_arrangement',
        'dining_services',
        'room_cleanness',
        'stay_location',
        'description',
        'host_approved',
    ];

    protected $casts = [
        'host_approved' => 'boolean',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRatingAttribute()
    {
        $fields = ['room_space', 'room_amenities', 'room_arrangement', 'room_cleanness', 'stay_location'];
        $sum = 0;
        $count = count($fields);

        foreach ($fields as $field) {
            $sum += $this->{$field};
        }

        if ($this->dining_services) {
            $sum += $this->dining_services;
            $count++;
        }

        return $count > 0 ? round($sum / $count, 1) : 0;
    }
}
