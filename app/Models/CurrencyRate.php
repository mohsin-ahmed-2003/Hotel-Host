<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_currency',
        'target_currency',
        'rate',
        'rate_date',
    ];

    /**
     * Get the currency details associated with this rate.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'target_currency', 'currency_code');
    }
}
