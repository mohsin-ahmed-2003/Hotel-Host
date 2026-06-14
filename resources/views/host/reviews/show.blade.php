@extends('layouts.app')

@section('styles')
<style>
.review-page-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 100px);
    padding: 40px 20px;
    background-color: #f8fafc;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

body.dark-mode .review-page-wrapper {
    background-color: #0f172a;
}

.review-card {
    background: #ffffff;
    width: 100%;
    max-width: 650px;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

body.dark-mode .review-card {
    background: #1e293b;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

.review-header {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    padding: 35px 25px;
    text-align: center;
    color: #ffffff;
}

.review-header h3 {
    margin: 0 0 10px 0;
    font-size: 24px;
    font-weight: 700;
}

.review-header p {
    margin: 0;
    font-size: 15px;
    opacity: 0.85;
}

.review-body {
    padding: 40px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary, #1e293b);
    margin: 0 0 16px 0;
}

body.dark-mode .section-title {
    color: #f8fafc;
}

.rating-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
    border-bottom: 1px solid var(--border-color, #f1f5f9);
}

body.dark-mode .rating-row {
    border-bottom-color: #334155;
}

.rating-row:last-child {
    border-bottom: none;
}

.rating-label {
    font-size: 15px;
    font-weight: 600;
    color: var(--text-primary, #1e293b);
    margin: 0;
}

body.dark-mode .rating-label {
    color: #e2e8f0;
}

.rating-value {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    color: var(--text-primary, #1e293b);
}

body.dark-mode .rating-value {
    color: #f8fafc;
}

.rating-value i {
    color: #fbbf24;
    font-size: 14px;
}

.desc-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 20px;
    border-radius: 12px;
    font-size: 15px;
    line-height: 1.6;
    color: #334155;
    margin-bottom: 30px;
}

body.dark-mode .desc-box {
    background: #0f172a;
    border-color: #334155;
    color: #cbd5e1;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.badge-approved {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.badge-rejected {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.badge-pending {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.action-buttons {
    display: flex;
    gap: 16px;
    margin-top: 30px;
    padding-top: 24px;
    border-top: 1px solid var(--border-color, #f1f5f9);
}

body.dark-mode .action-buttons {
    border-top-color: #334155;
}

.btn-action {
    flex: 1;
    padding: 14px;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
}

.btn-approve {
    background: #10b981;
    color: white;
}

.btn-approve:hover {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}

.btn-reject {
    background: #ef4444;
    color: white;
}

.btn-reject:hover {
    background: #dc2626;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
}

.alert-message {
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.2);
}
</style>
@endsection

@section('content')
<div class="review-page-wrapper">
    <div class="review-card">
        <div class="review-header">
            <h3>Guest Review for {{ $reservation->room->title ?? 'Room' }}</h3>
            <p>Submitted by {{ $reservation->user->name }} on {{ $review->created_at->format('M d, Y') }}</p>
        </div>

        <div class="review-body">
            @if(session('success'))
                <div class="alert-message alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert-message alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <h4 class="section-title">Ratings Breakdown</h4>
            <div style="margin-bottom: 30px;">
                <div class="rating-row">
                    <span class="rating-label">Room Space</span>
                    <span class="rating-value"><i class="fas fa-star"></i> {{ $review->room_space }} / 5</span>
                </div>
                <div class="rating-row">
                    <span class="rating-label">Room Amenities</span>
                    <span class="rating-value"><i class="fas fa-star"></i> {{ $review->room_amenities }} / 5</span>
                </div>
                <div class="rating-row">
                    <span class="rating-label">Room Arrangement</span>
                    <span class="rating-value"><i class="fas fa-star"></i> {{ $review->room_arrangement }} / 5</span>
                </div>
                @if($review->dining_services)
                <div class="rating-row">
                    <span class="rating-label">Dining & Services</span>
                    <span class="rating-value"><i class="fas fa-star"></i> {{ $review->dining_services }} / 5</span>
                </div>
                @endif
                <div class="rating-row">
                    <span class="rating-label">Room Cleanness</span>
                    <span class="rating-value"><i class="fas fa-star"></i> {{ $review->room_cleanness }} / 5</span>
                </div>
                <div class="rating-row">
                    <span class="rating-label">Stay Location</span>
                    <span class="rating-value"><i class="fas fa-star"></i> {{ $review->stay_location }} / 5</span>
                </div>
                <div class="rating-row" style="background: rgba(99, 102, 241, 0.05); padding: 16px; border-radius: 12px; margin-top: 10px;">
                    <span class="rating-label" style="color: #4f46e5;">Overall Rating</span>
                    <span class="rating-value" style="color: #4f46e5; font-size: 18px;"><i class="fas fa-star" style="font-size: 18px;"></i> {{ number_format($review->rating, 1) }} / 5</span>
                </div>
            </div>

            @if($review->description)
                <h4 class="section-title">Guest Comment</h4>
                <div class="desc-box">
                    "{{ $review->description }}"
                </div>
            @endif

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h4 class="section-title" style="margin: 0;">Current Status</h4>
                @if($review->host_approved === true)
                    <span class="status-badge badge-approved"><i class="fas fa-check-circle"></i> Approved</span>
                @elseif($review->host_approved === false)
                    <span class="status-badge badge-rejected"><i class="fas fa-times-circle"></i> Rejected</span>
                @else
                    <span class="status-badge badge-pending"><i class="fas fa-clock"></i> Pending Approval</span>
                @endif
            </div>

            <form action="{{ route('host.reviews.action', $reservation->id) }}" method="POST">
                @csrf
                <div class="action-buttons">
                    <button type="submit" name="action" value="approve" class="btn-action btn-approve">
                        <i class="fas fa-check"></i> Approve Review
                    </button>
                    <button type="submit" name="action" value="reject" class="btn-action btn-reject">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
