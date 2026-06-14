@extends('admin.layout')

@section('title', 'Review Details')

@section('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .review-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    @media (max-width: 992px) {
        .review-grid {
            grid-template-columns: 1fr;
        }
    }

    .rating-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .rating-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        font-size: 15px;
    }
    
    .rating-item:last-child {
        border-bottom: none;
    }

    .rating-label {
        font-weight: 600;
        color: var(--text);
    }

    .rating-value {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 700;
        color: var(--text);
    }

    .star-icon {
        color: #f59e0b;
        font-size: 16px;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-item {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-size: 13px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .info-value {
        font-size: 15px;
        font-weight: 500;
        color: var(--text);
    }
    
    .description-box {
        padding: 20px;
        color: var(--text);
        font-size: 15px;
        line-height: 1.6;
        background: var(--bg);
        border-radius: 8px;
        margin: 20px;
        border: 1px solid var(--border);
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 style="font-size: 24px; font-weight: 700; margin: 0;">Review Details #{{ $review->id }}</h1>
        <div style="font-size: 14px; color: var(--text-muted); margin-top: 4px;">Detailed breakdown of the guest's feedback</div>
    </div>
    <a href="{{ route('admin.reviews.index') }}" class="btn-custom btn-secondary-solid">
        <i class="fas fa-arrow-left"></i> Back to Reviews
    </a>
</div>

<div class="review-grid">
    <!-- Left Column: Ratings & Description -->
    <div class="left-col">
        <div class="card mb-4">
            <div class="card-header">
                <div class="card-title">Ratings Breakdown</div>
            </div>
            <div class="card-body" style="padding:0;">
                <ul class="rating-list">
                    <li class="rating-item">
                        <span class="rating-label">Room Space</span>
                        <span class="rating-value"><i class="fas fa-star star-icon"></i> {{ $review->room_space }} / 5</span>
                    </li>
                    <li class="rating-item">
                        <span class="rating-label">Room Amenities</span>
                        <span class="rating-value"><i class="fas fa-star star-icon"></i> {{ $review->room_amenities }} / 5</span>
                    </li>
                    <li class="rating-item">
                        <span class="rating-label">Room Arrangement</span>
                        <span class="rating-value"><i class="fas fa-star star-icon"></i> {{ $review->room_arrangement }} / 5</span>
                    </li>
                    @if($review->dining_services)
                    <li class="rating-item">
                        <span class="rating-label">Dining & Services</span>
                        <span class="rating-value"><i class="fas fa-star star-icon"></i> {{ $review->dining_services }} / 5</span>
                    </li>
                    @endif
                    <li class="rating-item">
                        <span class="rating-label">Room Cleanness</span>
                        <span class="rating-value"><i class="fas fa-star star-icon"></i> {{ $review->room_cleanness }} / 5</span>
                    </li>
                    <li class="rating-item">
                        <span class="rating-label">Stay Location</span>
                        <span class="rating-value"><i class="fas fa-star star-icon"></i> {{ $review->stay_location }} / 5</span>
                    </li>
                </ul>
            </div>
        </div>

        @if($review->description)
        <div class="card">
            <div class="card-header">
                <div class="card-title">Guest Comments</div>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="description-box">
                    "{{ $review->description }}"
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Right Column: Meta Info -->
    <div class="right-col">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Reservation Info</div>
            </div>
            <div class="card-body" style="padding:0;">
                <ul class="info-list">
                    <li class="info-item">
                        <span class="info-label">Reservation ID</span>
                        <span class="info-value">#{{ $reservation->id }}</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Room</span>
                        <span class="info-value">{{ $reservation->room->title ?? 'N/A' }}</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Guest Name</span>
                        <span class="info-value">{{ $reservation->user->name ?? 'N/A' }}</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Host Approval Status</span>
                        <span class="info-value">
                            @if($review->host_approved === true)
                                <span class="badge badge-user">Approved</span>
                            @elseif($review->host_approved === false)
                                <span class="badge badge-danger" style="background:#fee2e2; color:#b91c1c;">Rejected</span>
                            @else
                                <span class="badge badge-sub_admin">Pending</span>
                            @endif
                        </span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Submitted On</span>
                        <span class="info-value">{{ $review->created_at->format('d M, Y H:i A') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
