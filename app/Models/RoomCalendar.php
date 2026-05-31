<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomCalendar extends Model
{
    protected $fillable = ['room_id', 'date', 'is_blocked'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
