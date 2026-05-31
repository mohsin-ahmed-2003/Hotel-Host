<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class UserReservationController extends Controller
{
    /**
     * Display a listing of trips (where the user is the guest).
     */
    public function trips(Request $request)
    {
        $tab = $request->get('tab', 'upcoming');
        $query = Reservation::where('user_id', session('user_id'))
            ->with(['room.photos', 'room.user']);

        if ($tab === 'previous') {
            $query->whereDate('checkout', '<', now());
        } elseif ($tab === 'current') {
            $query->whereDate('checkin', '<=', now())
                  ->whereDate('checkout', '>=', now());
        } else {
            // upcoming is default
            $query->whereDate('checkin', '>', now());
        }

        $reservations = $query->orderBy('created_at', 'desc')->paginate(12)->appends(['tab' => $tab]);

        return view('user.trips', compact('reservations', 'tab'));
    }

    /**
     * Display a listing of reservations (where the user is the host).
     */
    public function reservations(Request $request)
    {
        $tab = $request->get('tab', 'upcoming');
        $query = Reservation::whereHas('room', function($query) {
                $query->where('user_id', session('user_id'));
            })
            ->with(['room.photos', 'user']);

        if ($tab === 'previous') {
            $query->whereDate('checkout', '<', now());
        } elseif ($tab === 'current') {
            $query->whereDate('checkin', '<=', now())
                  ->whereDate('checkout', '>=', now());
        } else {
            // upcoming is default
            $query->whereDate('checkin', '>', now());
        }

        $reservations = $query->orderBy('created_at', 'desc')->paginate(12)->appends(['tab' => $tab]);

        return view('user.reservations', compact('reservations', 'tab'));
    }

    /**
     * Display the itinerary for a specific reservation.
     */
    public function itinerary(Reservation $reservation)
    {
        // Check if the user is either the host or the guest
        if (session('user_id') != $reservation->user_id && session('user_id') != $reservation->room->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $reservation->load(['room.photos', 'room.user', 'room.roomLocation', 'user']);

        return view('user.itinerary', compact('reservation'));
    }

    /**
     * Display the receipt for a specific reservation.
     */
    public function receipt(Reservation $reservation)
    {
        // Check if the user is either the host or the guest
        if (session('user_id') != $reservation->user_id && session('user_id') != $reservation->room->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $reservation->load(['room.user', 'user']);

        return view('user.receipt', compact('reservation'));
    }
}
