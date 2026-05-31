@extends('layouts.app')

@section('title', 'Guest Reservations')

@section('content')
    <div class="reservations-dashboard-wrapper">
        <div class="dashboard-container">
            <!-- Header Section -->
            <div class="dashboard-header">
                <div class="header-left">
                    <h1 class="dashboard-title">Guest Reservations</h1>
                    <p class="dashboard-subtitle">Manage upcoming guests and view booking earnings.</p>
                </div>
                <div class="header-right">
                    <div class="header-actions">
                        <div class="view-toggle">
                            <button class="view-btn active" onclick="setViewMode('card')" title="Card View">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                            </button>
                            <button class="view-btn" onclick="setViewMode('list')" title="List View">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <line x1="8" y1="6" x2="21" y2="6"></line>
                                    <line x1="8" y1="12" x2="21" y2="12"></line>
                                    <line x1="8" y1="18" x2="21" y2="18"></line>
                                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                        <a href="{{ route('host.start') }}" class="btn-primary">
                            <span>Host another property</span>
                        </a>
                    </div>

                    <div class="header-tabs">
                        <a href="{{ route('user.reservations', ['tab' => 'previous']) }}"
                            class="tab-link {{ $tab === 'previous' ? 'active' : '' }}">Previous</a>
                        <a href="{{ route('user.reservations', ['tab' => 'current']) }}"
                            class="tab-link {{ $tab === 'current' ? 'active' : '' }}">Current</a>
                        <a href="{{ route('user.reservations', ['tab' => 'upcoming']) }}"
                            class="tab-link {{ ($tab ?? 'upcoming') === 'upcoming' ? 'active' : '' }}">Upcoming</a>
                    </div>
                </div>
            </div>

            <div class="reservation-grid" id="reservationGrid">
                @forelse($reservations as $res)
                    <div class="res-card">
                        <div class="res-card-img-wrap">
                            @php
                                $coverPhoto = $res->room->photos->where('is_cover', 1)->first() ?? $res->room->photos->first();
                                $imgUrl = $coverPhoto ? \Illuminate\Support\Facades\Storage::url($coverPhoto->photo_path) : asset('images/image.png');
                            @endphp
                            <img src="{{ $imgUrl }}" alt="Room Image" class="res-card-img">
                            <div class="status-badge status-{{ strtolower($res->reservation_status ?? 'requested') }}">
                                {{ ucfirst($res->reservation_status ?? 'requested') }}
                            </div>
                            <div class="host-profile-overlay">
                                <img src="{{ asset($res->user->profile_image ?? 'images/image.png') }}" class="host-img"
                                    alt="Guest">
                                <div class="host-overlay-text">
                                    <span class="host-label">Guest</span>
                                    <span class="host-name">{{ $res->user->name ?? 'Unknown' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="res-card-body">
                            <h3 class="res-room-title">{{ $res->room->title ?? 'Room Unavailable' }}</h3>
                            <p class="res-location">{{ $res->room->location->location_name ?? 'Location unavailable' }}</p>

                            <div class="res-info-row">
                                <div class="res-dates">
                                    <div class="date-box">
                                        <span class="date-label">Check-in</span>
                                        <span class="date-val">{{ $res->checkin->format('M d, Y') }}</span>
                                    </div>
                                    <div class="date-box">
                                        <span class="date-label">Check-out</span>
                                        <span class="date-val">{{ $res->checkout->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                <div class="res-price">
                                    <span
                                        style="font-size: 10px; color: var(--body-muted); display: block; text-align: right; text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px; margin-bottom: 2px;">Earning</span>
                                    <strong>{{ $res->room->currency_symbol ?? '$' }}{{ number_format($res->total_amount, 2) }}</strong>
                                    <div style="margin-top: 8px; text-align: right;">
                                        <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: rgba(37, 99, 235, 0.08); color: var(--accent); border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                            {{ ucfirst($res->room->cancellation_policy ?? 'Flexible') }} Policy
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="res-actions">
                                <a href="mailto:{{ $res->user->email ?? '' }}?subject=Your Reservation #{{ $res->id }}"
                                    class="btn-action btn-message">Message Guest</a>
                                <a href="{{ route('user.reservations.itinerary', $res->id) }}" class="btn-action btn-secondary">View Itinerary</a>
                                <a href="{{ route('user.reservations.receipt', $res->id) }}" class="btn-action btn-secondary">View Receipt</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">🏠</div>
                        <h3>No guest reservations</h3>
                        <p>When guests book your properties, their reservations will appear here.</p>
                    </div>
                @endforelse
            </div>

            <div class="pagination-wrapper">
                {{ $reservations->links() }}
            </div>
        </div>
    </div>

    <style>
        .reservations-dashboard-wrapper {
            min-height: 100vh;
            padding: 15px 20px;
            background: var(--body-bg);
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 16px;
        }

        .dashboard-title {
            font-size: 32px;
            font-weight: 850;
            letter-spacing: -1px;
            /* margin-bottom: 8px; */
            color: var(--body-text);
        }

        .dashboard-subtitle {
            color: var(--body-muted);
            font-size: 16px;
            margin: 10px;
        }

        .header-tabs {
            display: inline-flex;
            gap: 8px;
            background: rgba(0, 0, 0, 0.03);
            padding: 6px;
            border-radius: 14px;
            width: fit-content;
        }

        body.dark-mode .header-tabs {
            background: rgba(255, 255, 255, 0.03);
        }

        .tab-link {
            padding: 8px 20px;
            border-radius: 10px;
            color: var(--body-muted);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 14px;
        }

        .tab-link:hover {
            color: var(--body-text);
        }

        .tab-link.active {
            background: var(--card-bg);
            color: var(--body-text);
            box-shadow: var(--shadow-sm);
            font-weight: 700;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
        }

        .header-actions {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .view-toggle {
            display: flex;
            background: rgba(0, 0, 0, 0.05);
            padding: 4px;
            border-radius: 12px;
            gap: 4px;
        }

        body.dark-mode .view-toggle {
            background: rgba(255, 255, 255, 0.05);
        }

        .view-btn {
            background: transparent;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            color: var(--body-muted);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .view-btn:hover {
            color: var(--body-text);
        }

        .view-btn.active {
            background: var(--card-bg);
            color: var(--body-text);
            box-shadow: var(--shadow-sm);
        }

        .reservation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        /* List View Styles */
        .reservation-grid.list-view {
            grid-template-columns: 1fr;
        }

        .reservation-grid.list-view .res-card {
            flex-direction: row;
            height: auto;
            align-items: stretch;
        }

        .reservation-grid.list-view .res-card-img-wrap {
            width: 240px;
            flex-shrink: 0;
            border-right: 1px solid var(--border);
            height: auto;
            min-height: 150px;
        }

        .reservation-grid.list-view .res-card-img-wrap .res-card-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .reservation-grid.list-view .res-card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 16px 20px;
            gap: 0;
        }

        .reservation-grid.list-view .res-room-title {
            font-size: 18px;
            margin-bottom: 2px;
        }

        .reservation-grid.list-view .res-location {
            margin-bottom: 16px;
            font-size: 13px;
        }

        .reservation-grid.list-view .res-info-row {
            align-items: center;
            margin-bottom: 0;
        }

        .reservation-grid.list-view .res-dates {
            margin: 0;
            padding: 10px 14px;
            width: fit-content;
            gap: 20px;
        }

        .reservation-grid.list-view .res-actions {
            border-top: 1px solid var(--border);
            padding-top: 12px;
            margin-top: 16px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .reservation-grid.list-view .btn-action {
            flex: none;
            padding: 8px 16px;
            width: auto;
            min-width: 0;
        }

        /* Mobile overrides */
        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-right {
                width: 100%;
                align-items: flex-end;
            }

            .view-toggle {
                display: none !important;
            }

            .header-actions {
                width: 100%;
                justify-content: flex-end;
            }

            .header-tabs {
                width: 100%;
                justify-content: flex-end;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .tab-link {
                flex: 1;
                text-align: center;
                white-space: nowrap;
                padding: 10px 12px;
            }

            .res-info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .res-price {
                align-self: flex-start;
            }

            .res-dates {
                width: 100%;
                box-sizing: border-box;
                justify-content: space-between;
            }

            .reservation-grid.list-view .res-card {
                flex-direction: column;
            }

            .reservation-grid.list-view .res-card-img-wrap {
                width: 100%;
                height: 200px;
                min-height: 200px;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .reservation-grid.list-view .res-price span {
                text-align: left !important;
            }

            .reservation-grid.list-view .res-actions {
                margin-top: 16px;
            }

            .reservation-grid.list-view .btn-action {
                width: 100%;
                flex: 1;
            }
        }

        .res-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
        }

        .res-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .res-card-img-wrap {
            position: relative;
            width: 100%;
            height: 200px;
            background: #eee;
        }

        .res-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .status-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(255, 255, 255, 0.9);
            color: #1f2937;
            backdrop-filter: blur(4px);
            z-index: 2;
        }

        .status-accepted,
        .status-success {
            background: #22c55e;
            color: white;
        }

        .status-cancelled,
        .status-failed {
            background: #ef4444;
            color: white;
        }

        .status-requested,
        .status-pending {
            background: #f59e0b;
            color: white;
        }

        .host-profile-overlay {
            position: absolute;
            bottom: 12px;
            left: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 2;
            background: rgba(255, 255, 255, 0.9);
            padding: 4px 12px 4px 4px;
            border-radius: 40px;
            backdrop-filter: blur(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .host-profile-overlay .host-img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .host-overlay-text {
            display: flex;
            flex-direction: column;
        }

        .host-overlay-text .host-label {
            font-size: 9px;
            font-weight: 800;
            color: #6b7280;
            text-transform: uppercase;
            line-height: 1;
            margin-bottom: 2px;
        }

        .host-overlay-text .host-name {
            font-size: 13px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
            margin-top: 0;
        }

        body.dark-mode .host-profile-overlay {
            background: rgba(30, 30, 30, 0.9);
        }

        body.dark-mode .host-overlay-text .host-name {
            color: #f9fafb;
        }

        body.dark-mode .host-overlay-text .host-label {
            color: #9ca3af;
        }

        .res-card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .res-room-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 4px;
            color: var(--body-text);
        }

        .res-location {
            font-size: 14px;
            color: var(--body-muted);
            margin: 0 0 16px;
        }

        .res-info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 16px;
        }

        .res-dates {
            display: flex;
            gap: 16px;
            background: rgba(0, 0, 0, 0.03);
            padding: 12px;
            border-radius: 12px;
            margin: 0;
        }

        body.dark-mode .res-dates {
            background: rgba(255, 255, 255, 0.03);
        }

        .date-box {
            flex: 1;
        }

        .date-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: var(--body-muted);
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .date-val {
            font-size: 14px;
            font-weight: 600;
            color: var(--body-text);
            white-space: nowrap;
        }

        .res-price {
            font-size: 20px;
            color: var(--accent);
            text-align: right;
            line-height: 1.2;
            font-weight: 800;
        }

        .res-actions {
            border-top: 1px solid var(--border);
            padding-top: 16px;
            margin-top: auto;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-action {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.2s;
            min-width: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-message {
            background: rgba(37, 99, 235, 0.1);
            color: var(--accent);
        }

        .btn-message:hover {
            background: var(--accent);
            color: white;
        }

        .btn-secondary {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--body-text);
        }

        .btn-secondary:hover {
            background: var(--border);
            color: var(--body-text);
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: var(--card-bg);
            border: 1px dashed var(--border);
            border-radius: 24px;
            grid-column: 1 / -1;
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: var(--body-muted);
            max-width: 400px;
            margin: 0 auto;
        }

        .pagination-wrapper {
            margin-top: 40px;
            display: flex;
            justify-content: center;
        }

        /* Modern Pagination Overrides (Tailwind / Bootstrap) */
        .pagination-wrapper nav {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            flex-wrap: wrap;
            gap: 16px;
        }

        .pagination-wrapper nav div.flex.justify-between {
            display: none;
        }

        /* Hide generic mobile text */
        .pagination-wrapper nav div.hidden.sm\:flex-1 {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            flex-direction: column;
            gap: 16px;
        }

        .pagination-wrapper nav div p.text-sm {
            display: none;
        }

        /* Hide the text "Showing 1 to 10..." */
        .pagination-wrapper nav span.relative.z-0.inline-flex,
        .pagination-wrapper ul.pagination {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            box-shadow: none;
            border-radius: 0;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .pagination-wrapper nav span.relative.z-0.inline-flex>span,
        .pagination-wrapper nav span.relative.z-0.inline-flex>a,
        .pagination-wrapper ul.pagination li .page-link,
        .pagination-wrapper ul.pagination li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            height: 44px;
            padding: 0 16px;
            border-radius: 12px !important;
            border: 1px solid var(--border) !important;
            background: var(--card-bg) !important;
            color: var(--body-text) !important;
            font-weight: 700;
            text-decoration: none;
            font-size: 14px;
            margin-left: 0 !important;
            transition: all 0.2s;
            line-height: 1;
            box-shadow: var(--shadow-sm);
        }

        .pagination-wrapper nav span.relative.z-0.inline-flex>a:hover,
        .pagination-wrapper ul.pagination li .page-link:hover {
            background: var(--border) !important;
            border-color: var(--body-muted) !important;
            transform: translateY(-2px);
        }

        /* Active states */
        .pagination-wrapper nav span[aria-current="page"]>span,
        .pagination-wrapper ul.pagination li.active span {
            background: var(--accent) !important;
            color: white !important;
            border-color: var(--accent) !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .pagination-wrapper nav svg {
            width: 18px;
            height: 18px;
            stroke-width: 2.5;
        }

        .pagination-wrapper nav span[aria-disabled="true"]>span,
        .pagination-wrapper ul.pagination li.disabled span {
            opacity: 0.5;
            box-shadow: none;
        }
    </style>

    <script>
        function setViewMode(mode) {
            const grid = document.getElementById('reservationGrid');
            const btns = document.querySelectorAll('.view-btn');

            btns.forEach(btn => btn.classList.remove('active'));

            if (mode === 'list') {
                grid.classList.add('list-view');
                btns[1].classList.add('active');
                localStorage.setItem('reservations_view_mode', 'list');
            } else {
                grid.classList.remove('list-view');
                btns[0].classList.add('active');
                localStorage.setItem('reservations_view_mode', 'card');
            }
        }

        // Load saved preference or force list on mobile
        document.addEventListener('DOMContentLoaded', () => {
            const savedMode = localStorage.getItem('reservations_view_mode');
            if (window.innerWidth <= 768) {
                setViewMode('list');
            } else if (savedMode === 'list') {
                setViewMode('list');
            }
        });
    </script>
@endsection