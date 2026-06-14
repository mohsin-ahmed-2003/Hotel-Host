<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    public $timestamps = true;

    protected $fillable = [
        'name', 'email', 'phone', 'password',
        'date_of_birth', 'gender', 'country', 'country_id',
        'role', 'permissions', 'profile_image',
        'reset_code', 'reset_code_expires_at',
        'email_verified', 'email_verify_token', 'email_verified_at',
        'phone_verified', 'phone_verified_at',
        'is_active', 'login_type', 'social_id', 'social_provider',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'date_of_birth'         => 'date',
        'permissions'           => 'array',
        'reset_code_expires_at' => 'datetime',
        'email_verified'        => 'boolean',
        'email_verified_at'     => 'datetime',
        'phone_verified'        => 'boolean',
        'phone_verified_at'     => 'datetime',
        'is_active'             => 'boolean',
    ];

    public function countryRelation()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSubAdmin(): bool
    {
        return $this->role === 'sub_admin';
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) return true;
        return in_array($permission, $this->permissions ?? []);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
