<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check() && !session('user_id')) {
            return redirect()->route('auth');
        }

        $userId = Auth::id() ?? session('user_id');

        $isHost = Room::where('user_id', $userId)->exists();

        if ($isHost) {
            return $this->hostDashboard($request, $userId);
        } else {
            return $this->guestDashboard($userId);
        }
    }

    private function applyDateFilter($query, $filter, $column)
    {
        $now = Carbon::now();

        if ($filter === 'today') {
            $query->whereDate($column, $now->toDateString());
        } elseif ($filter === 'yesterday') {
            $query->whereDate($column, $now->subDay()->toDateString());
        } elseif ($filter === 'this_week') {
            $query->whereBetween($column, [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
        } elseif ($filter === 'next_week') {
            $query->whereBetween($column, [$now->copy()->addWeek()->startOfWeek(), $now->copy()->addWeek()->endOfWeek()]);
        } elseif ($filter === 'this_month') {
            $query->whereMonth($column, $now->month)->whereYear($column, $now->year);
        } elseif ($filter === 'previous_month') {
            $prev = $now->copy()->subMonth();
            $query->whereMonth($column, $prev->month)->whereYear($column, $prev->year);
        } elseif ($filter === 'this_year') {
            $query->whereYear($column, $now->year);
        }

        return $query;
    }

    private function hostDashboard(Request $request, $userId)
    {
        $filter = $request->get('filter', 'today'); 
        $filterColumn = $request->get('filter_column', 'created_at'); // created_at, checkin, checkout

        // HOST RESERVATIONS
        $rooms = Room::where('user_id', $userId)->pluck('id');
        $reservationsQuery = Reservation::with(['room', 'user'])
            ->whereIn('room_id', $rooms)
            ->orderBy($filterColumn, 'desc');

        $reservationsQuery = $this->applyDateFilter($reservationsQuery, $filter, $filterColumn);
        
        $allFiltered = (clone $reservationsQuery)->get();
        $totalEarnings = $allFiltered->where('status', 'success')->sum('total_amount');
        $totalReservations = $allFiltered->count();
        $totalNights = $allFiltered->sum(function($res) {
            return $res->checkin->diffInDays($res->checkout);
        });
        $bookedRooms = $allFiltered->unique('room_id')->count();
        $totalHostRooms = Room::where('user_id', $userId)->where('status', 'approved')->count();

        $reservations = $reservationsQuery->paginate(5, ['*'], 'reservations_page');

        // GUEST TRIPS
        $tripsQuery = Reservation::with('room')
            ->where('user_id', $userId)
            ->orderBy($filterColumn, 'desc');
            
        $tripsQuery = $this->applyDateFilter($tripsQuery, $filter, $filterColumn);
        $trips = $tripsQuery->paginate(5, ['*'], 'trips_page');

        if ($request->ajax()) {
            if ($request->has('load_trips')) {
                return view('dashboard.partials.trip_rows', compact('trips'))->render();
            }
            if ($request->has('load_reservations')) {
                return view('dashboard.partials.reservation_rows', compact('reservations'))->render();
            }
        }

        $isHost = true;
        return view('dashboard.index', compact('isHost', 'reservations', 'totalEarnings', 'totalReservations', 'totalNights', 'bookedRooms', 'totalHostRooms', 'filter', 'filterColumn', 'trips'));
    }

    private function guestDashboard($userId)
    {
        $trips = Reservation::with('room')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $isHost = false;
        return view('dashboard.index', compact('isHost', 'trips'));
    }
}
