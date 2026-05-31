<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Room;

class HomeController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $tab = $request->input('tab', 'recommended');
            
            if ($tab === 'recommended') {
                $recentlyBooked = Room::with(['photos', 'roomPrice', 'roomLocation', 'propertyType', 'spaceType'])
                    ->where('status', 'approved')
                    ->orderBy('book_count', 'desc')
                    ->take(8)
                    ->get();

                $mostViewed = Room::with(['photos', 'roomPrice', 'roomLocation', 'propertyType', 'spaceType'])
                    ->where('status', 'approved')
                    ->orderBy('view_count', 'desc')
                    ->take(8)
                    ->get();
                
                return response()->json([
                    'success' => true,
                    'recent_html' => view('home.partials.rooms_grid', [
                        'rooms' => $recentlyBooked,
                        'fallbackIcon' => 'fas fa-hotel',
                        'fallbackText' => 'No recently booked properties listed yet.'
                    ])->render(),
                    'views_html' => view('home.partials.rooms_grid', [
                        'rooms' => $mostViewed,
                        'fallbackIcon' => 'fas fa-eye-slash',
                        'fallbackText' => 'No popular properties found.'
                    ])->render()
                ]);
            }
            
            if ($tab === 'nearby') {
                $lat = $request->input('lat', 9.9575);
                $lng = $request->input('lng', 78.1720);
                $city = $request->input('city', 'Madurai');
                $state = $request->input('state', 'Tamil Nadu');

                // Get all approved rooms sorted by physical proximity using Haversine formula
                $rooms = Room::with(['photos', 'roomPrice', 'roomLocation', 'propertyType', 'spaceType'])
                    ->join('room_locations', 'rooms.id', '=', 'room_locations.room_id')
                    ->where('rooms.status', 'approved')
                    ->select('rooms.*')
                    ->selectRaw(
                        "(6371 * acos(cos(radians(?)) * cos(radians(room_locations.latitude)) * cos(radians(room_locations.longitude) - radians(?)) + sin(radians(?)) * sin(radians(room_locations.latitude)))) AS distance",
                        [$lat, $lng, $lat]
                    )
                    ->orderBy('distance', 'asc')
                    ->get();

                // 1. Current City Rooms (matches user current city)
                $cityRooms = $rooms->filter(function($r) use ($city) {
                    return !empty($r->roomLocation->city) && strtolower(trim($r->roomLocation->city)) === strtolower(trim($city));
                })->take(8)->values();

                // 2. Neighboring Cities Rooms (Grouped by city, excluding user current city)
                $otherCitiesRooms = $rooms->filter(function($r) use ($city) {
                    return !empty($r->roomLocation->city) && strtolower(trim($r->roomLocation->city)) !== strtolower(trim($city));
                });

                $groupedOtherCities = [];
                foreach ($otherCitiesRooms as $room) {
                    $cName = $room->roomLocation->city;
                    if (!isset($groupedOtherCities[$cName])) {
                        $groupedOtherCities[$cName] = [];
                    }
                    if (count($groupedOtherCities[$cName]) < 4) {
                        $groupedOtherCities[$cName][] = $room;
                    }
                }

                $otherCitiesPayload = [];
                foreach ($groupedOtherCities as $cName => $cRooms) {
                    $otherCitiesPayload[] = [
                        'city' => $cName,
                        'html' => view('home.partials.rooms_grid', [
                            'rooms' => collect($cRooms),
                            'fallbackIcon' => 'fas fa-city',
                            'fallbackText' => "No properties in {$cName}."
                        ])->render()
                    ];
                }

                // 3. State Rooms (same state)
                $stateRooms = $rooms->filter(function($r) use ($state) {
                    return !empty($r->roomLocation->state) && strtolower(trim($r->roomLocation->state)) === strtolower(trim($state));
                })->take(8)->values();

                return response()->json([
                    'success' => true,
                    'city_name' => $city,
                    'state_name' => $state,
                    'city_rooms_html' => view('home.partials.rooms_grid', [
                        'rooms' => $cityRooms,
                        'fallbackIcon' => 'fas fa-map-marker-alt',
                        'fallbackText' => "No properties found in {$city}."
                    ])->render(),
                    'other_cities' => $otherCitiesPayload,
                    'state_rooms_html' => view('home.partials.rooms_grid', [
                        'rooms' => $stateRooms,
                        'fallbackIcon' => 'fas fa-map',
                        'fallbackText' => "No properties found in {$state} state."
                    ])->render()
                ]);
            }
            
            if ($tab === 'offers') {
                $rooms = Room::with(['photos', 'roomPrice', 'roomLocation', 'propertyType', 'spaceType'])
                    ->where('status', 'approved')
                    ->get();
                
                $lastMinute = [];
                $earlyBird = [];
                $lengthOfStay = [];
                $special = [];
                
                foreach ($rooms as $room) {
                    $discounts = $room->roomPrice->discounts ?? [];
                    
                    if (!empty($discounts['last_minute']['active'])) {
                        $lastMinute[] = $room;
                    }
                    if (!empty($discounts['early_bird']['active'])) {
                        $earlyBird[] = $room;
                    }
                    if (!empty($discounts['length_of_stay']['active'])) {
                        $lengthOfStay[] = $room;
                    }
                    if (!empty($discounts['custom']['active'])) {
                        $special[] = $room;
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'last_minute_html' => view('home.partials.rooms_grid', [
                        'rooms' => array_slice($lastMinute, 0, 8),
                        'fallbackIcon' => 'fas fa-bolt',
                        'fallbackText' => 'No active Last-Minute Discounts right now.'
                    ])->render(),
                    'early_bird_html' => view('home.partials.rooms_grid', [
                        'rooms' => array_slice($earlyBird, 0, 8),
                        'fallbackIcon' => 'fas fa-feather',
                        'fallbackText' => 'No active Early-Bird Discounts right now.'
                    ])->render(),
                    'length_of_stay_html' => view('home.partials.rooms_grid', [
                        'rooms' => array_slice($lengthOfStay, 0, 8),
                        'fallbackIcon' => 'fas fa-calendar-alt',
                        'fallbackText' => 'No active Length-of-Stay Discounts right now.'
                    ])->render(),
                    'special_html' => view('home.partials.rooms_grid', [
                        'rooms' => array_slice($special, 0, 8),
                        'fallbackIcon' => 'fas fa-gift',
                        'fallbackText' => 'No active Special/Seasonal Deals right now.'
                    ])->render()
                ]);
            }
        }

        $recentlyBooked = Room::with(['photos', 'roomPrice', 'roomLocation', 'propertyType', 'spaceType'])
            ->where('status', 'approved')
            ->orderBy('book_count', 'desc')
            ->take(8)
            ->get();

        $mostViewed = Room::with(['photos', 'roomPrice', 'roomLocation', 'propertyType', 'spaceType'])
            ->where('status', 'approved')
            ->orderBy('view_count', 'desc')
            ->take(8)
            ->get();

        $settings = \App\Models\SiteSetting::pluck('value', 'key');

        return view('home.home', compact('recentlyBooked', 'mostViewed', 'settings'));
    }
}
