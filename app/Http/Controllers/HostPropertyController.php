<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomPhoto;
use App\Models\RoomLocation;
use App\Models\RoomPrice;
use App\Models\RoomEnhancement;
use App\Models\PropertyType;
use App\Models\SpaceType;
use App\Models\Amenity;
use App\Models\Currency;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\RoomCreatedMail;

class HostPropertyController extends Controller
{
    public function __construct()
    {
    // Require auth, but we don't strictly use auth middleware here if handled in routes
    }

    public function start()
    {
        $room = Room::create([
            'user_id' => session('user_id'),
            'status' => 'draft',
        ]);

        // Ensure default related rows exist
        RoomPrice::create(['room_id' => $room->id]);
        RoomLocation::create(['room_id' => $room->id, 'location_name' => '']);

        return redirect()->route('host.step', ['room' => $room->id, 'step' => 1]);
    }

    public function step(Room $room, $step)
    {
        // Ensure user owns this room
        if ($room->user_id !== session('user_id'))
            abort(403);

        // Defensive check: Ensure related rows exist if they were missed during creation
        if (!$room->roomLocation) $room->roomLocation()->create(['location_name' => '']);
        if (!$room->roomPrice)    $room->roomPrice()->create(['price' => 0]);

        $propertyTypes = PropertyType::where('status', 1)->get();
        $spaceTypes = SpaceType::where('status', 1)->get();
        $amenities = Amenity::all();
        $currencies = Currency::all();
        $roomBeds = \App\Models\RoomBed::all();
        $roomRules = \App\Models\RoomRule::all();

        $stepKeys = [
            1 => 'basic', 
            2 => 'media', 
            3 => 'location', 
            4 => 'amenities', 
            5 => 'pricing',
            6 => 'rules_calendar'
        ];
        $stepKey = $stepKeys[$step] ?? 'basic';
        $stepSetting = \App\Models\RoomStepSetting::where('step_key', $stepKey)->first();

        return view('host.step' . $step, compact('room', 'step', 'propertyTypes', 'spaceTypes', 'amenities', 'currencies', 'stepSetting', 'roomBeds', 'roomRules'));
    }

    public function saveField(Request $request, Room $room)
    {
        if ($room->user_id !== session('user_id'))
            return response()->json(['error' => 'Unauthorized'], 403);

        $field = $request->input('field');
        $value = $request->input('value');
        $table = $request->input('table', 'rooms');
        $stepNum = $request->input('step');

        if ($table === 'rooms') {
            if ($field === 'selected_rules' && is_string($value)) {
                $value = json_decode($value, true);
            }
            $room->update([$field => $value]);
        } elseif ($table === 'room_locations') {
            $room->roomLocation()->updateOrCreate(['room_id' => $room->id], [$field => $value]);
        } elseif ($table === 'room_prices') {
            if (($field === 'discounts' || $field === 'additional_pricing') && is_string($value)) {
                $value = json_decode($value, true);
            }
            RoomPrice::updateOrCreate(['room_id' => $room->id], [$field => $value]);
        }

        return response()->json([
            'success' => true,
            'step_valid' => $stepNum ? $room->isStepValid($stepNum) : null,
            'step' => $stepNum
        ]);
    }

    public function toggleCalendarDate(Request $request, Room $room)
    {
        if ($room->user_id !== session('user_id'))
            return response()->json(['error' => 'Unauthorized'], 403);

        $date = $request->input('date');
        $block = $request->input('block');

        if ($block) {
            $room->calendars()->updateOrCreate(
                ['date' => $date],
                ['is_blocked' => true]
            );
        } else {
            $room->calendars()->where('date', $date)->delete();
        }

        return response()->json([
            'success' => true,
            'step_valid' => $room->isStepValid(6),
            'step' => 6
        ]);
    }

    // Save multiple fields at once (used for lat+lng, address components)
    public function saveMultiple(Request $request, Room $room)
    {
        if ($room->user_id !== session('user_id'))
            return response()->json(['error' => 'Unauthorized'], 403);

        $fields = $request->input('fields', []);
        $stepNum = $request->input('step');

        foreach ($fields as $item) {
            $field = $item['field'] ?? null;
            $value = $item['value'] ?? null;
            $table = $item['table'] ?? 'rooms';

            if (!$field) continue;

            if ($table === 'rooms') {
                $room->update([$field => $value]);
            } elseif ($table === 'room_locations') {
                $room->roomLocation()->updateOrCreate(['room_id' => $room->id], [$field => $value]);
            } elseif ($table === 'room_prices') {
                if (($field === 'discounts' || $field === 'additional_pricing') && is_string($value)) {
                    $value = json_decode($value, true);
                }
                RoomPrice::updateOrCreate(['room_id' => $room->id], [$field => $value]);
            }
        }

        return response()->json([
            'success' => true,
            'step_valid' => $stepNum ? $room->isStepValid($stepNum) : null,
            'step' => $stepNum
        ]);
    }

    public function saveAmenities(Request $request, Room $room)
    {
        if ($room->user_id !== session('user_id'))
            return response()->json(['error' => 'Unauthorized'], 403);
            
        $room->amenities()->sync($request->amenities ?? []);
        $stepNum = $request->input('step');

        return response()->json([
            'success' => true,
            'step_valid' => $stepNum ? $room->isStepValid($stepNum) : null,
            'step' => $stepNum
        ]);
    }

    public function saveBedrooms(Request $request, Room $room)
    {
        if ($room->user_id !== session('user_id'))
            return response()->json(['error' => 'Unauthorized'], 403);

        $bedroomsCount = $request->input('bedrooms_count', 1);
        if ($bedroomsCount < 1) $bedroomsCount = 1;

        $room->update(['bedrooms_count' => $bedroomsCount]);

        $allocations = $request->input('allocations', []);

        // Re-sync bedroom beds
        $room->bedroomBeds()->delete();

        foreach ($allocations as $alloc) {
            $bedroomIndex = $alloc['bedroom_index'] ?? null;
            $bedId = $alloc['room_bed_id'] ?? null;
            $count = $alloc['count'] ?? 0;

            if ($bedroomIndex && $bedId && $count > 0) {
                $room->bedroomBeds()->create([
                    'bedroom_index' => $bedroomIndex,
                    'room_bed_id' => $bedId,
                    'count' => $count
                ]);
            }
        }

        $stepNum = $request->input('step', 4);

        return response()->json([
            'success' => true,
            'step_valid' => $room->isStepValid($stepNum),
            'step' => $stepNum
        ]);
    }

    public function uploadPhoto(Request $request, Room $room)
    {
        if ($room->user_id !== session('user_id'))
            return response()->json(['error' => 'Unauthorized'], 403);
        $request->validate(['photo' => 'required|image']);

        $path = $request->file('photo')->store('room_' . $room->id, 'public');
        $photo = $room->photos()->create(['photo_path' => $path]);

        return response()->json([
            'success' => true, 
            'photo' => $photo,
            'step_valid' => $room->isStepValid(2),
            'step' => 2
        ]);
    }

    public function updatePhotoDesc(Request $request, RoomPhoto $photo)
    {
        if ($photo->room->user_id !== session('user_id'))
            return response()->json(['error' => 'Unauthorized'], 403);
        $photo->update(['description' => $request->description]);
        return response()->json(['success' => true]);
    }

    public function deletePhoto(RoomPhoto $photo)
    {
        if ($photo->room->user_id !== session('user_id'))
            return response()->json(['error' => 'Unauthorized'], 403);
        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();
        
        return response()->json([
            'success' => true,
            'step_valid' => $photo->room->isStepValid(2),
            'step' => 2
        ]);
    }

    public function uploadVideo(Request $request, Room $room)
    {
        if ($room->user_id !== session('user_id'))
            return response()->json(['error' => 'Unauthorized'], 403);
        $request->validate(['video' => 'required|mimes:mp4,mov,ogg,qt|max:20000']);
        if ($room->video_path)
            Storage::disk('public')->delete($room->video_path);

        $path = $request->file('video')->store('room_' . $room->id, 'public');
        $room->update(['video_path' => $path, 'video_type' => 'video']);
        return response()->json(['success' => true]);
    }

    public function addEnhancement(Request $request, Room $room)
    {
        if ($room->user_id !== session('user_id'))
            return response()->json(['error' => 'Unauthorized'], 403);

        $enhancement = $room->enhancements()->create([
            'type' => $request->type,
            'item_name' => $request->item_name,
            'price' => $request->price,
            'currency' => $request->currency,
            'is_active' => true,
        ]);
        return response()->json(['success' => true, 'enhancement' => $enhancement]);
    }

    public function toggleEnhancement(Request $request, RoomEnhancement $enhancement)
    {
        if ($enhancement->room->user_id !== session('user_id'))
            return response()->json(['error' => 'Unauthorized'], 403);
        $enhancement->update(['is_active' => $request->is_active]);
        return response()->json(['success' => true]);
    }

    public function saveEnhancements(Request $request, Room $room)
    {
        if ($room->user_id !== session('user_id'))
            return response()->json(['error' => 'Unauthorized'], 403);

        $type = $request->type;

        if ($request->has('is_per_guest')) {
            // Delete existing ones of this type for this room to sync
            $room->enhancements()->where('type', $type)->delete();

            if ($request->is_per_guest) {
                $room->enhancements()->create([
                    'type' => $type,
                    'item_name' => $type . ' Service',
                    'price' => $request->price ?? 0,
                    'currency' => 'USD',
                    'is_active' => true,
                    'is_per_guest' => true
                ]);
            }
            return response()->json(['success' => true]);
        }

        $items = $request->items; // Array of [item_name, price]

        // Delete existing ones of this type for this room to sync
        $room->enhancements()->where('type', $type)->delete();

        if (is_array($items)) {
            foreach ($items as $item) {
                if (!empty($item['item_name'])) {
                    $room->enhancements()->create([
                        'type' => $type,
                        'item_name' => $item['item_name'],
                        'price' => $item['price'] ?? 0,
                        'currency' => 'USD',
                        'is_active' => true,
                        'is_per_guest' => false
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function finish(Room $room)
    {
        if ($room->user_id !== session('user_id'))
            abort(403);
            
        $room->update(['status' => 'pending']);
        
        // Send email to host using the same flow as signup/verification
        try {
            dispatch(function () use ($room) {
                \App\Http\Controllers\EmailController::sendRoomCreated($room);
            })->afterResponse();
        } catch (\Exception $e) {
            \Log::error('Room created email failed: ' . $e->getMessage());
        }
        
        return redirect()->route('user.properties')->with('success', 'Property submitted for approval!');
    }
}
