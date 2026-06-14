@foreach($reservations as $res)
    <a href="{{ route('user.reservations.itinerary', $res->id) }}" class="trip-card" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 16px; align-items: center;">
            @if($res->room && $res->room->photos->first())
                <img src="{{ \Illuminate\Support\Facades\Storage::url($res->room->photos->first()->photo_path) }}" class="trip-thumb" alt="Room">
            @else
                <div class="trip-thumb" style="background: var(--border); display:flex; align-items:center; justify-content:center;"><i class="fas fa-home" style="color: var(--body-muted)"></i></div>
            @endif
            <div class="trip-info">
                <h4>{{ $res->room->title ?? 'Deleted Room' }}</h4>
                <div class="trip-dates">
                    Guest: <strong style="color: var(--body-text);">{{ $res->user->name ?? 'Unknown' }}</strong><br>
                    <span style="display: inline-block; margin-top: 4px;">{{ $res->checkin->format('M d') }} - {{ $res->checkout->format('M d, Y') }}</span>
                </div>
                <span class="status-pill status-{{ strtolower($res->reservation_status ?? 'pending') }}">{{ ucfirst($res->reservation_status ?? 'Pending') }}</span>
            </div>
        </div>
        <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
            <strong style="font-size: 18px; color: var(--body-text);">{{ $res->room->currency_symbol ?? '$' }}{{ number_format($res->total_amount, 2) }}</strong>
            <div style="font-size: 11px; color: var(--body-muted); text-transform: uppercase;">{{ $res->payment_type }}</div>
            <span class="status-pill status-{{ strtolower($res->status) }}">{{ ucfirst($res->status) }}</span>
        </div>
    </a>
@endforeach
