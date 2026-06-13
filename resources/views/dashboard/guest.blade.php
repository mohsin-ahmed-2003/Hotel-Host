<style>
    .guest-welcome {
        background: linear-gradient(135deg, var(--accent) 0%, #3b82f6 100%);
        border-radius: 20px;
        padding: 40px;
        color: #fff;
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(14, 165, 233, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .welcome-text h1 {
        font-size: 32px;
        font-weight: 800;
        margin: 0 0 10px 0;
        color: #fff;
    }
    .welcome-text p {
        font-size: 16px;
        opacity: 0.9;
        margin: 0;
        max-width: 500px;
        line-height: 1.5;
    }
    .explore-btn {
        background: #fff;
        color: var(--accent);
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .explore-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        color: var(--accent);
    }

    .table-container {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }
    .transaction-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .transaction-table th {
        background: rgba(0,0,0,0.02);
        padding: 16px 24px;
        font-size: 13px;
        font-weight: 700;
        color: var(--body-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border);
    }
    body.dark-mode .transaction-table th { background: rgba(255,255,255,0.02); }
    .transaction-table td {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
        color: var(--body-text);
        font-size: 15px;
        vertical-align: middle;
    }
    .transaction-table tr:last-child td { border-bottom: none; }
    
    .status-pill {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-transform: capitalize;
    }
    .status-success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .status-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .status-failed { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

    .room-info { display: flex; align-items: center; gap: 12px; }
    .room-thumb { width: 56px; height: 56px; border-radius: 12px; object-fit: cover; }
    .room-name { font-weight: 700; color: var(--body-text); font-size: 16px; margin-bottom: 4px; }
    .room-location { font-size: 13px; color: var(--body-muted); }

    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-state i { font-size: 48px; color: var(--body-muted); margin-bottom: 16px; opacity: 0.5; }
    .empty-state h3 { font-size: 20px; font-weight: 700; margin-bottom: 8px; color: var(--body-text); }
</style>

<div class="guest-welcome">
    <div class="welcome-text">
        <h1>Welcome back, {{ auth()->user()->name ?? 'Guest' }}!</h1>
        <p>Ready for your next adventure? Explore thousands of unique stays and experiences across the globe.</p>
    </div>
    <a href="{{ route('homepage') }}" class="explore-btn">
        <i class="fas fa-search"></i> Explore Places
    </a>
</div>

<h2 style="font-size: 24px; font-weight: 800; margin-bottom: 20px; color: var(--body-text);">My Trips & Transactions</h2>

<div class="table-container">
    <table class="transaction-table">
        <thead>
            <tr>
                <th>Destination</th>
                <th>Dates</th>
                <th>Amount Paid</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trips as $trip)
                <tr>
                    <td>
                        <div class="room-info">
                            @if($trip->room && $trip->room->photos->first())
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($trip->room->photos->first()->photo_path) }}" class="room-thumb" alt="Room">
                            @else
                                <div class="room-thumb" style="background: var(--border); display:flex; align-items:center; justify-content:center;"><i class="fas fa-suitcase" style="color: var(--body-muted); font-size: 24px;"></i></div>
                            @endif
                            <div>
                                <div class="room-name">{{ $trip->room->title ?? 'Deleted Listing' }}</div>
                                <div class="room-location">
                                    <i class="fas fa-map-marker-alt" style="color: var(--accent); margin-right: 4px;"></i>
                                    {{ $trip->room->location->city ?? 'Unknown City' }}, {{ $trip->room->location->country ?? '' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $trip->checkin->format('M d') }} - {{ $trip->checkout->format('M d, Y') }}</div>
                        <div style="font-size: 13px; color: var(--body-muted);">{{ $trip->guests }} Guest(s)</div>
                    </td>
                    <td>
                        <strong style="font-size: 16px;">${{ number_format($trip->total_amount, 2) }}</strong>
                        <div style="font-size: 12px; color: var(--body-muted); text-transform: uppercase; margin-top: 2px;">{{ $trip->payment_type }}</div>
                    </td>
                    <td>
                        <span class="status-pill status-{{ strtolower($trip->status) }}">{{ $trip->status }}</span>
                    </td>
                    <td>
                        <a href="{{ route('user.reservations.itinerary', $trip->id) }}" class="btn btn-sm" style="background: rgba(14, 165, 233, 0.1); color: var(--accent); font-weight: 600; padding: 8px 16px; border-radius: 8px; text-decoration: none; display: inline-block;">View Itinerary</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-plane-slash"></i>
                            <h3>No trips found</h3>
                            <p style="color: var(--body-muted); margin-bottom: 20px;">You haven't booked any trips yet. Start exploring to find your perfect stay.</p>
                            <a href="{{ route('homepage') }}" class="btn" style="background: var(--accent); color: white; padding: 10px 24px; border-radius: 10px; text-decoration: none; font-weight: 600;">Find a Place to Stay</a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
