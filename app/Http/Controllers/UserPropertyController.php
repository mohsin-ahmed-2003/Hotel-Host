<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class UserPropertyController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        if (!$userId) return redirect()->route('auth');

        $rooms = Room::with(['photos', 'roomLocation', 'roomPrice'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $listedRooms = $rooms->filter(function($room) {
            return $room->is_approved;
        });

        $unlistedRooms = $rooms->filter(function($room) {
            return !$room->is_approved;
        });

        return view('user.properties', compact('listedRooms', 'unlistedRooms'));
    }
}
