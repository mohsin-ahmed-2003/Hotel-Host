@extends('layouts.app')

@section('title', 'My Wishlists - Hotel Host')

@section('content')
<div class="wishlist-container">
    <div class="wishlist-header">
        <h1 class="wishlist-title"><i class="fa-solid fa-heart" style="color:#f87171; margin-right:12px; animation: pulse 2s infinite;"></i> My Wishlists</h1>
        <p class="wishlist-subtitle">Organize and manage your favorite stays in gorgeous curated collections.</p>
    </div>

    @if($groups->isEmpty())
        <div class="wishlist-empty-state">
            <div class="empty-icon-wrap">
                <i class="fa-solid fa-folder-open" style="font-size: 64px; color: rgba(248, 113, 113, 0.4); margin-bottom: 16px;"></i>
            </div>
            <h2>No Collections Created Yet</h2>
            <p>Browse rooms on our home page and click the heart icons to start your custom collection list!</p>
            <a href="/" class="explore-btn">Start Exploring</a>
        </div>
    @else
        <!-- Folders Grid -->
        <div class="folders-grid" id="foldersGrid">
            @foreach($groups as $group)
                @php
                    $staysCount = isset($wishlistGrouped[$group->name]) ? $wishlistGrouped[$group->name]->count() : 0;
                    $folderHash = md5($group->name);
                @endphp
                <div class="folder-card" onclick="selectFolder('{{ addslashes($group->name) }}', '{{ $folderHash }}')" id="folder-{{ $folderHash }}">
                    <div class="folder-icon-wrap">
                        <div class="folder-tab"></div>
                        <div class="folder-back"></div>
                        <div class="folder-front">
                            <i class="fas fa-star folder-star"></i>
                        </div>
                    </div>
                    <div class="folder-info">
                        <h3 class="folder-name">{{ $group->name }}</h3>
                        <span class="folder-count"><i class="fas fa-bed"></i> {{ $staysCount }} {{ $staysCount === 1 ? 'Stay' : 'Stays' }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Dynamic Stays Display Segments -->
        @foreach($groups as $group)
            @php
                $folderHash = md5($group->name);
                $items = $wishlistGrouped[$group->name] ?? collect([]);
            @endphp
            <div class="group-rooms-segment" id="segment-{{ $folderHash }}" style="display:none; opacity:0; transition: opacity 0.3s ease;">
                <div class="segment-header">
                    <h2>Collection: <span class="group-highlight">{{ $group->name }}</span></h2>
                    
                    <div style="display:flex; gap:12px;">
                        <button class="delete-collection-btn" onclick="deleteCollection('{{ addslashes($group->name) }}', '{{ $folderHash }}')">
                            <i class="fas fa-trash-alt"></i> Delete Collection
                        </button>
                        <button class="back-folders-btn" onclick="showAllFolders()"><i class="fas fa-arrow-left"></i> View All Collections</button>
                    </div>
                </div>
                
                <div class="rooms-grid rooms-grid-wrap">
                    @if($items->isEmpty())
                        <div class="empty-folder-inside" style="grid-column: 1 / -1; text-align: center; padding: 60px 24px; background: rgba(255,255,255,0.01); border-radius: 20px; border: 1px dashed var(--border-color);">
                            <i class="fa-solid fa-bed" style="font-size: 36px; color: var(--text-muted); opacity: 0.3; margin-bottom: 12px;"></i>
                            <p style="color: var(--text-muted); margin: 0; font-size: 14.5px; font-weight: 500;">No stays added to this collection yet.</p>
                        </div>
                    @else
                        @foreach($items as $item)
                            @if($item->room)
                                <div class="wishlist-card-wrapper" id="wishlist-card-{{ $item->room->id }}" style="transition: all 0.4s ease;">
                                    @include('home.partials.room_card', ['room' => $item->room])
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>

<style>
/* Base Layout & Dark-Mode support variables */
:root {
    --wishlist-bg: #f8fafc;
    --card-bg: #ffffff;
    --text-primary: #1e293b;
    --text-muted: #64748b;
    --border-color: rgba(0, 0, 0, 0.05);

    /* Room Card Compatibility Fallbacks */
    --border: rgba(0, 0, 0, 0.06);
    --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    --accent: #6366f1;
    --body-text: #1e293b;
    --body-muted: #64748b;
}

body.dark-mode {
    --wishlist-bg: #0b0b14;
    --card-bg: #1e1e30;
    --text-primary: #f1f5f9;
    --text-muted: #94a3b8;
    --border-color: rgba(255, 255, 255, 0.08);

    /* Room Card Dark-Mode Fallbacks */
    --border: rgba(255, 255, 255, 0.08);
    --accent: #818cf8;
    --body-text: #f1f5f9;
    --body-muted: #94a3b8;
}

body.dark-mode .folder-card {
    background: #273043;
    border-color: rgba(255, 255, 255, 0.12);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
}

body.dark-mode .folder-card:hover {
    background: #313c54;
    border-color: rgba(248, 113, 113, 0.5);
}

.wishlist-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 24px 80px 24px;
    min-height: 80vh;
}

.wishlist-header {
    margin-bottom: 40px;
}

.wishlist-title {
    font-size: 32px;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
}

.wishlist-subtitle {
    font-size: 15px;
    color: var(--text-muted);
    margin: 0;
}

/* Empty State */
.wishlist-empty-state {
    text-align: center;
    padding: 80px 24px;
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    max-width: 500px;
    margin: 40px auto;
}

.explore-btn {
    display: inline-block;
    background: #f87171;
    color: #ffffff;
    padding: 12px 28px;
    border-radius: 30px;
    font-weight: 700;
    font-size: 14.5px;
    text-decoration: none;
    margin-top: 24px;
    transition: all 0.25s;
    box-shadow: 0 4px 14px rgba(248, 113, 113, 0.3);
}

.explore-btn:hover {
    background: #ef4444;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(248, 113, 113, 0.4);
}

/* 3D Animated CSS Folders Grid */
.folders-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 24px;
    margin-top: 20px;
}

.folder-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 24px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    display: flex;
    flex-direction: column;
    align-items: center;
    perspective: 1000px;
}

.folder-card:hover {
    transform: translateY(-5px);
    border-color: rgba(248, 113, 113, 0.3);
    box-shadow: 0 12px 28px rgba(248, 113, 113, 0.08);
}

/* CSS Folder Design */
.folder-icon-wrap {
    position: relative;
    width: 80px;
    height: 60px;
    margin-bottom: 16px;
    transform-style: preserve-3d;
}

.folder-tab {
    position: absolute;
    top: -6px;
    left: 8px;
    width: 28px;
    height: 12px;
    background: #ef4444;
    border-radius: 4px 4px 0 0;
    transition: background 0.25s;
}

.folder-back {
    position: absolute;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f87171, #ef4444);
    border-radius: 8px;
    z-index: 1;
}

.folder-front {
    position: absolute;
    bottom: 0;
    width: 100%;
    height: 86%;
    background: linear-gradient(135deg, #fca5a5, #f87171);
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 3;
    transform-origin: bottom;
    transition: transform 0.35s cubic-bezier(0.25, 1, 0.5, 1);
}

.folder-star {
    color: #ffffff;
    font-size: 15px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.15);
}

.folder-card:hover .folder-front {
    transform: rotateX(-30deg);
}

.folder-info {
    text-align: center;
}

.folder-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 4px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 170px;
}

.folder-count {
    font-size: 13px;
    color: var(--text-muted);
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

/* Dynamic Segment & Custom Cards Display */
.group-rooms-segment {
    margin-top: 20px;
}

.segment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 16px;
}

.segment-header h2 {
    font-size: 22px;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0;
}

.group-highlight {
    color: #f87171;
}

.delete-collection-btn {
    background: rgba(239, 68, 68, 0.08);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #ef4444;
    padding: 10px 20px;
    border-radius: 30px;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.25s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.delete-collection-btn:hover {
    background: #ef4444;
    border-color: #ef4444;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
}

.back-folders-btn {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
    padding: 10px 20px;
    border-radius: 30px;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.25s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.back-folders-btn:hover {
    border-color: #f87171;
    color: #f87171;
    transform: translateX(-2px);
}



/* Custom fade outs for heart actions in real time */
.wishlist-card-wrapper.removed {
    opacity: 0 !important;
    transform: scale(0.8) translateY(20px) !important;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>

<script>
    // Expand folder segment details in a seamless SPA feel
    function selectFolder(groupName, hash) {
        const folders = document.getElementById('foldersGrid');
        if (folders) {
            folders.style.display = 'none';
        }
        
        const targetSegment = document.getElementById(`segment-${hash}`);
        if (targetSegment) {
            targetSegment.style.display = 'block';
            setTimeout(() => {
                targetSegment.style.opacity = '1';
            }, 50);
        }
    }

    // Hide drawer and reveal original collection grid
    function showAllFolders() {
        const segments = document.querySelectorAll('.group-rooms-segment');
        segments.forEach(segment => {
            segment.style.opacity = '0';
            segment.style.display = 'none';
        });

        const folders = document.getElementById('foldersGrid');
        if (folders) {
            folders.style.display = 'grid';
        }
    }

    // Refactor heart active clicks to fade out in real-time on this gallery
    // Note: Folder count is updated or left active without auto-deleting the collection folder!
    function toggleWishlist(event, roomId) {
        event.stopPropagation();
        event.preventDefault();
        
        const btn = event.currentTarget;
        if (!btn) return;

        // Perform AJAX delete request
        fetch('/wishlist/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ room_id: roomId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.status === 'removed') {
                // Dynamically fade out the wrapper
                const wrapper = document.getElementById(`wishlist-card-${roomId}`);
                if (wrapper) {
                    wrapper.classList.add('removed');
                    setTimeout(() => {
                        // Crucial: Cache segment reference BEFORE deleting elements from the DOM!
                        const segment = wrapper.closest('.group-rooms-segment');
                        wrapper.remove();
                        
                        if (segment) {
                            const remaining = segment.querySelectorAll('.wishlist-card-wrapper');
                            if (remaining.length === 0) {
                                const grid = segment.querySelector('.rooms-grid');
                                if (grid) {
                                    grid.innerHTML = `
                                        <div class="empty-folder-inside" style="grid-column: 1 / -1; text-align: center; padding: 60px 24px; background: rgba(255,255,255,0.01); border-radius: 20px; border: 1px dashed var(--border-color);">
                                            <i class="fa-solid fa-bed" style="font-size: 36px; color: var(--text-muted); opacity: 0.3; margin-bottom: 12px;"></i>
                                            <p style="color: var(--text-muted); margin: 0; font-size: 14.5px; font-weight: 500;">No stays added to this collection yet.</p>
                                        </div>
                                    `;
                                }
                            }
                            
                            // Dynamically update the count inside folder card list
                            const segmentId = segment.id.replace('segment-', '');
                            const folderCountEl = document.querySelector(`#folder-${segmentId} .folder-count`);
                            if (folderCountEl) {
                                const newCount = remaining.length;
                                folderCountEl.innerHTML = `<i class="fas fa-bed"></i> ${newCount} ${newCount === 1 ? 'Stay' : 'Stays'}`;
                            }
                        }
                    }, 400);
                }
            }
        })
        .catch(err => {
            console.error("Failed to delete wishlist item:", err);
        });
    }

    // Explicitly delete collection folders and sync grid UI
    function deleteCollection(groupName, hash) {
        if (!confirm(`Are you sure you want to permanently delete the collection folder "${groupName}"?`)) {
            return;
        }

        fetch('/wishlist/group', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name: groupName })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Fade out drawer
                const segment = document.getElementById(`segment-${hash}`);
                if (segment) {
                    segment.style.opacity = '0';
                    setTimeout(() => {
                        segment.style.display = 'none';
                    }, 300);
                }

                // Shrink and fade out folder card from folders list
                const folderCard = document.getElementById(`folder-${hash}`);
                if (folderCard) {
                    folderCard.style.transform = 'scale(0.8)';
                    folderCard.style.opacity = '0';
                    setTimeout(() => {
                        folderCard.remove();
                        
                        // Show all folders
                        const folders = document.getElementById('foldersGrid');
                        if (folders) {
                            folders.style.display = 'grid';
                            
                            const remaining = folders.querySelectorAll('.folder-card');
                            if (remaining.length === 0) {
                                window.location.reload();
                            }
                        }
                    }, 300);
                }
            } else {
                alert(data.message || "An error occurred.");
            }
        })
        .catch(err => {
            console.error("Delete group error:", err);
        });
    }

    // Image Slider Navigation - Prev Slide helper
    function prevSlide(event) {
        event.stopPropagation();
        event.preventDefault();
        const btn = event.currentTarget;
        const slider = btn.closest('.room-image-slider');
        if (!slider) return;
        const container = slider.querySelector('.slides-container');
        if (!container) return;
        const slides = container.querySelectorAll('.slide-img');
        if (slides.length <= 1) return;

        let activeIndex = -1;
        slides.forEach((slide, index) => {
            if (slide.classList.contains('active')) {
                activeIndex = index;
            }
        });

        if (activeIndex !== -1) {
            slides[activeIndex].classList.remove('active');

            let nextIndex = activeIndex - 1;
            if (nextIndex < 0) nextIndex = slides.length - 1;

            slides[nextIndex].classList.add('active');
        }
    }

    // Image Slider Navigation - Next Slide helper
    function nextSlide(event) {
        event.stopPropagation();
        event.preventDefault();
        const btn = event.currentTarget;
        const slider = btn.closest('.room-image-slider');
        if (!slider) return;
        const container = slider.querySelector('.slides-container');
        if (!container) return;
        const slides = container.querySelectorAll('.slide-img');
        if (slides.length <= 1) return;

        let activeIndex = -1;
        slides.forEach((slide, index) => {
            if (slide.classList.contains('active')) {
                activeIndex = index;
            }
        });

        if (activeIndex !== -1) {
            slides[activeIndex].classList.remove('active');

            let nextIndex = activeIndex + 1;
            if (nextIndex >= slides.length) nextIndex = 0;

            slides[nextIndex].classList.add('active');
        }
    }
</script>
@endsection
