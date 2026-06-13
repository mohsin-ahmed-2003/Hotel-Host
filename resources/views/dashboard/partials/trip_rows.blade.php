@foreach($trips as $trip)
    <a href="{{ route('user.reservations.itinerary', $trip->id) }}" class="trip-card">
        @if($trip->room && $trip->room->photos->first())
            <img src="{{ \Illuminate\Support\Facades\Storage::url($trip->room->photos->first()->photo_path) }}" class="trip-thumb" alt="Room">
        @else
            <div class="trip-thumb" style="background: var(--border); display:flex; align-items:center; justify-content:center;"><i class="fas fa-suitcase" style="color: var(--body-muted);"></i></div>
        @endif
        <div class="trip-info">
            <h4>{{ $trip->room->title ?? 'Deleted Listing' }}</h4>
            <div class="trip-dates">{{ $trip->checkin->format('M d') }} - {{ $trip->checkout->format('M d, Y') }}</div>
            <span class="status-pill status-{{ strtolower($trip->status) }}">{{ $trip->status }}</span>
        </div>
    </a>
@endforeach
