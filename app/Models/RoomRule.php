<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomRule extends Model
{
    protected $fillable = ['rule_name', 'rule_text', 'icon'];
}
