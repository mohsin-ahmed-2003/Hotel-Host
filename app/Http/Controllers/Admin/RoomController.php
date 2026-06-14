<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Room, RoomStepSetting, RoomResubmitReason, User, PropertyType, SpaceType, Amenity, RoomPhoto, Currency};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\RoomApprovedMail;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with(['user', 'resubmitReason', 'roomPrice.currencyDetail'])
            ->withCount('reservations as book_count');
        
        if ($request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('id', $searchTerm)
                  ->orWhere('title', 'like', "%{$searchTerm}%")
                  ->orWhere('name', 'like', "%{$searchTerm}%");
            });
        }

        $rooms = $query->latest()->paginate(10);
        return view('admin.rooms.index', compact('rooms'));
    }

    public function updateStatus(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|in:approved,pending,resubmit',
        ]);

        if ($request->status === 'approved') {
            if ($room->countMissingSteps() > 0) {
                return response()->json(['success' => false, 'message' => 'Cannot approve room. All 6 steps must be completed.'], 400);
            }
        }

        $oldStatus = $room->status;
        $room->update(['status' => $request->status]);

        if ($oldStatus !== 'approved' && $request->status === 'approved') {
            try {
                dispatch(function () use ($room) {
                    \App\Http\Controllers\EmailController::sendRoomApproved($room);
                })->afterResponse();
            } catch (\Exception $e) {
                \Log::error('Room approved email failed: ' . $e->getMessage());
            }
        }

        // Notify Room Owner
        if ($room->user) {
            $reasonText = $request->status === 'resubmit' ? $request->resubmit_reason : null;
            $room->user->notify(new \App\Notifications\RoomStatusUpdatedNotification($room, $request->status, $reasonText));
        }

        return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
    }

    public function settings()
    {
        $settings = RoomStepSetting::all()->keyBy('step_key');
        return view('admin.rooms.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $steps = ['basic', 'media', 'location', 'amenities', 'pricing'];
        
        foreach ($steps as $step) {
            $setting = RoomStepSetting::firstOrNew(['step_key' => $step]);
            $setting->description = $request->input("desc_{$step}");
            
            if ($request->hasFile("img_{$step}")) {
                if ($setting->image) {
                    Storage::disk('public')->delete($setting->image);
                }
                $setting->image = $request->file("img_{$step}")->store('step_settings', 'public');
            }
            
            $setting->save();
        }

        return redirect()->route('admin.rooms.settings')->with('success', 'Room settings updated successfully!');
    }

    public function create()
    {
        $users = User::all();
        $propertyTypes = PropertyType::all();
        $spaceTypes = SpaceType::all();
        $amenities = Amenity::all();
        $currencies = Currency::all();
        $stepSettings = RoomStepSetting::all()->keyBy('step_key');
        $roomBeds = \App\Models\RoomBed::all();
        $roomRules = \App\Models\RoomRule::all();
        
        return view('admin.rooms.create', compact('users', 'propertyTypes', 'spaceTypes', 'amenities', 'currencies', 'stepSettings', 'roomBeds', 'roomRules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'user_id'     => 'required|exists:users,id',
            'status'      => 'required|in:approved,pending,resubmit',
            'price'       => 'nullable|numeric',
            'resubmit_reason' => 'required_if:status,resubmit|nullable|string',
        ]);

        $data = $request->only([
            'title', 'name', 'description', 'property_type_id', 'space_type_id',
            'accommodation', 'address', 'city', 'state', 'country',
            'price', 'user_id', 'status',
            'booking_type', 'cancellation_policy', 'free_cancellation_days', 'cancellation_fee', 'selected_rules', 'checkout_policy'
        ]);
        $data['custom_cancellation'] = $request->has('custom_cancellation') ? 1 : 0;

        $room = Room::create($data);

        // Location details
        $room->roomLocation()->updateOrCreate(
            ['room_id' => $room->id],
            [
                'location_name' => $request->address,
                'city'          => $request->city,
                'state'         => $request->state,
                'country'       => $request->country,
            ]
        );

        // Price details
        $priceData = $request->only([
            'price', 'currency', 'is_tax_included', 'tax_amount', 'tax_type', 'security_deposit'
        ]);
        if ($request->filled('discounts')) {
            $discounts = $request->discounts;
            $priceData['discounts'] = is_string($discounts) ? json_decode($discounts, true) : $discounts;
        }
        $room->roomPrice()->create($priceData);

        // Sleeping arrangements
        if ($request->has('bedrooms_count')) {
            $room->update(['bedrooms_count' => $request->bedrooms_count]);
        }
        if ($request->filled('bedroom_allocations')) {
            $allocations = json_decode($request->bedroom_allocations, true) ?: [];
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
        }

        // Food & Services Enhancements
        if ($request->filled('enhancements')) {
            $enhancements = json_decode($request->enhancements, true) ?: [];
            $room->enhancements()->delete();
            foreach ($enhancements as $item) {
                if (!empty($item['item_name'])) {
                    $room->enhancements()->create([
                        'type' => $item['type'] ?? 'breakfast',
                        'item_name' => $item['item_name'],
                        'price' => $item['price'] ?? 0,
                        'currency' => 'USD',
                        'is_active' => true,
                        'is_per_guest' => filter_var($item['is_per_guest'] ?? false, FILTER_VALIDATE_BOOLEAN)
                    ]);
                }
            }
        }

        // Amenities
        if ($request->filled('amenity_ids')) {
            $room->amenities()->sync($request->amenity_ids);
        }

        // Photos
        if ($request->hasFile('photos')) {
            $captions = $request->input('photo_captions', []);
            foreach ($request->file('photos') as $index => $photo) {
                if ($photo && $photo->isValid()) {
                    $path = $photo->store('room_photos', 'public');
                    $room->photos()->create([
                        'photo_path' => $path,
                        'description' => $captions[$index] ?? null
                    ]);
                }
            }
        }

        if ($request->status === 'resubmit' && $request->resubmit_reason) {
            RoomResubmitReason::create([
                'room_id' => $room->id,
                'reason'  => $request->resubmit_reason,
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Room created successfully!',
                'redirect' => route('admin.rooms.index'),
            ]);
        }

        return redirect()->route('admin.rooms.index')->with('success', 'Room created successfully!');
    }

    public function edit(Room $room)
    {
        $users = User::all();
        $propertyTypes = PropertyType::all();
        $spaceTypes = SpaceType::all();
        $amenities = Amenity::all();
        $currencies = Currency::all();
        $stepSettings = RoomStepSetting::all()->keyBy('step_key');
        $roomBeds = \App\Models\RoomBed::all();
        $roomRules = \App\Models\RoomRule::all();
        $room->load(['resubmitReason', 'amenities', 'roomPrice', 'roomLocation', 'photos', 'bedroomBeds', 'enhancements']);

        return view('admin.rooms.create', compact('room', 'users', 'propertyTypes', 'spaceTypes', 'amenities', 'currencies', 'stepSettings', 'roomBeds', 'roomRules'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'user_id'     => 'required|exists:users,id',
            'status'      => 'required|in:approved,pending,resubmit',
            'price'       => 'nullable|numeric',
            'resubmit_reason' => 'required_if:status,resubmit|nullable|string',
        ]);

        $oldStatus = $room->status;

        $data = $request->only([
            'title', 'name', 'description', 'property_type_id', 'space_type_id',
            'accommodation', 'address', 'city', 'state', 'country',
            'price', 'user_id', 'status',
            'booking_type', 'cancellation_policy', 'free_cancellation_days', 'cancellation_fee', 'selected_rules', 'checkout_policy'
        ]);
        $data['custom_cancellation'] = $request->has('custom_cancellation') ? 1 : 0;

        $room->update($data);

        // Send approval email if status changed to approved
        if ($oldStatus !== 'approved' && $request->status === 'approved') {
            try {
                dispatch(function () use ($room) {
                    \App\Http\Controllers\EmailController::sendRoomApproved($room);
                })->afterResponse();
            } catch (\Exception $e) {
                \Log::error('Room approved email failed: ' . $e->getMessage());
            }
        }

        // Notify Room Owner on status change
        if ($oldStatus !== $request->status && $room->user) {
            $reasonText = $request->status === 'resubmit' ? $request->resubmit_reason : null;
            $room->user->notify(new \App\Notifications\RoomStatusUpdatedNotification($room, $request->status, $reasonText));
        }

        // Location details
        $room->roomLocation()->updateOrCreate(
            ['room_id' => $room->id],
            [
                'location_name' => $request->address,
                'city'          => $request->city,
                'state'         => $request->state,
                'country'       => $request->country,
            ]
        );

        // Price details
        $priceData = $request->only(['price', 'currency', 'is_tax_included', 'tax_amount', 'tax_type', 'security_deposit']);
        if ($request->filled('discounts')) {
            $discounts = $request->discounts;
            $priceData['discounts'] = is_string($discounts) ? json_decode($discounts, true) : $discounts;
        }
        $room->roomPrice()->updateOrCreate(
            ['room_id' => $room->id],
            $priceData
        );

        // Sleeping arrangements
        if ($request->has('bedrooms_count')) {
            $room->update(['bedrooms_count' => $request->bedrooms_count]);
        }
        if ($request->filled('bedroom_allocations')) {
            $allocations = json_decode($request->bedroom_allocations, true) ?: [];
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
        }

        // Food & Services Enhancements
        if ($request->filled('enhancements')) {
            $enhancements = json_decode($request->enhancements, true) ?: [];
            $room->enhancements()->delete();
            foreach ($enhancements as $item) {
                if (!empty($item['item_name'])) {
                    $room->enhancements()->create([
                        'type' => $item['type'] ?? 'breakfast',
                        'item_name' => $item['item_name'],
                        'price' => $item['price'] ?? 0,
                        'currency' => 'USD',
                        'is_active' => true,
                        'is_per_guest' => filter_var($item['is_per_guest'] ?? false, FILTER_VALIDATE_BOOLEAN)
                    ]);
                }
            }
        }

        // Amenities
        if ($request->has('amenity_ids')) {
            $room->amenities()->sync($request->amenity_ids ?? []);
        }

        // Photos (append new ones)
        if ($request->hasFile('photos')) {
            $captions = $request->input('photo_captions', []);
            foreach ($request->file('photos') as $index => $photo) {
                if ($photo && $photo->isValid()) {
                    $path = $photo->store('room_photos', 'public');
                    $room->photos()->create([
                        'photo_path' => $path,
                        'description' => $captions[$index] ?? null
                    ]);
                }
            }
        }

        // Update existing photo captions
        if ($request->has('existing_captions')) {
            foreach ($request->existing_captions as $photoId => $caption) {
                $room->photos()->where('id', $photoId)->update(['description' => $caption]);
            }
        }

        if ($request->status === 'resubmit' && $request->resubmit_reason) {
            RoomResubmitReason::updateOrCreate(
                ['room_id' => $room->id],
                ['reason'  => $request->resubmit_reason]
            );
        } else {
            $room->resubmitReason()->delete();
        }

        if ($request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Room updated successfully!',
                'redirect' => route('admin.rooms.index'),
            ]);
        }

        return redirect()->route('admin.rooms.index')->with('success', 'Room updated successfully!');
    }

    public function destroy(Room $room)
    {
        // Delete photo files from storage
        foreach ($room->photos as $photo) {
            if (!empty($photo->photo_path)) {
                Storage::disk('public')->delete($photo->photo_path);
            }
        }

        // room_photos cascade-deletes via FK, but explicit for safety
        $room->photos()->delete();

        if ($room->amenities()->exists()) {
            $room->amenities()->detach();
        }

        $room->resubmitReason()->delete();
        $room->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Room deleted successfully!']);
        }

        return redirect()->route('admin.rooms.index')->with('success', 'Room deleted successfully!');
    }

    public function deletePhoto($id)
    {
        $photo = RoomPhoto::findOrFail($id);
        
        if (!empty($photo->photo_path)) {
            Storage::disk('public')->delete($photo->photo_path);
        }
        
        $photo->delete();
        
        return response()->json(['success' => true]);
    }
}