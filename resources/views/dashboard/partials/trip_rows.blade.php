@foreach($trips as $trip)
    <a href="{{ route('user.reservations.itinerary', $trip->id) }}" class="trip-card" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 16px; align-items: center;">
            @if($trip->room && $trip->room->photos->first())
                <img src="{{ \Illuminate\Support\Facades\Storage::url($trip->room->photos->first()->photo_path) }}" class="trip-thumb" alt="Room">
            @else
                <div class="trip-thumb" style="background: var(--border); display:flex; align-items:center; justify-content:center;"><i class="fas fa-suitcase" style="color: var(--body-muted);"></i></div>
            @endif
            <div class="trip-info">
                <h4>{{ $trip->room->title ?? 'Deleted Listing' }}</h4>
                <div class="trip-dates">{{ $trip->checkin->format('M d') }} - {{ $trip->checkout->format('M d, Y') }}</div>
                <span class="status-pill status-{{ strtolower($trip->reservation_status ?? 'pending') }}">{{ ucfirst($trip->reservation_status ?? 'Pending') }}</span>
            </div>
        </div>
        <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
            <strong style="font-size: 18px; color: var(--body-text);">{{ $trip->room->currency_symbol ?? '$' }}{{ number_format($trip->total_amount, 2) }}</strong>
            <div style="font-size: 11px; color: var(--body-muted); text-transform: uppercase;">{{ $trip->payment_type }}</div>
            <span class="status-pill status-{{ strtolower($trip->status) }}">{{ ucfirst($trip->status) }}</span>
        </div>
    </a>
@endforeach
