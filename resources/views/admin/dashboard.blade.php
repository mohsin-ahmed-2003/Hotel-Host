@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Overview')
@section('page-subtitle', 'Platform Statistics & Revenue')

@section('styles')
    <style>
        /* Filter Section */
        .filter-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
        }

        .custom-select-wrap {
            position: relative;
        }

        .filter-select {
            padding: 0 16px;
            height: 44px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--card);
            color: var(--text);
            font-size: 14px;
            font-weight: 600;
            outline: none;
            cursor: pointer;
            min-width: 180px;
            box-sizing: border-box;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 12px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .icon-users {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
        }

        .icon-rooms {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .icon-res {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .icon-sub {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .icon-earn {
            background: rgba(236, 72, 153, 0.1);
            color: #ec4899;
        }

        .stat-details {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
        }

        .stat-value {
            font-size: 19px;
            font-weight: 800;
            color: var(--text);
            line-height: 1.1;
            margin-bottom: 2px;
        }

        .stat-label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-meta {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            margin-top: 2px;
        }

        .stat-meta span {
            color: var(--text);
            font-weight: 700;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
        }

        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        .chart-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
        }

        .chart-card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
        }

        .chart-card-sub {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        /* Animation class */
        .fade-update {
            animation: fadeUpdate 0.5s ease-in-out;
        }

        @keyframes fadeUpdate {
            0% {
                opacity: 0.5;
                transform: scale(0.98);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')

    <div class="filter-section">
        <div class="custom-select-wrap">
            <select class="filter-select" id="dashboardFilter" onchange="updateDashboard()">
                <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Time</option>
                <option value="today" {{ $filter === 'today' ? 'selected' : '' }}>Today</option>
                <option value="yesterday" {{ $filter === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                <option value="this_week" {{ $filter === 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="this_month" {{ $filter === 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="previous_month" {{ $filter === 'previous_month' ? 'selected' : '' }}>Previous Month</option>
                <option value="this_year" {{ $filter === 'this_year' ? 'selected' : '' }}>This Year</option>
                <option value="previous_year" {{ $filter === 'previous_year' ? 'selected' : '' }}>Previous Year</option>
            </select>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="stats-grid" id="statsGrid">
        <div class="stat-card" id="card-users">
            <div class="stat-icon icon-users"><i class="fas fa-users"></i></div>
            <div class="stat-details">
                <div class="stat-value" id="val-users">{{ number_format($totalUsers) }}</div>
                <div class="stat-label">Regular Users</div>
            </div>
        </div>

        <div class="stat-card" id="card-rooms">
            <div class="stat-icon icon-rooms"><i class="fas fa-home"></i></div>
            <div class="stat-details">
                <div class="stat-value" id="val-rooms">{{ number_format($totalRooms) }}</div>
                <div class="stat-label">Total Rooms</div>
                <div class="stat-meta">
                    Approved: <span id="val-live-rooms">{{ number_format($liveRooms) }}</span>
                </div>
            </div>
        </div>

        <div class="stat-card" id="card-res">
            <div class="stat-icon icon-res"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-details">
                <div class="stat-value" id="val-res">{{ number_format($totalReservations) }}</div>
                <div class="stat-label">Reservations</div>
                <div class="stat-meta">
                    Revenue:&nbsp;<span
                        id="val-res-rev">{{ $currencySymbol }}{{ number_format($reservationRevenue, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="stat-card" id="card-sub">
            <div class="stat-icon icon-sub"><i class="fas fa-medal"></i></div>
            <div class="stat-details">
                <div class="stat-value" id="val-sub">{{ number_format($activeSubscriptions) }}</div>
                <div class="stat-label">Active Subs</div>
                <div class="stat-meta">
                    Revenue:&nbsp;<span
                        id="val-sub-rev">{{ $currencySymbol }}{{ number_format($subscriptionRevenue, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="stat-card" id="card-earn">
            <div class="stat-icon icon-earn"><i class="fas fa-wallet"></i></div>
            <div class="stat-details">
                <div class="stat-value" id="val-earn">{{ $currencySymbol }}{{ number_format($totalEarnings, 2) }}</div>
                <div class="stat-label">Platform Revenue</div>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="charts-grid">
        <!-- Revenue Chart -->
        <div class="chart-card">
            <div class="chart-card-title">Revenue Overview</div>
            <div class="chart-card-sub">Monthly earnings from Reservations & Subscriptions (Last 6 Months)</div>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- User Growth Chart -->
        <div class="chart-card">
            <div class="chart-card-title">User Growth</div>
            <div class="chart-card-sub">New accounts registered (Last 6 Months)</div>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="userChart"></canvas>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        // Setup Charts
        Chart.defaults.color = 'var(--text-muted)';
        Chart.defaults.font.family = "'Inter', sans-serif";

        const months = @json($months);
        const reservationData = @json($reservationData);
        const subscriptionData = @json($subscriptionData);
        const userGrowth = @json($userGrowth);
        const currencySymbol = '{!! $currencySymbol !!}';

        // Revenue Chart (Bar)
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctxRev, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: `Reservations (${currencySymbol})`,
                        data: reservationData,
                        backgroundColor: 'rgba(245, 158, 11, 0.8)',
                        borderRadius: 4
                    },
                    {
                        label: `Subscriptions (${currencySymbol})`,
                        data: subscriptionData,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { position: 'top' }
                },
                animation: { duration: 1000, easing: 'easeOutQuart' }
            }
        });

        // User Growth Chart (Line)
        const ctxUser = document.getElementById('userChart').getContext('2d');

        // Create gradient
        const gradient = ctxUser.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

        const userChart = new Chart(ctxUser, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'New Users',
                    data: userGrowth,
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6366f1',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { precision: 0 } },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { display: false }
                },
                animation: { duration: 1000, easing: 'easeOutQuart' }
            }
        });

        // AJAX Filter Update
        function updateDashboard() {
            const filter = document.getElementById('dashboardFilter').value;
            const url = new URL(window.location.href);
            url.searchParams.set('filter', filter);

            const cards = document.querySelectorAll('.stat-card');
            cards.forEach(card => {
                card.classList.remove('fade-update');
                card.style.opacity = '0.5';
            });

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('val-users').innerText = data.totalUsers;
                    document.getElementById('val-rooms').innerText = data.totalRooms;
                    document.getElementById('val-live-rooms').innerText = data.liveRooms;
                    document.getElementById('val-res').innerText = data.totalReservations;
                    document.getElementById('val-res-rev').innerText = data.currencySymbol + data.reservationRevenue;
                    document.getElementById('val-sub').innerText = data.activeSubscriptions;
                    document.getElementById('val-sub-rev').innerText = data.currencySymbol + data.subscriptionRevenue;
                    document.getElementById('val-earn').innerText = data.currencySymbol + data.totalEarnings;

                    cards.forEach(card => {
                        card.style.opacity = '1';
                        // Trigger reflow
                        void card.offsetWidth;
                        card.classList.add('fade-update');
                    });
                })
                .catch(err => {
                    console.error('Error fetching data:', err);
                    cards.forEach(card => { card.style.opacity = '1'; });
                });
        }
    </script>
@endsection