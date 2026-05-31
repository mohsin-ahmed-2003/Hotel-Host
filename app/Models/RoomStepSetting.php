<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomStepSetting extends Model
{
    use HasFactory;

    protected $table = 'room_steps_settings';
    protected $fillable = ['step_key', 'image', 'description'];
}
