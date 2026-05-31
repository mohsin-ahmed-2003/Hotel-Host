<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Room;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display the My Wishlists dashboard page, grouping properties by collection name.
     */
    public function index()
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('auth')->with('error', 'Please log in to view your wishlist.');
        }

        // Fetch user's wishlist rooms with rich relations for preview cards
        $wishlistItems = Wishlist::with([
            'room.photos',
            'room.roomPrice',
            'room.roomLocation',
            'room.propertyType',
            'room.spaceType'
        ])
        ->where('user_id', $userId)
        ->get();

        $groups = $wishlistItems->groupBy('group_name');

        return view('wishlist.index', compact('groups'));
    }

    /**
     * Fetch all unique wishlist group names for the logged-in user (populates popup modal dropdowns).
     */
    public function getGroups()
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json([]);
        }

        $groups = Wishlist::where('user_id', $userId)
            ->distinct()
            ->pluck('group_name')
            ->values();

        return response()->json($groups);
    }

    /**
     * Dynamic Heart Icon Wishlist Toggle action.
     */
    public function toggle(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to manage your wishlist.',
                'require_login' => true
            ], 401);
        }

        $request->validate([
            'room_id' => 'required|integer|exists:rooms,id',
            'group_name' => 'nullable|string|max:50'
        ]);

        $roomId = $request->room_id;
        $groupName = trim($request->input('group_name', 'My Favorites'));
        if (empty($groupName)) {
            $groupName = 'My Favorites';
        }

        // Check if this room is already wishlisted by the user
        $existing = Wishlist::where('user_id', $userId)
            ->where('room_id', $roomId)
            ->first();

        if ($existing) {
            // Already wishlisted, so untoggle and delete directly!
            $existing->delete();
            return response()->json([
                'success' => true,
                'status' => 'removed',
                'message' => 'Room removed from wishlist.'
            ]);
        }

        // If no group name is specified in request, prompt the user to choose or create one
        if (!$request->has('group_name')) {
            return response()->json([
                'success' => true,
                'status' => 'prompt',
                'message' => 'Select or create a collection group.'
            ]);
        }

        $groupName = trim($request->input('group_name', 'My Favorites'));
        if (empty($groupName)) {
            $groupName = 'My Favorites';
        }

        // Otherwise, add it to the designated collection group
        Wishlist::create([
            'user_id' => $userId,
            'room_id' => $roomId,
            'group_name' => $groupName
        ]);

        return response()->json([
            'success' => true,
            'status' => 'added',
            'group' => $groupName,
            'message' => "Room added to collection: {$groupName}!"
        ]);
    }
}
