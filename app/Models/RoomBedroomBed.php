<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomBedroomBed extends Model
{
    protected $table = 'room_bedroom_beds';
    protected $guarded = [];

    public function bedType()
    {
        return $this->belongsTo(RoomBed::class, 'room_bed_id');
    }
}
