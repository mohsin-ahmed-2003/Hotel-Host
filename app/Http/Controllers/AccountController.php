<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Fetch reviews left for the host's properties
        $hostReviews = Review::with(['user', 'room'])
            ->whereHas('room', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->get();

        return view('account.index', compact('hostReviews'));
    }

    public function approveReview(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        
        // Verify the review belongs to a room owned by the auth user
        if ($review->room->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $review->host_approved = true;
        $review->save();

        return redirect()->back()->with('success', 'Review has been approved and published.');
    }
}
