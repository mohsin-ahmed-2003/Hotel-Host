<style>
    .host-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 24px;
        box-shadow: var(--shadow-md);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .stat-icon.earnings {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .stat-icon.reservations {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .stat-content h3 {
        font-size: 12px;
        color: var(--body-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0 0 4px 0;
    }

    .stat-content .stat-value {
        font-size: 32px;
        font-weight: 800;
        color: var(--body-text);
        line-height: 1.2;
    }

    .filter-form {
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
    }

    .filter-select {
        box-sizing: border-box;
        padding: 0 16px;
        height: 44px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--card-bg);
        color: var(--body-text);
        font-size: 14px;
        font-weight: 600;
        outline: none;
        cursor: pointer;
        position: relative;
        z-index: 1;
        margin: 0;
    }

    .filter-settings-btn {
        box-sizing: border-box;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--card-bg);
        color: var(--body-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        margin: 0;
    }

    .filter-settings-btn:hover {
        background: rgba(14, 165, 233, 0.1);
        color: var(--accent);
        border-color: rgba(14, 165, 233, 0.3);
    }

    .filter-settings-menu {
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 8px;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: var(--shadow-lg);
        width: 200px;
        padding: 8px;
        z-index: 100;
        display: none;
    }

    .filter-settings-menu.active {
        display: block;
    }

    .filter-menu-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        color: var(--body-text);
        font-weight: 500;
        transition: background 0.2s;
    }

    .filter-menu-item:hover {
        background: rgba(14, 165, 233, 0.05);
    }

    .filter-menu-item.active {
        background: rgba(14, 165, 233, 0.1);
        color: var(--accent);
        font-weight: 700;
    }

    .dashboard-split {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 24px;
    }

    @media (max-width: 992px) {
        .dashboard-split {
            grid-template-columns: 1fr;
        }
    }

    .panel {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
    }

    .panel-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .panel-header h2 {
        font-size: 18px;
        font-weight: 800;
        margin: 0;
    }

    .panel-header .badge {
        background: rgba(14, 165, 233, 0.1);
        color: var(--accent);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .trips-list {
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .trip-card {
        display: flex;
        gap: 16px;
        padding: 16px;
        border-radius: 12px;
        border: 1px solid var(--border);
        transition: transform 0.2s, box-shadow 0.2s;
        text-decoration: none;
        color: inherit;
    }

    .trip-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--accent);
    }

    .trip-thumb {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        object-fit: cover;
    }

    .trip-info h4 {
        margin: 0 0 4px 0;
        font-size: 16px;
        font-weight: 700;
        color: var(--body-text);
    }

    .trip-info .trip-dates {
        font-size: 13px;
        color: var(--body-muted);
        margin-bottom: 8px;
    }

    .transaction-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .transaction-table th {
        background: rgba(0, 0, 0, 0.02);
        padding: 16px 24px;
        font-size: 13px;
        font-weight: 700;
        color: var(--body-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border);
    }

    body.dark-mode .transaction-table th {
        background: rgba(255, 255, 255, 0.02);
    }

    .transaction-table td {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
        color: var(--body-text);
        font-size: 15px;
        vertical-align: middle;
    }

    .clickable-row {
        cursor: pointer;
        transition: background 0.2s;
    }

    .clickable-row:hover {
        background: rgba(14, 165, 233, 0.03);
    }

    .transaction-table tr:last-child td {
        border-bottom: none;
    }

    .status-pill {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: capitalize;
        margin-top: 4px;
    }

    .status-success {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }

    .status-failed {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .room-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .room-thumb {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        object-fit: cover;
    }

    .room-name {
        font-weight: 700;
        color: var(--body-text);
        font-size: 16px;
        margin-bottom: 4px;
    }

    .room-meta {
        font-size: 13px;
        color: var(--body-muted);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 48px;
        color: var(--body-muted);
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .btn-show-more {
        display: block;
        width: 100%;
        background: rgba(0, 0, 0, 0.02);
        border: none;
        border-top: 1px solid var(--border);
        padding: 16px;
        text-align: center;
        font-weight: 700;
        color: var(--accent);
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-show-more:hover {
        background: rgba(14, 165, 233, 0.05);
    }

    body.dark-mode .btn-show-more {
        background: rgba(255, 255, 255, 0.02);
    }

    body.dark-mode .btn-show-more:hover {
        background: rgba(14, 165, 233, 0.1);
    }
</style>

<div class="host-header">
    <h1 class="page-title" style="margin: 0;">Host Dashboard</h1>

    <form action="{{ route('dashboard') }}" method="GET" class="filter-form" id="dashboardFilterForm">
        <input type="hidden" name="filter_column" id="filterColumn" value="{{ $filterColumn }}">

        <select name="filter" class="filter-select" onchange="updateDashboardAjax()">
            <option value="today" {{ $filter === 'today' ? 'selected' : '' }}>Today</option>
            <option value="yesterday" {{ $filter === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
            <option value="this_week" {{ $filter === 'this_week' ? 'selected' : '' }}>This Week</option>
            <option value="next_week" {{ $filter === 'next_week' ? 'selected' : '' }}>Next Week</option>
            <option value="this_month" {{ $filter === 'this_month' ? 'selected' : '' }}>This Month</option>
            <option value="previous_month" {{ $filter === 'previous_month' ? 'selected' : '' }}>Previous Month</option>
            <option value="this_year" {{ $filter === 'this_year' ? 'selected' : '' }}>This Year</option>
            <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Time</option>
        </select>

        <div class="filter-settings-btn" id="filterSettingsBtn" title="Filter Settings">
            <i class="fas fa-sliders-h"></i>
        </div>

        <div class="filter-settings-menu" id="filterSettingsMenu">
            <div
                style="font-size: 12px; color: var(--body-muted); text-transform: uppercase; font-weight: 700; margin-bottom: 8px; padding: 0 12px;">
                Filter By Field</div>
            <div class="filter-menu-item {{ $filterColumn === 'created_at' ? 'active' : '' }}"
                onclick="setFilterColumn('created_at')">
                <i class="fas fa-calendar-plus" style="width: 20px;"></i> Booked Date
            </div>
            <div class="filter-menu-item {{ $filterColumn === 'checkin' ? 'active' : '' }}"
                onclick="setFilterColumn('checkin')">
                <i class="fas fa-plane-arrival" style="width: 20px;"></i> Check-in Date
            </div>
            <div class="filter-menu-item {{ $filterColumn === 'checkout' ? 'active' : '' }}"
                onclick="setFilterColumn('checkout')">
                <i class="fas fa-plane-departure" style="width: 20px;"></i> Check-out Date
            </div>
        </div>
    </form>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon earnings"><i class="fas fa-wallet"></i></div>
        <div class="stat-content">
            <h3>Total Earnings</h3>
            <div class="stat-value">${{ number_format($totalEarnings, 2) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon reservations"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-content">
            <h3>Total Reservations</h3>
            <div class="stat-value">{{ number_format($totalReservations) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(168, 85, 247, 0.1); color: #a855f7;"><i class="fas fa-moon"></i>
        </div>
        <div class="stat-content">
            <h3>Nights Booked</h3>
            <div class="stat-value">{{ number_format($totalNights) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(236, 72, 153, 0.1); color: #ec4899;"><i class="fas fa-bed"></i>
        </div>
        <div class="stat-content">
            <h3>Rooms Booked</h3>
            <div class="stat-value">{{ $bookedRooms }} <span
                    style="font-size: 16px; color: var(--body-muted); font-weight: 500;">/ {{ $totalHostRooms }}</span>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-split">
    <!-- Left Side: User's Trips -->
    <div class="panel">
        <div class="panel-header">
            <h2>My Trips</h2>
            <div class="badge">Based on {{ str_replace('_', ' ', $filterColumn) }}</div>
        </div>

        <div class="trips-list" id="tripsContainer">
            @if($trips->count() > 0)
                @include('dashboard.partials.trip_rows')
            @else
                <div class="empty-state" style="padding: 40px 20px;">
                    <i class="fas fa-plane-slash"></i>
                    <h3>No trips found</h3>
                    <p style="color: var(--body-muted); font-size: 14px;">No trips match your filters.</p>
                </div>
            @endif
        </div>
        @if($trips->hasMorePages())
            <button class="btn-show-more" id="loadMoreTrips" data-page="2">Show More</button>
        @endif
    </div>

    <!-- Right Side: Property Reservations -->
    <div class="panel">
        <div class="panel-header">
            <h2>Hosting Transactions</h2>
            <div class="badge">Based on {{ str_replace('_', ' ', $filterColumn) }}</div>
        </div>

        <table class="transaction-table">
            <thead>
                <tr>
                    <th>Property & Guest</th>
                    <th style="text-align: right;">Amount & Status</th>
                </tr>
            </thead>
            <tbody id="reservationsContainer">
                @if($reservations->count() > 0)
                    @include('dashboard.partials.reservation_rows')
                @else
                    <tr>
                        <td colspan="2">
                            <div class="empty-state">
                                <i class="fas fa-receipt"></i>
                                <h3>No transactions found</h3>
                                <p style="color: var(--body-muted);">No reservations match your filters.</p>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        @if($reservations->hasMorePages())
            <button class="btn-show-more" id="loadMoreReservations" data-page="2">Show More</button>
        @endif
    </div>
</div>

<script>
    // Toggle Filter Settings Menu
    const filterSettingsBtn = document.getElementById('filterSettingsBtn');
    const filterSettingsMenu = document.getElementById('filterSettingsMenu');

    filterSettingsBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        filterSettingsMenu.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!filterSettingsMenu.contains(e.target) && !filterSettingsBtn.contains(e.target)) {
            filterSettingsMenu.classList.remove('active');
        }
    });

    function setFilterColumn(column) {
        document.getElementById('filterColumn').value = column;
        
        document.querySelectorAll('.filter-menu-item').forEach(item => item.classList.remove('active'));
        document.querySelector(`.filter-menu-item[onclick="setFilterColumn('${column}')"]`).classList.add('active');
        
        filterSettingsMenu.classList.remove('active');
        updateDashboardAjax();
    }

    function updateDashboardAjax() {
        const form = document.getElementById('dashboardFilterForm');
        const url = new URL(form.action);
        url.searchParams.set('filter', form.filter.value);
        url.searchParams.set('filter_column', form.filter_column.value);
        
        document.body.style.cursor = 'wait';
        document.querySelector('.stats-grid').style.opacity = '0.5';
        document.querySelector('.dashboard-split').style.opacity = '0.5';
        
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                document.querySelector('.stats-grid').innerHTML = doc.querySelector('.stats-grid').innerHTML;
                document.querySelector('.dashboard-split').innerHTML = doc.querySelector('.dashboard-split').innerHTML;
                
                document.querySelector('.stats-grid').style.opacity = '1';
                document.querySelector('.dashboard-split').style.opacity = '1';
                document.body.style.cursor = 'default';
                
                // re-bind load more events since DOM changed
                loadMore('trips', 'tripsContainer', 'loadMoreTrips');
                loadMore('reservations', 'reservationsContainer', 'loadMoreReservations');
            })
            .catch(err => {
                console.error(err);
                document.body.style.cursor = 'default';
                document.querySelector('.stats-grid').style.opacity = '1';
                document.querySelector('.dashboard-split').style.opacity = '1';
            });
    }

    // AJAX Load More Logic
    function loadMore(type, containerId, btnId) {
        const btn = document.getElementById(btnId);
        if (!btn) return;

        btn.addEventListener('click', function () {
            const page = this.getAttribute('data-page');
            const originalText = this.innerText;
            this.innerText = 'Loading...';
            this.disabled = true;

            const url = new URL(window.location.href);
            url.searchParams.set(type + '_page', page);
            url.searchParams.set('load_' + type, '1');

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    if (html.trim() !== '') {
                        document.getElementById(containerId).insertAdjacentHTML('beforeend', html);
                        this.setAttribute('data-page', parseInt(page) + 1);
                        this.innerText = originalText;
                        this.disabled = false;
                    } else {
                        this.remove(); // No more pages
                    }
                })
                .catch(err => {
                    console.error('Error loading more:', err);
                    this.innerText = originalText;
                    this.disabled = false;
                });
        });
    }

    loadMore('trips', 'tripsContainer', 'loadMoreTrips');
    loadMore('reservations', 'reservationsContainer', 'loadMoreReservations');
</script>