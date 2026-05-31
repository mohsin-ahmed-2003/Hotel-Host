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
            @foreach($groups as $groupName => $items)
                <div class="folder-card" onclick="selectFolder('{{ addslashes($groupName) }}', '{{ md5($groupName) }}')" id="folder-{{ md5($groupName) }}">
                    <div class="folder-icon-wrap">
                        <div class="folder-tab"></div>
                        <div class="folder-back"></div>
                        <div class="folder-front">
                            <i class="fas fa-star folder-star"></i>
                        </div>
                    </div>
                    <div class="folder-info">
                        <h3 class="folder-name">{{ $groupName }}</h3>
                        <span class="folder-count"><i class="fas fa-bed"></i> {{ $items->count() }} {{ $items->count() === 1 ? 'Stay' : 'Stays' }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Dynamic Stays Display Segments -->
        @foreach($groups as $groupName => $items)
            <div class="group-rooms-segment" id="segment-{{ md5($groupName) }}" style="display:none; opacity:0; transition: opacity 0.3s ease;">
                <div class="segment-header">
                    <h2>Collection: <span class="group-highlight">{{ $groupName }}</span></h2>
                    <button class="back-folders-btn" onclick="showAllFolders()"><i class="fas fa-arrow-left"></i> View All Collections</button>
                </div>
                
                <div class="rooms-grid">
                    @foreach($items as $item)
                        @if($item->room)
                            <div class="wishlist-card-wrapper" id="wishlist-card-{{ $item->room->id }}" style="transition: all 0.4s ease;">
                                @include('home.partials.room_card', ['room' => $item->room])
                            </div>
                        @endif
                    @endforeach
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
}

body.dark-mode {
    --wishlist-bg: #0b0b14;
    --card-bg: #111122;
    --text-primary: #f1f5f9;
    --text-muted: #94a3b8;
    --border-color: rgba(255, 255, 255, 0.05);
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

.rooms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 28px;
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
                        wrapper.remove();
                        
                        // Check if segment is now empty
                        const segment = wrapper.closest('.group-rooms-segment');
                        if (segment) {
                            const remaining = segment.querySelectorAll('.wishlist-card-wrapper');
                            if (remaining.length === 0) {
                                // Reload page or fallback to show empty folder state
                                window.location.reload();
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
</script>
@endsection
