<!-- Premium Compact Room Card -->
@php
    $isWishlisted = false;
    if (session('user_id')) {
        $wishlistedRoomIds = $wishlistedRoomIds ?? \Illuminate\Support\Facades\Cache::remember('wishlist_ids_' . session('user_id'), 1, function () {
            return \App\Models\Wishlist::where('user_id', session('user_id'))->pluck('room_id')->toArray();
        });
        $isWishlisted = in_array($room->id, $wishlistedRoomIds);
    }
@endphp
<div class="room-card" onclick="window.location.href='{{ route('rooms.show', $room->id) }}'">
    <div class="room-image-slider">
        <!-- Wishlist Heart Button (Top-Right) -->
        <button class="wishlist-btn wishlist-btn-room-{{ $room->id }} {{ $isWishlisted ? 'active' : '' }}"
            onclick="toggleWishlist(event, {{ $room->id }})" title="Add to Wishlist">
            <i class="{{ $isWishlisted ? 'fas fa-heart' : 'far fa-heart' }}"
                style="{{ $isWishlisted ? 'color:#f87171;' : '' }}"></i>
        </button>

        <!-- Next/Prev Buttons (shown on hover) -->
        <button class="slider-btn prev-btn" onclick="prevSlide(event)">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="slider-btn next-btn" onclick="nextSlide(event)">
            <i class="fas fa-chevron-right"></i>
        </button>

        <!-- Slides Wrapper (Eager Loaded correctly via Storage path!) -->
        <div class="slides-container">
            @if($room->photos && $room->photos->count() > 0)
                @foreach($room->photos as $index => $photo)
                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="{{ $room->title }}"
                        class="slide-img {{ $index === 0 ? 'active' : '' }}">
                @endforeach
            @else
                <img src="{{ asset('images/image.png') }}" alt="{{ $room->title }}" class="slide-img active">
            @endif
        </div>
    </div>

    <div class="room-card-content">
        <!-- First Row: Room Name & Reviews (Right side) -->
        <div class="room-title-review-row">
            <a href="{{ route('rooms.show', $room->id) }}" class="room-title-link" onclick="event.stopPropagation();">
                <h3 class="room-card-title">{{ $room->title ?: ($room->name ?: 'N/A') }}</h3>
            </a>
            <div class="room-card-review" style="display: flex; gap: 2px; align-items: center;">
                @if($room->average_rating > 0)
                    @for($i = 1; $i <= 5; $i++)
                        @if($room->average_rating >= $i - 0.25)
                            <i class="fas fa-star" style="color: #fbbf24; font-size: 12px;"></i>
                        @elseif($room->average_rating >= $i - 0.75)
                            <i class="fas fa-star-half-alt" style="color: #fbbf24; font-size: 12px;"></i>
                        @else
                            <i class="far fa-star" style="color: #fbbf24; font-size: 12px;"></i>
                        @endif
                    @endfor
                    <span style="font-size: 11px; font-weight: 600; color: var(--body-muted); margin-left: 2px;">({{ $room->review_count }})</span>
                @else
                    <span class="star-icon" style="color: var(--body-muted);"><i class="far fa-star"></i></span>
                    <span class="no-review-text" style="margin-left: 4px; font-size: 12px; color: var(--body-muted);">No reviews</span>
                @endif
            </div>
        </div>

        <!-- Second Row: Room Type and Space Type -->
        <div class="room-type-space-row">
            <span class="room-property-type"><i class="fas fa-hotel" style="font-size:10px;"></i>
                {{ $room->propertyType->name ?? 'Room' }}</span>
            <span class="dot-separator">•</span>
            <span class="room-space-type"><i class="fas fa-door-open" style="font-size:10px;"></i>
                {{ $room->spaceType->name ?? 'Entire Space' }}</span>
        </div>

        <!-- Third Row: Price below type and space type -->
        <div class="room-card-price-row">
            <span class="price-val">{{ $room->currency_symbol }}{{ number_format($room->price, 0) }}</span>
            / night
        </div>

        <!-- Fourth Row: Compact Footer details -->
        <div class="room-card-footer">
            <span class="room-location">
                <i class="fas fa-map-marker-alt"></i>
                {{ $room->roomLocation->city ?? ($room->city ?: 'N/A') }}
            </span>
            <span class="room-guests">
                <i class="fas fa-users"></i> {{ $room->accommodation ?? 2 }} Guests
            </span>
        </div>
    </div>
</div>