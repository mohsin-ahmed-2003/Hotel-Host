@extends('layouts.app')

@section('title', 'Confirm and Pay - ' . ($room->title ?: $room->name))

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --checkout-bg: #f8fafc;
            --checkout-surface: #ffffff;
            --checkout-text: #0f172a;
            --checkout-muted: #64748b;
            --checkout-accent: #0ea5e9;
            /* Modern Cyan */
            --checkout-border: #e2e8f0;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            --card-border-radius: 20px;
        }

        body.dark-mode {
            --checkout-bg: #0f172a;
            --checkout-surface: #1e293b;
            --checkout-text: #f8fafc;
            --checkout-muted: #94a3b8;
            --checkout-border: #334155;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        body {
            background-color: var(--checkout-bg);
            color: var(--checkout-text);
            font-family: 'Outfit', sans-serif;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 24px;
        }

        .checkout-layout {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 48px;
            align-items: start;
        }

        /* Left Side Styles */
        .left-section {
            display: flex;
            flex-direction: column;
            /* gap: 36px; */
        }

        .section-header {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .section-desc {
            font-size: 14.5px;
            color: var(--checkout-muted);
            line-height: 1.6;
            margin-bottom: 24px;
        }

        /* Payment Methods Grid */
        .payment-methods-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .payment-card {
            background: var(--checkout-surface);
            border: 2px solid var(--checkout-border);
            border-radius: 16px;
            /* padding: 24px 16px; */
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            /* gap: 12px; */
            position: relative;
            height: 80px;
        }

        .payment-card:hover {
            transform: translateY(-4px);
            border-color: var(--checkout-accent);
            box-shadow: 0 12px 24px rgba(14, 165, 233, 0.1);
        }

        .payment-card.active {
            border-color: var(--checkout-accent);
            background: rgba(14, 165, 233, 0.04);
            box-shadow: 0 12px 24px rgba(14, 165, 233, 0.15);
        }

        body.dark-mode .payment-card.active {
            background: rgba(14, 165, 233, 0.08);
        }

        .payment-card img {
            max-height: 40px;
            max-width: 80%;
            object-fit: contain;
            transition: transform 0.2s;
        }

        .payment-card:hover img {
            transform: scale(1.05);
        }

        .payment-card-text {
            font-size: 15px;
            font-weight: 700;
            color: var(--checkout-text);
        }

        /* Easebuzz Custom Brand Logo UI replacement */
        .easebuzz-custom-logo {
            display: flex;
            align-items: center;
            gap: 4px;
            justify-content: center;
        }

        .easebuzz-custom-logo span {
            font-size: 20px;
            font-weight: 900;
            color: #3b82f6;
            /* Custom Brand Blue */
            letter-spacing: -1px;
        }

        .easebuzz-custom-logo .eb-green {
            color: #10b981;
            /* Brand Green */
        }

        /* Dynamic Alert Box */
        .selection-alert {
            background: rgba(14, 165, 233, 0.08);
            border: 1px solid rgba(14, 165, 233, 0.2);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: all 0.3s ease;
            margin-bottom: 24px;
        }

        .selection-alert-icon {
            color: var(--checkout-accent);
            font-size: 20px;
            margin-top: 2px;
        }

        .selection-alert-content {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .selection-alert-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--checkout-text);
        }

        .selection-alert-text {
            font-size: 13.5px;
            color: var(--checkout-muted);
            line-height: 1.5;
        }

        /* Trip Details Inputs */
        .trip-form-section {
            border-top: 1px solid var(--checkout-border);
            /* padding-top: 36px; */
            margin-bottom: 20px;
        }

        .trip-textarea {
            width: 100%;
            height: 120px;
            border-radius: 16px;
            border: 1.5px solid var(--checkout-border);
            background: var(--checkout-surface);
            color: var(--checkout-text);
            padding: 16px;
            font-size: 15px;
            font-weight: 500;
            outline: none;
            resize: none;
            transition: all 0.2s;
            margin-top: 12px;
        }

        .trip-textarea:focus {
            border-color: var(--checkout-accent);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
        }

        /* House Rules */
        .house-rules-section {
            border-top: 1px solid var(--checkout-border);
            /* padding-top: 36px; */
        }

        .rules-title {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .rules-text {
            font-size: 14.5px;
            color: var(--checkout-muted);
            line-height: 1.6;
        }

        /* Footer Consent & Book Button */
        .consent-section {
            border-top: 1px solid var(--checkout-border);
            /* padding-top: 36px; */
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .consent-text {
            font-size: 13px;
            color: var(--checkout-muted);
            line-height: 1.6;
        }

        .consent-text a {
            color: var(--checkout-accent);
            text-decoration: none;
            font-weight: 600;
        }

        .consent-text a:hover {
            text-decoration: underline;
        }

        .btn-book-now {
            background: var(--checkout-accent);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 16px 36px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            align-self: flex-start;
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.25);
        }

        .btn-book-now:hover {
            transform: translateY(-2px);
            background: #0284c7;
            box-shadow: 0 12px 24px rgba(14, 165, 233, 0.4);
        }

        .btn-book-now:active {
            transform: translateY(0);
        }

        /* Right Side Sidebar Styles */
        .sidebar-section {
            position: sticky;
            top: 40px;
            background: var(--checkout-surface);
            border: 1px solid var(--checkout-border);
            border-radius: var(--card-border-radius);
            padding: 28px;
            box-shadow: var(--card-shadow);
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .sidebar-room-card {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .sidebar-image-container {
            width: 120px;
            height: 90px;
            border-radius: 12px;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            position: relative;
            cursor: pointer;
        }

        .sidebar-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .sidebar-image-container:hover img {
            transform: scale(1.08);
        }

        /* Zoom indicator overlay on hover */
        .sidebar-image-container::after {
            content: '\f00e';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 16px;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .sidebar-image-container:hover::after {
            opacity: 1;
        }

        .sidebar-room-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sidebar-room-name {
            font-size: 16px;
            font-weight: 800;
            line-height: 1.3;
            color: var(--checkout-text);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .sidebar-host-name {
            font-size: 13.5px;
            color: var(--checkout-muted);
            font-weight: 500;
        }

        .sidebar-location {
            font-size: 12.5px;
            color: var(--checkout-muted);
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 2px;
        }

        .sidebar-divider {
            height: 1px;
            background: var(--checkout-border);
            width: 100%;
        }

        .sidebar-details-row {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .details-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14.5px;
        }

        .details-label {
            color: var(--checkout-muted);
            font-weight: 500;
        }

        .details-value {
            color: var(--checkout-text);
            font-weight: 700;
        }

        /* Price Breakdown Section */
        .price-breakdown-section {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14.5px;
        }

        .price-label {
            color: var(--checkout-muted);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .price-value {
            color: var(--checkout-text);
            font-weight: 600;
        }

        /* Custom Tooltip */
        .tooltip-container {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .custom-tooltip {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(10px);
            background: #111827;
            color: #f9fafb;
            padding: 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            width: max-content;
            max-width: 280px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
            transition: all 0.2s ease;
            z-index: 1000;
            pointer-events: none;
            margin-bottom: 8px;
        }

        .custom-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 6px;
            border-style: solid;
            border-color: #111827 transparent transparent transparent;
        }

        .tooltip-container:hover .custom-tooltip {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        .price-row.discount .price-label,
        .price-row.discount .price-value {
            color: #10b981;
            /* Emerald Green for discounts */
            font-weight: 600;
        }

        .price-row.total {
            font-size: 18px;
            font-weight: 800;
            margin-top: 8px;
        }

        .price-row.total .price-label {
            color: var(--checkout-text);
            font-weight: 800;
        }

        .price-row.total .price-value {
            color: var(--checkout-text);
            font-weight: 800;
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .checkout-layout {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .sidebar-section {
                position: relative;
                top: 0;
                order: -1;
                /* Sidebar goes to top on smaller screens */
            }
        }

        @media (max-width: 600px) {
            .payment-methods-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .payment-card {
                height: 100px;
                padding: 16px;
            }

            .btn-book-now {
                width: 100%;
                text-align: center;
            }
        }
    </style>
@endsection

@section('content')
    <!-- SweetAlert2 CDN for toaster -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            });
        </script>
    @endif

    <div class="checkout-container">
        <div class="checkout-layout">

            <!-- Left Side: Interactive Payment Flow -->
            <div class="left-section">

                <div>
                    <h1 class="section-header">Choose Your Payment Method</h1>
                    <p class="section-desc">
                        Please use Easebuzz if you wish to make payments in INR.<br>
                        For all other currencies (e.g., USD, GBP, EUR, etc.) please use PayPal or Stripe.
                    </p>

                    <!-- Payment Cards Grid -->
                    <div class="payment-methods-grid">

                        <!-- PayPal Option -->
                        @if(\App\Models\SiteSetting::get('paypal_enabled') === '1')
                        <div class="payment-card active" onclick="selectPaymentMethod('PayPal')" id="card-PayPal">
                            <img src="{{ asset('images/paypal-logo-png.png') }}" alt="PayPal">
                            <span class="payment-card-text">PayPal</span>
                        </div>
                        @else
                        <script>
                            // If PayPal is disabled, make sure it is not the default selected method
                            document.addEventListener('DOMContentLoaded', function() {
                                if(typeof selectedPayment !== 'undefined' && selectedPayment === 'PayPal') {
                                    // select next available
                                    const stripeCard = document.getElementById('card-Stripe');
                                    if(stripeCard) stripeCard.click();
                                }
                            });
                        </script>
                        @endif

                        <!-- Stripe Option -->
                        @if(\App\Models\SiteSetting::get('stripe_enabled') === '1')
                        <div class="payment-card" onclick="selectPaymentMethod('Stripe')" id="card-Stripe">
                            <img src="{{ asset('images/stripe_payment.png') }}" alt="Stripe">
                            <span class="payment-card-text">Stripe</span>
                        </div>
                        @else
                        <script>
                            // If Stripe is disabled, ensure it is not selected
                            document.addEventListener('DOMContentLoaded', function() {
                                if(typeof selectedPayment !== 'undefined' && selectedPayment === 'Stripe') {
                                    const nextCard = document.getElementById('card-Easebuzz') || document.getElementById('card-Razorpay');
                                    if(nextCard) nextCard.click();
                                }
                            });
                        </script>
                        @endif

                        <!-- Easebuzz Option -->
                        @if(\App\Models\SiteSetting::get('easebuzz_enabled') === '1')
                        <div class="payment-card" onclick="selectPaymentMethod('Easebuzz')" id="card-Easebuzz">
                            <!-- Custom CSS-styled Easebuzz brand icon -->
                            <div class="easebuzz-custom-logo">
                                <span>ease<span class="eb-green">buzz</span></span>
                            </div>
                            <span class="payment-card-text">Easebuzz</span>
                        </div>
                        @endif

                        <!-- Razorpay Option -->
                        @if(\App\Models\SiteSetting::get('razorpay_enabled') === '1')
                        <div class="payment-card" onclick="selectPaymentMethod('Razorpay')" id="card-Razorpay">
                            <img src="{{ asset('images/razorpay.png') }}" alt="Razorpay">
                            <span class="payment-card-text">Razorpay</span>
                        </div>
                        @endif
                    </div>

                    <!-- Selected Confirmation Alert Card -->
                    <div class="selection-alert" id="selectionAlert">
                        <div class="selection-alert-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="selection-alert-content">
                            <div class="selection-alert-title" id="alertTitle">PayPal Chosen</div>
                            <div class="selection-alert-text" id="alertMessage">
                                You have chosen to pay using PayPal. <br>
                                Scroll below and click Book Now to complete the booking.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trip Custom Message Form -->
                <div class="trip-form-section">
                    <h2 class="rules-title">Tell {{ $room->user->name ?? 'Host' }} About Your Trip</h2>
                    <p class="rules-text">Some helpful tips on what to write:</p>
                    <ul
                        style="padding-left: 20px; font-size: 14px; color: var(--checkout-muted); line-height: 1.6; margin-top: 8px;">
                        <li>What brings you to Madurai?</li>
                        <li>Who is checking in with you?</li>
                    </ul>
                    <textarea class="trip-textarea" placeholder="Hello! I am visiting Madurai for..."></textarea>
                </div>

                <!-- House Rules Section -->
                <div class="house-rules-section">
                    <h2 class="rules-title">House Rules</h2>
                    <p class="rules-text" style="margin-bottom: 16px;">By booking this space you're agreeing to follow
                        {{ $room->user->name ?? 'Host' }}'s House Rules.
                    </p>

                    @php
                        $selectedRuleIds = is_array($room->selected_rules) ? $room->selected_rules : [];
                        $selectedRules = \App\Models\RoomRule::whereIn('id', $selectedRuleIds)->get();
                    @endphp
                    @if($selectedRules->count() > 0)
                        <div
                            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px; margin-top: 12px; margin-bottom: 24px;">
                            @foreach($selectedRules as $rule)
                                <div
                                    style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: var(--checkout-bg); border: 1.5px solid var(--checkout-border); border-radius: 12px;">
                                    <i class="{{ $rule->icon ?: 'fas fa-info-circle' }}"
                                        style="color: var(--checkout-accent); font-size: 14px;"></i>
                                    <span
                                        style="font-size: 13px; font-weight: 700; color: var(--checkout-text);">{{ $rule->rule_name }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Cancellation Policy Section -->
                <div class="cancellation-policy-section"
                    style="border-top: 1px solid var(--checkout-border); margin-bottom: 24px;">
                    <h2 class="rules-title"><i class="fas fa-calendar-times" style="color: #ef4444; margin-right: 6px;"></i>
                        Cancellation Policy</h2>

                    <div
                        style="background: rgba(255, 255, 255, 0.02); border: 1.5px solid var(--checkout-border); border-radius: 16px; padding: 20px; box-shadow: var(--card-shadow);">
                        @if($room->custom_cancellation)
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <div
                                    style="width: 36px; height: 36px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <strong style="font-size: 15px; color: var(--checkout-text); display: block;">Custom
                                        Cancellation Policy</strong>
                                    <span
                                        style="font-size: 12px; color: #10b981; font-weight: 700;">{{ $room->free_cancellation_days }}
                                        Days Free Cancellation</span>
                                </div>
                            </div>
                            <p style="font-size: 13.5px; color: var(--checkout-muted); line-height: 1.5; margin: 0;">
                                Cancel before <strong>{{ $room->free_cancellation_days }} days</strong> prior to check-in to get
                                a 100% full refund. Cancellations made after this period will be subject to a
                                <strong>{{ $room->cancellation_fee }}% penalty fee</strong> on the base stay amount.
                            </p>
                        @else
                            @php
                                $policy = $room->cancellation_policy ?: 'Flexible';
                            @endphp
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <div
                                    style="width: 36px; height: 36px; border-radius: 50%; background: rgba(14, 165, 233, 0.1); color: var(--checkout-accent); display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                    <i class="fas fa-shield-halved"></i>
                                </div>
                                <div>
                                    <strong style="font-size: 15px; color: var(--checkout-text); display: block;">{{ $policy }}
                                        Cancellation Policy</strong>
                                    <span style="font-size: 12px; color: var(--checkout-muted); font-weight: 600;">Standard Host
                                        Terms</span>
                                </div>
                            </div>
                            <p style="font-size: 13.5px; color: var(--checkout-muted); line-height: 1.5; margin: 0;">
                                @if($policy === 'Flexible')
                                    Free cancellation up to 24 hours before check-in. Cancellations made within 24 hours are
                                    non-refundable.
                                @elseif($policy === 'Moderate')
                                    Free cancellation up to 5 days before check-in. Cancellations made within 5 days incur a 50%
                                    penalty fee.
                                @else
                                    Strict cancellation rules. 50% refund up to 14 days before check-in, non-refundable afterwards.
                                @endif
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Final Action Section -->
                <div class="consent-section">
                    <p class="consent-text">
                        By clicking on "Book Now", you agree to pay the total amount shown, which includes Service Fees, on
                        the right and to the <a href="#">Terms of Service</a>.
                    </p>
                    <button class="btn-book-now" onclick="completeBooking()">Book Now</button>
                </div>

            </div>

            <!-- Right Side: Interactive summary of pricing and room details -->
            <div class="sidebar-section">

                <!-- Room Card Overview -->
                <div class="sidebar-room-card">
                    <div class="sidebar-image-container">
                        @if($room->photos->count() > 0)
                            <a data-fancybox="gallery" data-caption="{{ $room->title }}"
                                href="{{ asset('storage/' . $room->photos->first()->photo_path) }}">
                                <img src="{{ asset('storage/' . $room->photos->first()->photo_path) }}"
                                    alt="{{ $room->title }}">
                            </a>
                        @else
                            <img src="{{ asset('images/image.png') }}" alt="Placeholder Room">
                        @endif
                    </div>
                    <div class="sidebar-room-details">
                        <div class="sidebar-room-name">{{ $room->title ?: $room->name }}</div>
                        <div class="sidebar-host-name">by {{ $room->user->name ?? 'Host' }}</div>
                        <div class="sidebar-location">
                            <i class="fas fa-map-marker-alt" style="color: #ef4444;"></i>
                            {{ $room->roomLocation->city ?? 'Madurai' }}, {{ $room->roomLocation->state ?? 'Tamil Nadu' }}
                            ,{{ $room->roomLocation->country ?? 'India' }}
                        </div>
                    </div>
                </div>

                <div class="sidebar-divider"></div>

                <!-- Stay Details Summary -->
                <div class="sidebar-details-row">
                    <div class="details-item">
                        <span class="details-label">Space Details</span>
                        <span class="details-value">{{ $room->spaceType->name ?? 'Private room' }} for {{ $guests }}
                            Guest{{ $guests > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="details-item">
                        <span class="details-label">Stay Dates</span>
                        <span class="details-value">{{ \Carbon\Carbon::parse($checkin)->format('d/m/Y') }} to
                            {{ \Carbon\Carbon::parse($checkout)->format('d/m/Y') }}</span>
                    </div>
                </div>

                <div class="sidebar-divider"></div>

                <!-- Policies Summary Card -->
                <div style="background: rgba(255, 255, 255, 0.02); border: 1.5px solid var(--checkout-border); border-radius: 16px; padding: 16px; margin: 0 0 16px 0; display: flex; flex-direction: column; gap: 12px; box-shadow: var(--card-shadow);">
                    <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--checkout-muted); letter-spacing: 0.5px; margin-bottom: 2px;">
                        <i class="fas fa-shield-alt" style="color: var(--checkout-accent); margin-right: 4px;"></i> Policies & Terms
                    </div>
                    
                    <!-- Booking Type -->
                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px;">
                        <div style="display: flex; align-items: center; gap: 8px; color: var(--checkout-muted);">
                            <i class="fas fa-bolt" style="color: #10b981; width: 16px; text-align: center;"></i>
                            <span>Booking Type</span>
                        </div>
                        <span style="font-weight: 700; color: var(--checkout-text);">{{ $room->booking_type ?: 'Instant Booking' }}</span>
                    </div>

                    <!-- Checkout Policy Time -->
                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px;">
                        <div style="display: flex; align-items: center; gap: 8px; color: var(--checkout-muted);">
                            <i class="far fa-clock" style="color: var(--checkout-accent); width: 16px; text-align: center;"></i>
                            <span>Checkout Time</span>
                        </div>
                        <span style="font-weight: 700; color: var(--checkout-text);">{{ $room->checkout_policy ?: '11:00 AM' }}</span>
                    </div>

                    <!-- Cancellation Policy -->
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; font-size: 13px; border-top: 1px dashed var(--checkout-border); padding-top: 10px; margin-top: 2px;">
                        <div style="display: flex; align-items: center; gap: 8px; color: var(--checkout-muted); margin-top: 2px;">
                            <i class="fas fa-calendar-times" style="color: #ef4444; width: 16px; text-align: center;"></i>
                            <span>Cancellation</span>
                        </div>
                        <div style="text-align: right;">
                            @if($room->custom_cancellation)
                                <span style="font-weight: 700; color: #10b981; display: block;">{{ $room->free_cancellation_days }} Days Free</span>
                                <span style="font-size: 10px; color: var(--checkout-muted);">{{ $room->cancellation_fee }}% fee after</span>
                            @else
                                @php
                                    $policy = $room->cancellation_policy ?: 'Flexible';
                                @endphp
                                <span style="font-weight: 700; color: var(--checkout-text); display: block;">{{ $policy }}</span>
                                <span style="font-size: 10px; color: var(--checkout-muted);">
                                    @if($policy === 'Flexible')
                                        Free up to 24h before
                                    @elseif($policy === 'Moderate')
                                        Free up to 5 days before
                                    @else
                                        Strict rules apply
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="sidebar-divider"></div>

                @php
                    $enhancementsTotal = 0;
                    $enhancementsTooltipHtml = '';
                    if (!empty($priceData['selectedEnhancements'])) {
                        foreach($priceData['selectedEnhancements'] as $e) {
                            $enhancementsTotal += $e['item_total'];
                            
                            $enhancementsTooltipHtml .= '
                                <div style="display:flex; justify-content:space-between; gap:16px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:6px; margin-bottom:6px; font-size:11px;">
                                    <div style="text-align:left;">
                                        <div style="font-weight:700; color:#fff;">' . e($e['item_name']) . '</div>
                                        <div style="color:#94a3b8; font-size:10px;">(' . $guests . ' guest' . ($guests > 1 ? 's' : '') . ' &times; ' . $e['days_count'] . ' day' . ($e['days_count'] > 1 ? 's' : '') . ')</div>
                                    </div>
                                    <div style="font-weight:700; color:#10b981; align-self:center;">' . $room->currency_symbol . number_format($e['item_total'], 2) . '</div>
                                </div>';
                        }
                        $enhancementsTooltipHtml = preg_replace('/border-bottom:1px solid rgba\(255,255,255,0\.1\); padding-bottom:6px; margin-bottom:6px;"(?=[^>]*>[ \s\n]*$)/', '"', $enhancementsTooltipHtml);
                    }
                @endphp

                <!-- Detailed Pricing Breakdown -->
                <div class="price-breakdown-section">

                    <!-- Base nights price -->
                    <div class="price-row">
                        <span class="price-label">
                            <i class="far fa-calendar-alt" style="color: var(--checkout-accent); margin-right: 10px;"></i>
                            <span>{{ $room->currency_symbol }}{{ number_format($priceData['avgBaseRate'], 2) }} x
                            {{ $priceData['totalNights'] }} night{{ $priceData['totalNights'] > 1 ? 's' : '' }}</span>
                        </span>
                        <span
                            class="price-value">{{ $room->currency_symbol }}{{ number_format($priceData['totalRawBasePrice'], 2) }}</span>
                    </div>

                    <!-- Discounted nights (if any) -->
                    @if($priceData['discountPct'] > 0)
                        <div class="price-row discount">
                            <span class="price-label">
                                <div class="tooltip-container" style="cursor:help;">
                                    <i class="fas fa-tag" style="color: #10b981; margin-right: 10px;"></i>
                                    <span style="border-bottom: 1px dashed rgba(16, 185, 129, 0.3); font-weight: 600;">Discount</span>
                                    <div class="custom-tooltip">
                                        <div style="font-weight: 700; margin-bottom: 6px; color: #10b981; border-bottom: 1px solid rgba(255,255,255,0.15); padding-bottom: 4px;">Discounts Applied</div>
                                        <div style="display: flex; justify-content: space-between; gap: 16px; font-size: 11px;">
                                            <div style="text-align: left;">
                                                <div style="font-weight: 700; color: #fff;">{{ $priceData['discountAppliedName'] }}</div>
                                                <div style="color: #94a3b8; font-size: 10px; font-weight: normal;">Promotional rate savings</div>
                                            </div>
                                            <div style="font-weight: 700; color: #10b981; align-self: center;">-{{ $priceData['discountPct'] }}%</div>
                                        </div>
                                    </div>
                                </div>
                            </span>
                            <span
                                class="price-value">-{{ $room->currency_symbol }}{{ number_format($priceData['discountSavings'], 2) }}</span>
                        </div>
                    @endif

                    <!-- Cleaning Fee (if any) -->
                    @if($priceData['cleaningFee'] > 0)
                        <div class="price-row">
                            <span class="price-label">
                                <i class="fas fa-broom" style="color: var(--checkout-accent); margin-right: 6px;"></i>
                                <span>Cleaning fee</span>
                            </span>
                            <span
                                class="price-value">{{ $room->currency_symbol }}{{ number_format($priceData['cleaningFee'], 2) }}</span>
                        </div>
                    @endif

                    <!-- Extra guests fee (if any) -->
                    @if($priceData['extraGuestsFee'] > 0)
                        <div class="price-row">
                            <span class="price-label">
                                <i class="fas fa-users" style="color: var(--checkout-accent); margin-right: 6px;"></i>
                                <span>Additional guests fee</span>
                            </span>
                            <span
                                class="price-value">{{ $room->currency_symbol }}{{ number_format($priceData['extraGuestsFee'], 2) }}</span>
                        </div>
                    @endif

                    <!-- Site Service fee (if any) -->
                    @if($priceData['serviceFeeAmt'] > 0)
                        <div class="price-row">
                            <span class="price-label">
                                <div class="tooltip-container" style="cursor:help;">
                                    <i class="fas fa-info-circle" style="color: var(--checkout-accent); margin-right: 6px;"></i>
                                    <span style="border-bottom: 1px dashed rgba(0,0,0,0.2); margin-left: 8px;">Service fee</span>
                                    <div class="custom-tooltip">
                                        <div style="font-weight:700; margin-bottom:4px; color:#10b981;">Service Fee</div>
                                        <div style="color:rgba(255,255,255,0.85); line-height:1.4; font-weight:normal;">This amount is non refundable</div>
                                    </div>
                                </div>
                            </span>
                            <span
                                class="price-value">{{ $room->currency_symbol }}{{ number_format($priceData['serviceFeeAmt'], 2) }}</span>
                        </div>
                    @endif

                    <!-- Selected Dining & Enhancements (if any) -->
                    @if($enhancementsTotal > 0)
                        <div class="price-row">
                            <span class="price-label">
                                <div class="tooltip-container" style="cursor:help;">
                                    <i class="fas fa-concierge-bell" style="color: #10b981; margin-right: 14px;"></i>
                                    <span style="border-bottom: 1px dashed rgba(0,0,0,0.2); font-weight:600;">Dining & Services</span>
                                    <div class="custom-tooltip">
                                        <div style="font-weight:700; margin-bottom:6px; color:#10b981; border-bottom:1px solid rgba(255,255,255,0.15); padding-bottom:4px;">Dining & Services Breakdown</div>
                                        {!! $enhancementsTooltipHtml !!}
                                    </div>
                                </div>
                            </span>
                            <span
                                class="price-value">{{ $room->currency_symbol }}{{ number_format($enhancementsTotal, 2) }}</span>
                        </div>
                    @endif

                    <!-- Taxes (if any) -->
                    @if($priceData['taxAmt'] > 0)
                        <div class="price-row">
                            <span class="price-label">
                                <i class="fas fa-file-invoice-dollar" style="color: var(--checkout-accent); margin-right: 10px;"></i>
                                <span>Taxes</span>
                            </span>
                            <span
                                class="price-value">{{ $room->currency_symbol }}{{ number_format($priceData['taxAmt'], 2) }}</span>
                        </div>
                    @endif

                    <div class="sidebar-divider"></div>

                    <!-- Total Amount -->
                    <div class="price-row total">
                        <span class="price-label">
                            <i class="fas fa-wallet" style="color: var(--checkout-text); margin-right: 6px;"></i>
                            <span>Total ({{ $room->currency_symbol }})</span>
                        </span>
                        <span
                            class="price-value">{{ $room->currency_symbol }}{{ number_format($priceData['finalTotal'], 2) }}</span>
                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        // Store currently selected payment method
        let selectedPayment = 'PayPal';

        // Bind fancybox for room gallery image zoom
        Fancybox.bind("[data-fancybox]", {
            // Fancybox options
            dragToClose: true,
            Toolbar: {
                display: {
                    left: ["infobar"],
                    middle: [],
                    right: ["slideshow", "download", "thumbs", "close"],
                },
            },
        });

        // Function to select and highlight payment methods dynamically
        function selectPaymentMethod(method) {
            selectedPayment = method;

            // Remove active class from all cards
            document.querySelectorAll('.payment-card').forEach(card => {
                card.classList.remove('active');
            });

            // Add active class to clicked card
            const selectedCard = document.getElementById(`card-${method}`);
            if (selectedCard) {
                selectedCard.classList.add('active');
            }

            // Update confirmation warning/alert message dynamically
            const alertTitle = document.getElementById('alertTitle');
            const alertMsg = document.getElementById('alertMessage');

            alertTitle.textContent = `${method} Chosen`;
            alertMsg.innerHTML = `You have chosen to pay using <strong>${method}</strong>.<br>Scroll below and click Book Now to complete the booking.`;
        }

        // Mock action when completing the booking
        function completeBooking() {
            if (selectedPayment === 'PayPal') {
                document.getElementById('paypalCheckoutForm').submit();
            } else if (selectedPayment === 'Stripe') {
                document.getElementById('stripeCheckoutForm').submit();
            } else if (selectedPayment === 'Easebuzz') {
                document.getElementById('easebuzzCheckoutForm').submit();
            } else if (selectedPayment === 'Razorpay') {
                document.getElementById('razorpayCheckoutForm').submit();
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Work in Progress',
                    text: `Payment using ${selectedPayment} is currently not implemented.`
                });
            }
        }
    </script>

    <!-- Hidden form for PayPal Submission -->
    <form id="paypalCheckoutForm" action="{{ route('payment.paypal.initiate') }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="room_id" value="{{ $room->id }}">
        <input type="hidden" name="checkin" value="{{ $checkin }}">
        <input type="hidden" name="checkout" value="{{ $checkout }}">
        <input type="hidden" name="guests" value="{{ $guests }}">
        <input type="hidden" name="enhancement_ids" value="{{ request('enhancement_ids') }}">
        <input type="hidden" name="enhancement_dates" value="{{ request('enhancement_dates') }}">
    </form>

    <!-- Hidden form for Stripe Submission -->
    <form id="stripeCheckoutForm" action="{{ route('payment.stripe.initiate') }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="room_id" value="{{ $room->id }}">
        <input type="hidden" name="checkin" value="{{ $checkin }}">
        <input type="hidden" name="checkout" value="{{ $checkout }}">
        <input type="hidden" name="guests" value="{{ $guests }}">
        <input type="hidden" name="enhancement_ids" value="{{ request('enhancement_ids') }}">
        <input type="hidden" name="enhancement_dates" value="{{ request('enhancement_dates') }}">
    </form>

    <!-- Hidden form for Easebuzz Submission -->
    <form id="easebuzzCheckoutForm" action="{{ route('payment.easebuzz.initiate') }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="room_id" value="{{ $room->id }}">
        <input type="hidden" name="checkin" value="{{ $checkin }}">
        <input type="hidden" name="checkout" value="{{ $checkout }}">
        <input type="hidden" name="guests" value="{{ $guests }}">
        <input type="hidden" name="enhancement_ids" value="{{ request('enhancement_ids') }}">
        <input type="hidden" name="enhancement_dates" value="{{ request('enhancement_dates') }}">
    </form>

    <!-- Hidden form for Razorpay Submission -->
    <form id="razorpayCheckoutForm" action="{{ route('payment.razorpay.initiate') }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="room_id" value="{{ $room->id }}">
        <input type="hidden" name="checkin" value="{{ $checkin }}">
        <input type="hidden" name="checkout" value="{{ $checkout }}">
        <input type="hidden" name="guests" value="{{ $guests }}">
        <input type="hidden" name="enhancement_ids" value="{{ request('enhancement_ids') }}">
        <input type="hidden" name="enhancement_dates" value="{{ request('enhancement_dates') }}">
    </form>
@endsection