<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'currency',
        'duration_days',
        'hosting_allowed',
        'cancellations_allowed',
        'cancellation_fee_reduction',
        'is_active',
    ];
}
