<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'group_name',
    ];

    /**
     * Get the room associated with this wishlist item.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the user associated with this wishlist item.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
