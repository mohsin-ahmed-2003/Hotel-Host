<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Review;
use App\Mail\HostReviewNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReviewController extends Controller
{
    public function create(Reservation $reservation)
    {
        // Ensure only the user of the reservation can review
        if (auth()->id() !== $reservation->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure review isn't already submitted
        if ($reservation->review) {
            return redirect()->route('user.reservations')->with('error', 'You have already submitted a review for this reservation.');
        }

        return view('reviews.create', compact('reservation'));
    }

    public function store(Request $request, Reservation $reservation)
    {
        if (auth()->id() !== $reservation->user_id) {
            abort(403);
        }

        if ($reservation->review) {
            return redirect()->route('user.reservations')->with('error', 'Review already submitted.');
        }

        $validated = $request->validate([
            'room_space' => 'required|integer|min:1|max:5',
            'room_amenities' => 'required|integer|min:1|max:5',
            'room_arrangement' => 'required|integer|min:1|max:5',
            'dining_services' => 'nullable|integer|min:1|max:5',
            'room_cleanness' => 'required|integer|min:1|max:5',
            'stay_location' => 'required|integer|min:1|max:5',
            'description' => 'nullable|string|max:2000',
        ]);

        $review = new Review($validated);
        $review->user_id = auth()->id();
        $review->room_id = $reservation->room_id;
        $reservation->review()->save($review);

        // Send email to host
        if ($reservation->room->user->email) {
            \App\Http\Controllers\EmailController::sendHostReviewNotification($reservation, $review);
        }

        return redirect()->route('user.reservations')->with('success', 'Your review has been successfully submitted! Thank you.');
    }
}
