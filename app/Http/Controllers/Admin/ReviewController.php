<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Review;
use App\Mail\ReviewPromptMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function index()
    {
        // Get reservations where checkout date has crossed and status is accepted
        $reservations = Reservation::with(['room', 'user', 'review'])
            ->whereIn('reservation_status', ['accepted', 'confirmed'])
            ->where('status', 'success')
            ->whereDate('checkout', '<', Carbon::today())
            ->orderBy('checkout', 'desc')
            ->paginate(15);

        return view('admin.reviews.index', compact('reservations'));
    }

    public function sendEmail(Request $request, Reservation $reservation)
    {
        if ($reservation->review) {
            return back()->with('error', 'User has already given a review.');
        }

        if ($reservation->user->email) {
            \App\Http\Controllers\EmailController::sendReviewPrompt($reservation);
            $reservation->review_email_sent = true;
            $reservation->save();
        }

        return back()->with('success', 'Review prompt email sent to user.');
    }

    public function view(Reservation $reservation)
    {
        $review = $reservation->review;
        if (!$review) {
            return back()->with('error', 'No review available.');
        }
        return view('admin.reviews.show', compact('reservation', 'review'));
    }
}
