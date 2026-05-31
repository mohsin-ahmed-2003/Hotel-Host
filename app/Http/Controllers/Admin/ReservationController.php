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
    public function index()
    {
        $reservations = Reservation::with(['room', 'user'])
            ->orderBy('id', 'desc')
            ->paginate(15);

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
