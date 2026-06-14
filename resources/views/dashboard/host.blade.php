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

    .custom-status-dropdown {
        position: relative;
        user-select: none;
    }

    .status-trigger {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid var(--border);
        background: var(--card-bg);
        color: var(--body-text);
        transition: all 0.2s;
        height: 44px;
        box-sizing: border-box;
    }

    .status-trigger:hover {
        border-color: var(--accent);
    }

    .status-options {
        position: absolute;
        top: 100%;
        left: 0;
        margin-top: 8px;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: var(--shadow-lg);
        width: 100%;
        min-width: 150px;
        padding: 8px;
        z-index: 100;
        display: none;
    }

    .status-options.active {
        display: block;
    }

    .status-option {
        padding: 10px 12px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        color: var(--body-text);
        font-weight: 500;
        transition: background 0.2s;
    }

    .status-option:hover {
        background: rgba(14, 165, 233, 0.05);
        color: var(--accent);
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

    .status-success, .status-accepted, .status-confirmed {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }
    
    .status-requested {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .status-failed, .status-cancelled, .status-rejected {
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
        <input type="hidden" name="filter" id="filterValue" value="{{ $filter }}">

        <div class="custom-status-dropdown" style="margin: 0; min-width: 150px;">
            <div class="status-trigger" onclick="document.getElementById('filterOptionsList').classList.toggle('active')">
                <span class="status-text" id="selectedFilterText">{{ ucwords(str_replace('_', ' ', $filter)) }}</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px; margin-left:auto;">
                    <polyline points="6 9 12 15 18 9" />
                </svg>
            </div>
            <div class="status-options" id="filterOptionsList">
                @php
                    $filterOpts = [
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'this_week' => 'This Week',
                        'next_week' => 'Next Week',
                        'this_month' => 'This Month',
                        'previous_month' => 'Previous Month',
                        'this_year' => 'This Year',
                        'all' => 'All Time'
                    ];
                @endphp
                @foreach($filterOpts as $key => $label)
                    <div class="status-option" onclick="setFilter('{{ $key }}', '{{ $label }}')">{{ $label }}</div>
                @endforeach
            </div>
        </div>

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
    <!-- Left Side: User's Trips & Reservations -->
    <div class="panel">
        <div class="panel-header">
            <div class="custom-status-dropdown" style="margin: 0; min-width: 180px;">
                <div class="status-trigger" onclick="document.getElementById('panelOptions').classList.toggle('active')">
                    <span class="status-text" id="leftPanelTitle" style="font-size: 16px;">My Trips</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px; margin-left: auto;">
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                </div>
                <div class="status-options" id="panelOptions">
                    <div class="status-option" onclick="switchLeftPanel('trips', 'My Trips')">My Trips</div>
                    <div class="status-option" onclick="switchLeftPanel('reservations', 'My Reservations')">My Reservations</div>
                </div>
            </div>
            <div class="badge">Based on {{ str_replace('_', ' ', $filterColumn) }}</div>
        </div>

        <div id="tripsWrapper">
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

        <div id="reservationsWrapper" style="display:none;">
            <div class="trips-list" id="reservationsContainer">
                @if($reservations->count() > 0)
                    @include('dashboard.partials.reservation_rows')
                @else
                    <div class="empty-state" style="padding: 40px 20px;">
                        <i class="fas fa-receipt"></i>
                        <h3>No transactions found</h3>
                        <p style="color: var(--body-muted);">No reservations match your filters.</p>
                    </div>
                @endif
            </div>
            @if($reservations->hasMorePages())
                <button class="btn-show-more" id="loadMoreReservations" data-page="2">Show More</button>
            @endif
        </div>
    </div>

    <!-- Right Side: Inbox / Notifications -->
    <div class="panel">
        <div class="panel-header" style="padding-bottom: 0; border-bottom: none;">
            <div style="display:flex; gap: 24px;">
                <div class="tab-btn active" style="font-size: 16px; font-weight: 700; padding-bottom: 16px; border-bottom: 2px solid var(--accent); color: var(--body-text); cursor: pointer;" onclick="switchRightTab('inbox', this)">Inbox</div>
                <div class="tab-btn" style="font-size: 16px; font-weight: 700; padding-bottom: 16px; color: var(--body-muted); cursor: pointer; border-bottom: 2px solid transparent;" onclick="switchRightTab('notifications', this)">Notifications</div>
            </div>
        </div>
        <div style="border-top: 1px solid var(--border);">
            <div id="inboxContent" class="empty-state" style="padding: 60px 20px;">
                <i class="far fa-comments"></i>
                <h3>No Messages</h3>
                <p style="color: var(--body-muted);">You have no new messages in your inbox.</p>
            </div>
            <div id="notificationsContent" style="display:none; max-height: 600px; overflow-y: auto;">
                @if(isset($notifications) && $notifications->count() > 0)
                    <div class="notifications-list">
                        @foreach($notifications as $notification)
                            <a href="{{ $notification->data['url'] ?? '#' }}" class="notification-card" style="display:flex; gap: 16px; padding: 16px; border-bottom: 1px solid var(--border); text-decoration: none; align-items: flex-start; {{ is_null($notification->read_at) ? 'background-color: rgba(99, 102, 241, 0.05); border-left: 3px solid var(--accent);' : 'border-left: 3px solid transparent;' }} transition: all 0.2s ease;">
                                <div class="notification-icon" style="width: 40px; height: 40px; border-radius: 50%; background-color: {{ $notification->data['color'] ?? 'var(--accent)' }}15; color: {{ $notification->data['color'] ?? 'var(--accent)' }}; display:flex; align-items:center; justify-content:center; flex-shrink: 0; font-size: 16px;">
                                    <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }}"></i>
                                </div>
                                <div style="flex-grow: 1;">
                                    <h4 style="margin: 0 0 4px 0; color: var(--body-text); font-size: 15px; font-weight: 600;">{{ $notification->data['title'] ?? 'Notification' }}</h4>
                                    <p style="margin: 0 0 8px 0; color: var(--body-muted); font-size: 13px; line-height: 1.4;">{{ $notification->data['message'] ?? '' }}</p>
                                    <span style="font-size: 11px; color: #94a3b8; font-weight: 500;">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                @if(is_null($notification->read_at))
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--accent); margin-top: 6px;"></div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state" style="padding: 60px 20px;">
                        <i class="far fa-bell"></i>
                        <h3>No Notifications</h3>
                        <p style="color: var(--body-muted);">You're all caught up!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle Filter Settings Menu
    const filterSettingsBtn = document.getElementById('filterSettingsBtn');

    filterSettingsBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        document.getElementById('filterSettingsMenu')?.classList.toggle('active');
        document.getElementById('filterOptionsList')?.classList.remove('active');
        document.getElementById('panelOptions')?.classList.remove('active');
    });

    document.addEventListener('click', (e) => {
        if (!document.getElementById('filterSettingsMenu')?.contains(e.target) && !filterSettingsBtn.contains(e.target)) {
            document.getElementById('filterSettingsMenu')?.classList.remove('active');
        }
        if (!e.target.closest('.custom-status-dropdown')) {
            document.getElementById('filterOptionsList')?.classList.remove('active');
            document.getElementById('panelOptions')?.classList.remove('active');
        }
    });

    function setFilter(value, label) {
        document.getElementById('filterValue').value = value;
        document.getElementById('selectedFilterText').innerText = label;
        document.getElementById('filterOptionsList')?.classList.remove('active');
        updateDashboardAjax();
    }

    function switchLeftPanel(type, title) {
        document.getElementById('leftPanelTitle').innerText = title;
        document.getElementById('panelOptions')?.classList.remove('active');

        if(type === 'trips') {
            document.getElementById('tripsWrapper').style.display = 'block';
            document.getElementById('reservationsWrapper').style.display = 'none';
        } else {
            document.getElementById('tripsWrapper').style.display = 'none';
            document.getElementById('reservationsWrapper').style.display = 'block';
        }
    }

    function switchRightTab(tab, element) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.style.borderBottom = '2px solid transparent';
            btn.style.color = 'var(--body-muted)';
        });
        element.style.borderBottom = '2px solid var(--accent)';
        element.style.color = 'var(--body-text)';

        if(tab === 'inbox') {
            document.getElementById('inboxContent').style.display = 'block';
            document.getElementById('notificationsContent').style.display = 'none';
        } else {
            document.getElementById('inboxContent').style.display = 'none';
            document.getElementById('notificationsContent').style.display = 'block';
        }
    }

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
        url.searchParams.set('filter', document.getElementById('filterValue').value);
        url.searchParams.set('filter_column', document.getElementById('filterColumn').value);
        
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