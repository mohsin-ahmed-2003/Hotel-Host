<div class="rooms-slider-container">
    <div class="rooms-grid">
    @forelse($rooms as $room)
        @include('home.partials.room_card', ['room' => $room])
    @empty
        <div style="grid-column: 1/-1; text-align: center; padding: 48px; color: var(--body-muted);">
            <i class="{{ $fallbackIcon ?? 'fas fa-hotel' }}" style="font-size: 40px; margin-bottom: 12px; opacity: 0.5;"></i>
            <p>{{ $fallbackText ?? 'No properties found.' }}</p>
        </div>
    @endforelse

    @if(isset($showSeeAll) && $showSeeAll || (isset($rooms) && count($rooms) >= 4))
        <!-- See All Card -->
        <a href="{{ route('search') }}" class="room-card see-all-card stagger-fade-in" style="text-decoration: none; justify-content: flex-start; animation-delay: {{ (count($rooms ?? []) % 4) * 0.1 }}s;">
            <div class="room-image-slider see-all-wrapper" style="display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: 1fr 1fr; gap: 8px; padding: 0; background: transparent; border: none; box-shadow: none;">
                @php
                    $seeAllRooms = \App\Models\Room::with('photos')->where('status', 'approved')->inRandomOrder()->take(4)->get();
                @endphp
                @foreach($seeAllRooms as $saRoom)
                    <div style="width: 100%; height: 100%; border-radius: 12px; overflow: hidden; position: relative;" class="slides-container">
                        <div class="skeleton-img-placeholder"></div>
                        @if($saRoom->photos && $saRoom->photos->count() > 0)
                            <img src="{{ asset('storage/' . $saRoom->photos->first()->photo_path) }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Room" loading="lazy" onload="this.closest('.slides-container').classList.add('loaded')">
                        @else
                            <img src="{{ asset('images/image.png') }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Fallback Room" loading="lazy" onload="this.closest('.slides-container').classList.add('loaded')">
                        @endif
                    </div>
                @endforeach
            </div>
            
            <div class="room-card-content" style="display: flex; align-items: center; justify-content: center; padding-top: 16px !important; margin-top: auto; padding-bottom: 24px !important;">
                <button type="button" style="background: #ff5a5f; color: white; border: none; border-radius: 24px; padding: 12px 24px; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: background 0.2s; width: 80%; justify-content: center;">
                    <i class="fas fa-th-large"></i> See all
                </button>
            </div>
        </a>
    @endif
    </div>
</div>
