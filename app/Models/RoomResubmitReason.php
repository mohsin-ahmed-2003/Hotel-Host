<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomResubmitReason extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'reason'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
