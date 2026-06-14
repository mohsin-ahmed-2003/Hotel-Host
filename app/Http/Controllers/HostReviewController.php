<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class HostReviewController extends Controller
{
    public function show(Reservation $reservation)
    {
        // Ensure only the host of the room can see this
        if (auth()->id() !== $reservation->room->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $review = $reservation->review;
        if (!$review) {
            return redirect()->route('account.index')->with('error', 'No review found for this reservation.');
        }

        return view('host.reviews.show', compact('reservation', 'review'));
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        if (auth()->id() !== $reservation->room->user_id) {
            abort(403);
        }

        $review = $reservation->review;
        if (!$review) {
            return redirect()->route('account.index')->with('error', 'No review found.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $review->host_approved = ($validated['action'] === 'approve');
        $review->save();

        return redirect()->back()->with('success', 'Review status updated successfully.');
    }
}
