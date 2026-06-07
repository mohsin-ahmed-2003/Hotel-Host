<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\WishlistGroup;
use App\Models\Room;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display the My Wishlists dashboard page, showing all groups and their wishlisted stays.
     */
    public function index()
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('auth')->with('error', 'Please log in to view your wishlist.');
        }

        // Fetch all of the user's custom collection folders
        $groups = WishlistGroup::where('user_id', $userId)->get();

        // Fetch all wishlisted room details
        $wishlistItems = Wishlist::with([
            'room.photos',
            'room.roomPrice',
            'room.roomLocation',
            'room.propertyType',
            'room.spaceType'
        ])
        ->where('user_id', $userId)
        ->get();

        $wishlistGrouped = $wishlistItems->groupBy('group_name');

        return view('wishlist.index', compact('groups', 'wishlistGrouped'));
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

        $groups = WishlistGroup::where('user_id', $userId)
            ->pluck('name')
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

        // Check if this room is already wishlisted by the user
        $existing = Wishlist::where('user_id', $userId)
            ->where('room_id', $roomId)
            ->first();

        if ($existing) {
            // Already wishlisted, so untoggle and delete directly!
            // Crucial: The group in wishlist_groups is NOT deleted.
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

        // Ensure the folder exists in wishlist_groups first
        $group = WishlistGroup::firstOrCreate([
            'user_id' => $userId,
            'name' => $groupName
        ]);

        // Add the stay to the designated folder group
        Wishlist::create([
            'user_id' => $userId,
            'room_id' => $roomId,
            'group_name' => $group->name
        ]);

        return response()->json([
            'success' => true,
            'status' => 'added',
            'group' => $group->name,
            'message' => "Room added to collection: {$group->name}!"
        ]);
    }

    /**
     * Dedicated Action to delete a wishlist collection folder and all its associated stays.
     */
    public function deleteGroup(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to manage your collections.'
            ], 401);
        }

        $request->validate([
            'name' => 'required|string'
        ]);

        $groupName = trim($request->name);

        // Delete the folder group itself
        WishlistGroup::where('user_id', $userId)
            ->where('name', $groupName)
            ->delete();

        // Delete all wishlist stay items belonging to this folder group
        Wishlist::where('user_id', $userId)
            ->where('group_name', $groupName)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Collection folder '{$groupName}' successfully deleted."
        ]);
    }
}
