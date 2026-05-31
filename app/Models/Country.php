<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'country_name',
        'short_name',
        'phone_code',
        'currency',
    ];

    protected $table = 'countries';
}
