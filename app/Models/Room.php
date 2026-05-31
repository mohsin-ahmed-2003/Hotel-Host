<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'selected_rules' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function spaceType()
    {
        return $this->belongsTo(SpaceType::class);
    }

    public function photos()
    {
        return $this->hasMany(RoomPhoto::class);
    }

    public function roomLocation()
    {
        return $this->hasOne(RoomLocation::class, 'room_id');
    }

    public function location()
    {
        return $this->roomLocation();
    }

    public function roomPrice()
    {
        return $this->hasOne(RoomPrice::class, 'room_id');
    }

    public function enhancements()
    {
        return $this->hasMany(RoomEnhancement::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'room_amenities');
    }

    public function bedroomBeds()
    {
        return $this->hasMany(RoomBedroomBed::class, 'room_id');
    }

    public function calendars()
    {
        return $this->hasMany(RoomCalendar::class);
    }

    public function resubmitReason()
    {
        return $this->hasOne(RoomResubmitReason::class);
    }

    public function getResubmitReasonTextAttribute()
    {
        return $this->resubmitReason->reason ?? '';
    }

    public function getDisplayNameAttribute()
    {
        return $this->title ?: ($this->name ?: 'N/A');
    }

    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    public function getPriceAttribute()
    {
        return $this->roomPrice->price ?? 0;
    }

    public function getCurrencySymbolAttribute()
    {
        return $this->roomPrice->currencyDetail->symbol ?? ($this->roomPrice->currency ?? '$');
    }

    public function isStepValid($stepNum)
    {
        switch ($stepNum) {
            case 1: // Basics
                return $this->title && $this->property_type_id && $this->space_type_id;
            case 2: // Media
                return $this->photos()->exists();
            case 3: // Location
                return ($this->roomLocation && $this->roomLocation->location_name) || $this->address;
            case 4: // Amenities & Sleeping arrangements
                return $this->amenities()->exists() || $this->bedroomBeds()->exists();
            case 5: // Pricing
                return ($this->roomPrice && $this->roomPrice->price > 0) || $this->price > 0;
            case 6: // Rules & Calendar
                return !empty($this->booking_type) && !empty($this->cancellation_policy);
            default:
                return true;
        }
    }

    public function countMissingSteps()
    {
        $missing = 0;
        for ($i = 1; $i <= 6; $i++) {
            if (!$this->isStepValid($i)) {
                $missing++;
            }
        }
        return $missing;
    }
}
