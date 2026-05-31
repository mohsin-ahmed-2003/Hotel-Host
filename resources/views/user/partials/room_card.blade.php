@php
    $missingSteps = $room->countMissingSteps();
    $isIncomplete = $missingSteps > 0;
    $thumbnail = $room->photos->first() ? asset('storage/' . $room->photos->first()->photo_path) : asset('images/placeholder-room.jpg');
@endphp

<div class="room-row-premium {{ $isIncomplete ? 'resubmit' : $room->status }}">
    <!-- LEFT: Room Image -->
    <div class="room-visual-box">
        <img src="{{ $thumbnail }}" class="room-img-top" alt="{{ $room->title }}">
        @if($room->status === 'approved')
            <div class="live-indicator">LIVE</div>
        @endif
    </div>

    <!-- CENTER: Content -->
    <div class="room-details-main">
        <div class="title-status-line">
            <h3 class="room-title-text">{{ $room->title ?: ($room->name ?: 'Untitled Property') }}</h3>
            @if(!$isIncomplete)
                <div class="badge-pill {{ $room->status }}">
                    <span class="dot"></span>
                    {{ ucfirst($room->status) }}
                </div>
            @endif
        </div>
        <div class="room-meta-info">
            <span class="meta-item"><i class="fas fa-map-marker-alt"></i> {{ $room->city ?: 'Location pending' }}</span>
            <span class="meta-item"><i class="fas fa-home"></i> {{ optional($room->propertyType)->name ?: 'Property Type pending' }}</span>
        </div>
        <p class="room-snippet">{{ Str::limit($room->description ?: 'No description provided yet.', 80) }}</p>
    </div>

    <!-- RIGHT: Actions -->
    <div class="room-side-actions">
        @if($isIncomplete)
            <a href="{{ route('host.step', ['room' => $room->id, 'step' => 1]) }}" class="btn-continue-hosting">
                <div class="btn-label">Continue Hosting</div>
                <div class="btn-subtext">{{ $missingSteps }} steps left</div>
            </a>
        @else
            <div class="action-buttons-group">
                <a href="{{ route('host.step', ['room' => $room->id, 'step' => 1]) }}" class="btn-action-outline">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                @if($room->status === 'approved')
                    <a href="{{ url('/rooms/' . $room->id) }}" class="btn-action-primary" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        <span>View</span>
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<style>
    .room-row-premium {
        display: flex;
        align-items: center;
        padding: 24px 32px;
        border-bottom: 1px solid var(--border);
        background: var(--card-bg);
        transition: all 0.3s ease;
        gap: 32px;
    }

    .room-row-premium:hover {
        background: var(--bg-secondary);
    }

    .room-row-premium:last-child {
        border-bottom: none;
    }

    /* Status-based borders */
    .room-row-premium.approved {
        border-left: 4px solid #22c55e;
    }

    .room-row-premium.pending {
        border-left: 4px solid #3b82f6;
    }

    .room-row-premium.resubmit {
        border-left: 4px solid #ef4444;
    }

    /* Image Box */
    .room-visual-box {
        position: relative;
        flex-shrink: 0;
    }

    .room-img-top {
        width: 160px;
        height: 100px;
        object-fit: cover;
        border-radius: 18px;
        display: block;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .room-row-premium:hover .room-img-top {
        transform: scale(1.04);
    }

    .live-indicator {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #22c55e;
        color: #fff;
        font-size: 9px;
        font-weight: 900;
        padding: 2px 8px;
        border-radius: 6px;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 8px rgba(34, 197, 94, 0.3);
    }

    /* Content Area */
    .room-details-main {
        flex-grow: 1;
        min-width: 0;
    }

    .title-status-line {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 6px;
        flex-wrap: wrap;
    }

    .room-title-text {
        font-size: 18px;
        font-weight: 800;
        color: var(--body-text);
        margin: 0;
        letter-spacing: -0.4px;
    }

    .badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-pill.approved {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
    }

    .badge-pill.pending {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .badge-pill.resubmit {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .badge-pill .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .room-meta-info {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin: 8px 0 12px 0;
    }

    .meta-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        background: rgba(99, 102, 241, 0.06); /* Soft Indigo */
        border: 1px solid rgba(99, 102, 241, 0.12);
        border-radius: 20px;
        color: #4f46e5;
        font-size: 11.5px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    body.dark-mode .meta-item {
        background: rgba(167, 139, 250, 0.06); /* Soft Purple */
        border-color: rgba(167, 139, 250, 0.12);
        color: #c084fc;
    }

    .meta-item:hover {
        transform: translateY(-1px);
        background: rgba(99, 102, 241, 0.1);
        border-color: rgba(99, 102, 241, 0.22);
    }

    body.dark-mode .meta-item:hover {
        background: rgba(167, 139, 250, 0.1);
        border-color: rgba(167, 139, 250, 0.22);
    }

    .meta-item i {
        font-size: 12px;
        color: currentColor;
    }

    .room-snippet {
        font-size: 14px;
        color: var(--body-muted);
        margin: 0;
        line-height: 1.5;
    }

    /* Action Area */
    .room-side-actions {
        flex-shrink: 0;
        display: flex;
        justify-content: flex-end;
        min-width: 200px;
    }

    .action-buttons-group {
        display: flex;
        gap: 12px;
    }

    .btn-action-outline {
        padding: 10px 18px;
        border-radius: 12px;
        border: 1.5px solid var(--border);
        color: var(--body-text);
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-action-outline:hover {
        background: var(--bg-secondary);
        border-color: var(--accent);
        color: var(--accent);
    }

    .btn-action-primary {
        padding: 10px 18px;
        border-radius: 12px;
        background: var(--accent);
        color: #fff !important;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-action-primary:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .btn-continue-hosting {
        background: #ff385c;
        color: #fff !important;
        padding: 12px 24px;
        border-radius: 14px;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s;
        box-shadow: 0 4px 10px rgba(255, 56, 92, 0.2);
    }

    .btn-continue-hosting:hover {
        background: #e31c5f;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(255, 56, 92, 0.3);
    }

    .btn-label {
        font-size: 14px;
        font-weight: 800;
        display: block;
        margin-bottom: 2px;
    }

    .btn-subtext {
        font-size: 11px;
        font-weight: 600;
        opacity: 0.9;
        text-transform: uppercase;
    }

    @media (max-width: 850px) {
        .room-row-premium {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
            padding: 24px;
        }

        .room-visual-box,
        .room-img-top {
            width: 100%;
            height: 180px;
        }

        .room-side-actions {
            width: 100%;
        }

        .btn-continue-hosting,
        .action-buttons-group {
            width: 100%;
        }

        .action-buttons-group .btn-action-outline,
        .action-buttons-group .btn-action-primary {
            flex: 1;
            justify-content: center;
        }
    }
</style>