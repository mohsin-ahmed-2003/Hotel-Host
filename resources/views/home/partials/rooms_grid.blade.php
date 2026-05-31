<div class="rooms-grid">
    @forelse($rooms as $room)
        @include('home.partials.room_card', ['room' => $room])
    @empty
        <div style="grid-column: 1/-1; text-align: center; padding: 48px; color: var(--body-muted);">
            <i class="{{ $fallbackIcon ?? 'fas fa-hotel' }}" style="font-size: 40px; margin-bottom: 12px; opacity: 0.5;"></i>
            <p>{{ $fallbackText ?? 'No properties found.' }}</p>
        </div>
    @endforelse
</div>
