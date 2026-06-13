<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations.
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['room', 'user'])->orderBy('id', 'desc');

        if ($request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('id', $searchTerm)
                  ->orWhereHas('user', function($u) use ($searchTerm) {
                      $u->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('email', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('room', function($r) use ($searchTerm) {
                      $r->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $reservations = $query->paginate(15);

        return view('admin.reservations.index', compact('reservations'));
    }

    /**
     * Display the specified reservation.
     */
    public function show(Reservation $reservation)
    {
        // Eager load relationships for the view
        $reservation->load(['room', 'user']);
        
        return view('admin.reservations.show', compact('reservation'));
    }

    /**
     * Send email to guest or host.
     */
    public function sendEmail(Request $request, Reservation $reservation)
    {
        $request->validate([
            'to_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message_body' => 'required|string',
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($request->to_email)
                ->send(new \App\Mail\ReservationContactMail($request->subject, $request->message_body));
                
            return redirect()->back()->with('success', 'Email sent successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send email. Ensure your mail credentials are set up. Error: ' . $e->getMessage());
        }
    }
}
