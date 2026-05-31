@extends('layouts.app')

@section('content')
    <div class="properties-dashboard-wrapper">
        <div class="dashboard-container">
            <!-- Header Section -->
            <div class="dashboard-header">
                <div class="header-left">
                    <h1 class="dashboard-title">Property Dashboard</h1>
                    <p class="dashboard-subtitle">Manage your listings, track approvals, and host new spaces.</p>
                </div>
                <a href="{{ route('host.start') }}" class="btn-host-premium">
                    <div class="btn-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </div>
                    <span>Host New Property</span>
                </a>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card listed">
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value">{{ $listedRooms->count() }}</span>
                        <span class="stat-label">Listed Properties</span>
                    </div>
                </div>
                <div class="stat-card pending">
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value">{{ $unlistedRooms->where('status', 'pending')->count() }}</span>
                        <span class="stat-label">Pending Approval</span>
                    </div>
                </div>
                <div class="stat-card incomplete">
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                            </path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <div class="stat-info">
                        @php 
                            $incompleteCount = $unlistedRooms->filter(fn($r) => $r->countMissingSteps() > 0)->count();
                        @endphp
                        <span class="stat-value">{{ $incompleteCount }}</span>
                        <span class="stat-label">Incomplete Drafts</span>
                    </div>
                </div>
            </div>

            <!-- Listed Section -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">Active Listings</h2>
                    <div class="section-badge">{{ $listedRooms->count() }}</div>
                </div>
            <div class="properties-list-card">
                @if($listedRooms->isEmpty())
                    <div class="empty-state-card">
                        <div class="empty-icon">🏙️</div>
                        <h3>No active listings</h3>
                            <p>Once your properties are approved, they will appear here.</p>
                    </div>
                @else
                    <div class="list-wrapper">
                            @foreach($listedRooms as $room)
                                @include('user.partials.room_card', ['room' => $room])
                            @endforeach
                            </div>
                @endif
                </div>
            </div>

            <!-- Inactive/Pending Section -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">Drafts & Pending Approval</h2>
                    <div class="section-badge muted">{{ $unlistedRooms->count() }}</div>
                </div>
            <div class="properties-list-card">
                @if($unlistedRooms->isEmpty())
                    <div class="empty-state-card">
                        <div class="empty-icon">📝</div>
                        <h3>No drafts found</h3>
                            <p>Start hosting to see your draft properties here.</p>
                    </div>
                @else
                    <div class="list-wrapper">
                            @foreach($unlistedRooms as $room)
                                @include('user.partials.room_card', ['room' => $room])
                            @endforeach
                            </div>
                @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .properties-dashboard-wrapper {
            min-height: 100vh;
            padding: 20px 20px;
            background: var(--bg-primary);
        }

        .dashboard-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Header Styles */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            gap: 20px;
        }

        .dashboard-title {
            font-size: 32px;
            font-weight: 850;
            color: var(--body-text);
            letter-spacing: -1px;
            margin-bottom: 8px;
        }

        .dashboard-subtitle {
            color: var(--body-muted);
            font-size: 16px;
            margin: 0;
        }

        .btn-host-premium {
            background: linear-gradient(135deg, var(--accent), #1d4ed8);
            color: #fff !important;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            box-sizing: border-box;
            white-space: nowrap;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
        }

        .btn-host-premium:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.3);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 50px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            padding: 24px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
            box-shadow: var(--card-shadow);
        }

        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-card.listed { border-color: rgba(34, 197, 94, 0.3); }
        .stat-card.listed .stat-icon { background: rgba(34, 197, 94, 0.1); color: #22c55e; }

        .stat-card.pending { border-color: rgba(59, 130, 246, 0.3); }
        .stat-card.pending .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }

        .stat-card.incomplete { border-color: rgba(239, 68, 68, 0.3); }
        .stat-card.incomplete .stat-icon { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        .stat-value {
            display: block;
            font-size: 24px;
            font-weight: 800;
            color: var(--body-text);
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--body-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Section Styles */
        .dashboard-section {
            margin-bottom: 50px;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--body-text);
            margin: 0;
        }

        .section-badge {
            background: #22c55e;
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            padding: 4px 14px;
            border-radius: 50px;
        }

        .section-badge.muted {
            background: var(--border);
            color: var(--body-muted);
        }

        .properties-list-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 28px;
               overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }

        .empty-state-card {
            padding: 80px 40px;
            text-align: center;
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .empty-state-card h3 {
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--body-text);
        }

        .empty-state-card p {
            color: var(--body-muted);
            max-width: 300px;
            margin: 0 auto;
        }



        @media (max-width: 991px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .dashboard-title { font-size: 28px; }
        }

        @media (max-width: 768px) {
            .dashboard-header { flex-direction: column; align-items: flex-start; gap: 16px; }
            .dashboard-title { font-size: 24px; }
            .btn-host-premium { 
                width: 100%; 
                justify-content: center; 
                padding: 10px 20px; 
                font-size: 15px; 
            }
            .stats-grid { grid-template-columns: 1fr; gap: 16px; }
            .properties-dashboard-wrapper { padding: 30px 16px; }
        }
    </style>
@endsection
