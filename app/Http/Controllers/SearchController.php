<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\PropertyType;
use App\Models\SpaceType;
use App\Models\Amenity;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::where('status', 'approved')->with([
            'photos',
            'roomLocation',
            'propertyType',
            'spaceType',
            'roomPrice',
            'reviews',
            'amenities'
        ]);

        // Location Filter
        if ($request->filled('city')) {
            $cityParam = $request->input('city');
            // 'Madurai, Tamil Nadu, India' -> ['Madurai', 'Tamil Nadu', 'India']
            $parts = array_map('trim', explode(',', $cityParam));
            $mainLocation = $parts[0] ?? $cityParam; // e.g., 'Madurai'
            
            $query->whereHas('roomLocation', function ($q) use ($mainLocation, $cityParam) {
                $q->where(function($subQ) use ($mainLocation, $cityParam) {
                    // Match the full string just in case
                    $subQ->where('location_name', 'like', '%' . $cityParam . '%')
                         // Match the primary/most specific location
                         ->orWhere('city', 'like', '%' . $mainLocation . '%')
                         ->orWhere('state', 'like', '%' . $mainLocation . '%')
                         ->orWhere('country', 'like', '%' . $mainLocation . '%')
                         ->orWhere('location_name', 'like', '%' . $mainLocation . '%');
                });
            });
        }

        // Guests Filter
        if ($request->filled('guests')) {
            $guests = $request->input('guests');
            if ($guests === '10+') {
                $query->whereRaw('CAST(REGEXP_REPLACE(accommodation, "[^0-9]", "") AS UNSIGNED) >= 10');
            } else {
                $guestsNum = (int) $guests;
                $query->whereRaw('CAST(REGEXP_REPLACE(accommodation, "[^0-9]", "") AS UNSIGNED) >= ?', [$guestsNum]);
            }
        }

        // Price Filter
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $min = $request->input('min_price', 0);
            $max = $request->input('max_price', 999999);
            $query->whereHas('roomPrice', function ($q) use ($min, $max) {
                $q->whereBetween('price', [$min, $max]);
            });
        }

        // Space Types Filter
        if ($request->filled('space_types')) {
            $spaceTypes = (array) $request->input('space_types');
            $query->whereIn('space_type_id', $spaceTypes);
        }

        // Property Type Filter
        if ($request->filled('property_type')) {
            $query->where('property_type_id', $request->input('property_type'));
        }

        // Booking Type Filter (Instant vs Request)
        if ($request->filled('booking_type')) {
            $bookingType = $request->input('booking_type'); // 'instant' or 'request'
            $query->where('booking_type', $bookingType);
        }

        // Bedrooms Filter
        if ($request->filled('bedrooms') && $request->input('bedrooms') !== 'any') {
            $bedrooms = $request->input('bedrooms');
            if ($bedrooms === '10+') {
                $query->where('bedrooms_count', '>=', 10);
            } else {
                $query->where('bedrooms_count', '>=', (int) $bedrooms);
            }
        }

        // Beds Filter
        if ($request->filled('beds') && $request->input('beds') !== 'any') {
            $beds = $request->input('beds');
            if ($beds === '10+') {
                $query->whereRaw('(SELECT SUM(count) FROM room_bedroom_beds WHERE room_bedroom_beds.room_id = rooms.id) >= 10');
            } else {
                $query->whereRaw('(SELECT SUM(count) FROM room_bedroom_beds WHERE room_bedroom_beds.room_id = rooms.id) >= ?', [(int) $beds]);
            }
        }

        // Amenities Filter (AND logic)
        if ($request->filled('amenities')) {
            $amenities = (array) $request->input('amenities');
            foreach ($amenities as $amenityId) {
                $query->whereHas('amenities', function ($q) use ($amenityId) {
                    $q->where('amenities.id', $amenityId);
                });
            }
        }

        $rooms = $query->paginate(12)->withQueryString();

        $propertyTypes = PropertyType::all();
        $spaceTypes = SpaceType::all();
        $amenitiesList = Amenity::all();

        return view('search.index', compact('rooms', 'propertyTypes', 'spaceTypes', 'amenitiesList'));
    }
}
