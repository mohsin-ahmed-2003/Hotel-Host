@extends('layouts.app')
@section('title', 'Itinerary - ' . ($reservation->room->title ?? 'Reservation'))

@section('content')
<style>
    .itinerary-container {
        max-width: 1100px;
        margin: 0 auto 60px auto;
        padding: 20px;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .itinerary-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .itinerary-title {
        font-size: 28px;
        font-weight: 800;
        color: var(--body-text, #111827);
        letter-spacing: -1px;
        margin: 0;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--body-text, #111827);
        font-weight: 600;
        text-decoration: none;
        padding: 8px 16px;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 8px;
        background: white;
        transition: background 0.2s;
    }

    .btn-back:hover {
        background: #f9fafb;
    }

    /* Property Overview Card */
    .property-card {
        display: flex;
        gap: 24px;
        background: white;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
    }

    .property-image {
        width: 300px;
        height: 200px;
        border-radius: 12px;
        object-fit: cover;
        flex-shrink: 0;
    }

    .property-details {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .property-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--body-text, #111827);
        margin: 0 0 8px 0;
    }

    .property-location {
        font-size: 15px;
        color: var(--body-muted, #6b7280);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .host-info {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 16px;
        border-top: 1px solid var(--border, #e5e7eb);
    }

    .host-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .host-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #64748b;
    }

    .btn-contact {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: var(--body-text, #111827);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        transition: opacity 0.2s;
    }

    .btn-contact:hover {
        opacity: 0.9;
    }

    /* Grid Layout */
    .info-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 24px;
    }

    .info-card {
        background: white;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--body-text, #111827);
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border, #e5e7eb);
    }

    /* Timeline */
    .timeline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        padding: 20px;
        background: rgba(37, 99, 235, 0.04);
        border-radius: 12px;
    }

    .timeline-node {
        text-align: center;
    }

    .timeline-node .label {
        font-size: 12px;
        text-transform: uppercase;
        color: var(--body-muted, #6b7280);
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .timeline-node .value {
        font-size: 18px;
        font-weight: 800;
        color: var(--body-text, #111827);
    }

    .timeline-divider {
        flex: 1;
        height: 2px;
        background: #cbd5e1;
        margin: 0 20px;
        position: relative;
    }

    .timeline-divider::after {
        content: '{{ $reservation->checkin->diffInDays($reservation->checkout) }} Nights';
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        padding: 0 10px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
    }

    /* Billing Summary */
    .billing-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .billing-row:last-child {
        border-bottom: none;
    }

    .billing-label {
        color: var(--body-muted, #64748b);
        font-size: 14px;
        font-weight: 600;
    }

    .billing-value {
        color: var(--body-text, #111827);
        font-size: 15px;
        font-weight: 700;
    }

    .billing-total {
        margin-top: 10px;
        padding-top: 16px;
        border-top: 2px solid var(--body-text, #111827);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .billing-total .label {
        font-size: 16px;
        font-weight: 800;
        color: var(--body-text, #111827);
    }

    .billing-total .value {
        font-size: 22px;
        font-weight: 800;
        color: var(--primary, #2563eb);
    }

    .policy-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .status-success { background: #dcfce7; color: #166534; }
    .status-pending { background: #fef08a; color: #854d0e; }
    .status-failed { background: #fee2e2; color: #b91c1c; }

    @media (max-width: 900px) {
        .property-card {
            flex-direction: column;
        }
        .property-image {
            width: 100%;
            height: 250px;
        }
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="itinerary-container">
    
    <div class="itinerary-header">
        <div>
            <h1 class="itinerary-title">Your Itinerary</h1>
            <div style="color: var(--body-muted); margin-top:4px;">Reservation #{{ $reservation->id }}</div>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($reservation->room->roomLocation->location_name ?? $reservation->room->address ?? '') }}" target="_blank" class="btn-back" style="color: #2563eb; border-color: #bfdbfe; background: #eff6ff;">
                <i class="fas fa-directions"></i> Directions
            </a>
            <a href="{{ url()->previous() }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @php
        $isGuest = (session('user_id') == $reservation->user_id);
        $targetUser = $isGuest ? $reservation->room->user : $reservation->user;
        $roleLabel = $isGuest ? 'Host' : 'Guest';
    @endphp

    <!-- Property Overview -->
    <div class="property-card">
        @if($reservation->room->photos->count() > 0)
            <img class="property-image" src="{{ \Illuminate\Support\Facades\Storage::url($reservation->room->photos->first()->photo_path) }}" alt="Room Image">
        @else
            <div class="property-image" style="background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#94a3b8;">
                <i class="fas fa-image fa-3x"></i>
            </div>
        @endif
        
        <div class="property-details">
            <a href="{{ route('rooms.show', $reservation->room->id) }}" style="text-decoration: none; color: inherit; display: inline-block;">
                <h2 class="property-title" style="transition: color 0.2s;" onmouseover="this.style.color='#2563eb'" onmouseout="this.style.color='var(--body-text)'">
                    {{ $reservation->room->title ?? 'Amazing Place' }}
                </h2>
            </a>
            <div class="property-location">
                <i class="fas fa-map-marker-alt"></i> 
                {{ $reservation->room->roomLocation->location_name ?? $reservation->room->address ?? 'Location not specified' }}
            </div>
            
            <div style="margin-bottom: auto;">
                <span class="policy-badge">
                    <i class="fas fa-shield-alt"></i> {{ ucfirst($reservation->room->cancellation_policy ?? 'Flexible') }} Policy
                </span>
            </div>

            <div class="host-info">
                <div class="host-profile">
                    @if($targetUser && $targetUser->profile_image)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($targetUser->profile_image) }}" alt="Avatar" class="host-avatar" onerror="this.onerror=null; this.src='{{ asset('images/image.png') }}';">
                    @else
                        <img src="{{ asset('images/image.png') }}" alt="Default Avatar" class="host-avatar">
                    @endif
                    <div>
                        <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase;">{{ $roleLabel }}</div>
                        <div style="font-size:15px; font-weight:700; color:#111827;">{{ $targetUser->name ?? 'Unknown User' }}</div>
                    </div>
                </div>
                
                @if($targetUser && $targetUser->email)
                    <a href="mailto:{{ $targetUser->email }}?subject=Reservation #{{ $reservation->id }}" class="btn-contact">
                        <i class="fas fa-envelope"></i> Message {{ $roleLabel }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="info-grid">
        <!-- Trip Details -->
        <div class="info-card">
            <h3 class="section-title">Trip Details</h3>
            
            <div class="timeline">
                <div class="timeline-node">
                    <div class="label">Check-in</div>
                    <div class="value">{{ $reservation->checkin->format('M d, Y') }}</div>
                    <div style="font-size:13px; color:#64748b; margin-top:2px;">After 3:00 PM</div>
                </div>
                <div class="timeline-divider"></div>
                <div class="timeline-node">
                    <div class="label">Check-out</div>
                    <div class="value">{{ $reservation->checkout->format('M d, Y') }}</div>
                    <div style="font-size:13px; color:#64748b; margin-top:2px;">Before 11:00 AM</div>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px; padding-top:16px; border-top:1px dashed #e2e8f0;">
                <div>
                    <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase;">Guests</div>
                    <div style="font-size:16px; font-weight:600; color:#111827;">{{ $reservation->guests }} Guest(s)</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase;">Booking Status</div>
                    <div style="font-size:16px; font-weight:600; color:#111827;">{{ ucfirst($reservation->reservation_status ?? 'Requested') }}</div>
                </div>
            </div>
        </div>

        <!-- Billing Summary -->
        <div class="info-card">
            <h3 class="section-title">Billing & Payment</h3>
            
            <div class="billing-row">
                <span class="billing-label">Payment Status</span>
                @php
                    $statusClass = 'status-pending';
                    if($reservation->status === 'success' || $reservation->status === 'completed') $statusClass = 'status-success';
                    elseif($reservation->status === 'failed') $statusClass = 'status-failed';
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ ucfirst($reservation->status ?? 'Pending') }}</span>
            </div>

            <div class="billing-row">
                <span class="billing-label">Payment Method</span>
                <span class="billing-value">{{ $reservation->payment_type ?? 'N/A' }}</span>
            </div>

            <div class="billing-row">
                <span class="billing-label">Transaction ID</span>
                <span class="billing-value" style="font-family: monospace;">{{ $reservation->transaction_id ?? 'None' }}</span>
            </div>

            <div class="billing-total">
                <span class="label">Total Amount</span>
                <span class="value">{{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($reservation->total_amount, 2) }}</span>
            </div>
            
            <div style="margin-top: 16px; text-align: center;">
                <a href="{{ route('user.reservations.receipt', $reservation->id) }}" style="font-size: 14px; font-weight: 600; color: var(--primary, #2563eb); text-decoration: none;">
                    <i class="fas fa-file-invoice" style="margin-right: 4px;"></i> View Full Receipt
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
