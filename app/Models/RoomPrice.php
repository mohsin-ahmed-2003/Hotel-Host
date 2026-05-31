<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPrice extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'discounts'          => 'array',
        'additional_pricing' => 'array',
    ];

    public function currencyDetail()
    {
        return $this->belongsTo(Currency::class, 'currency', 'currency_code');
    }
}
