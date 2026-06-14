<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Helpers\price_calculation;

class RoomController extends Controller
{
    /**
     * Display the search results / room list.
     */
    public function index(Request $request)
    {
        $query = Room::where('status', 'approved');

        // Note: Full search logic will go here (location, checkin, checkout, guests filtering)
        
        $rooms = $query->paginate(12);

        return view('rooms.index', compact('rooms'));
    }

    /**
     * Display the public room details page.
     */
    public function show(Room $room)
    {
        // For public viewing, we might want to restrict to approved rooms,
        // but to allow hosts to preview, we'll allow access if they are the owner,
        // otherwise only approved rooms.
        if ($room->status !== 'approved' && (!auth()->check() || auth()->id() !== $room->user_id)) {
            abort(404, 'Room not found or not available.');
        }

        // Clean up expired custom discounts dynamically in the database
        if ($room->roomPrice) {
            $discounts = $room->roomPrice->discounts ?? [];
            if (!empty($discounts['custom']['rules'])) {
                $today = date('Y-m-d');
                $rules = $discounts['custom']['rules'];
                $filteredRules = array_filter($rules, function($rule) use ($today) {
                    return empty($rule['end_date']) || $rule['end_date'] >= $today;
                });

                if (count($rules) !== count($filteredRules)) {
                    $discounts['custom']['rules'] = array_values($filteredRules);
                    
                    // Persist clean JSON to room_prices table
                    $roomPrice = $room->roomPrice;
                    $roomPrice->discounts = $discounts;
                    $roomPrice->save();
                }
            }
        }

        // Eager load relationships needed for the view
        $room->load([
            'photos', 
            'amenities', 
            'roomLocation', 
            'user', 
            'propertyType', 
            'spaceType', 
            'roomPrice', 
            'enhancements',
            'bedroomBeds',
            'bedroomBeds.bedType'
        ]);

        // Increment the view count
        $room->increment('view_count');

        // Fetch similar properties
        $similarRooms = Room::where('status', 'approved')
            ->where('rooms.id', '!=', $room->id)
            ->select('rooms.*')
            ->leftJoin('room_locations', 'rooms.id', '=', 'room_locations.room_id')
            ->orderByRaw("
                CASE 
                    WHEN rooms.user_id = ? THEN 1
                    WHEN room_locations.city = ? THEN 2
                    WHEN room_locations.state = ? THEN 3
                    ELSE 4
                END
            ", [
                $room->user_id, 
                $room->roomLocation->city ?? '', 
                $room->roomLocation->state ?? ''
            ])
            ->with(['roomLocation', 'photos', 'roomPrice'])
            ->take(4)
            ->get();

        return view('rooms.show', compact('room', 'similarRooms'));
    }

    /**
     * Calculate stay pricing dynamically via AJAX.
     */
    public function calculatePrice(Request $request, Room $room)
    {
        $request->validate([
            'checkin' => 'required|date',
            'checkout' => 'required|date|after:checkin',
            'guests' => 'nullable|integer|min:1',
            'enhancement_ids' => 'nullable|array',
            'enhancement_ids.*' => 'integer|exists:room_enhancements,id',
            'enhancement_dates' => 'nullable|array'
        ]);

        $result = price_calculation::calculate(
            $room,
            $request->input('checkin'),
            $request->input('checkout'),
            (int) $request->input('guests', 2),
            $request->input('enhancement_ids', []),
            $request->input('enhancement_dates', [])
        );

        return response()->json($result);
    }

    public function booking_page(Request $request, Room $room)
    {
        if ($request->isMethod('get')) {
            if (session()->has('pending_booking_' . $room->id)) {
                $request->merge(session('pending_booking_' . $room->id));
            } else {
                return redirect()->route('rooms.show', $room->id)->with('error', 'Booking session expired. Please select your dates again.');
            }
        }

        $request->validate([
            'checkin' => 'required|date',
            'checkout' => 'required|date|after:checkin',
            'guests' => 'nullable|integer|min:1',
            'enhancement_ids' => 'nullable',
            'enhancement_dates' => 'nullable',
        ]);

        if (!auth()->check()) {
            session(['pending_booking_' . $room->id => $request->all()]);
            session(['url.intended' => route('rooms.booking_page', $room->id)]);
            return redirect()->route('auth')->with('error', 'Please login to continue with your booking.');
        }

        $checkin = $request->input('checkin');
        $checkout = $request->input('checkout');
        $guests = (int) $request->input('guests', 2);
        
        $enhancementIds = $request->input('enhancement_ids', []);
        if (is_string($enhancementIds)) {
            $enhancementIds = array_filter(explode(',', $enhancementIds));
        }

        $enhancementDates = $request->input('enhancement_dates', []);
        if (is_string($enhancementDates)) {
            $enhancementDates = json_decode($enhancementDates, true) ?: [];
        }

        $priceData = price_calculation::calculate(
            $room,
            $checkin,
            $checkout,
            $guests,
            $enhancementIds,
            $enhancementDates
        );

        $room->load(['photos', 'user', 'roomLocation']);

        return view('rooms.checkout', compact('room', 'checkin', 'checkout', 'guests', 'priceData'));
    }
}
