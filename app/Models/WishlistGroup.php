<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistGroup extends Model
{
    use HasFactory;

    protected $table = 'wishlist_groups';

    protected $fillable = [
        'user_id',
        'name',
    ];

    /**
     * Get the user who owns this collection group.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
