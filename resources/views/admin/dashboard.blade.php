@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your application')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 18px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: 0; right: 0;
        width: 80px; height: 80px;
        border-radius: 50%;
        opacity: 0.06;
        transform: translate(20px, -20px);
    }

    .stat-card.total::after  { background: #6366f1; }
    .stat-card.users::after  { background: #10b981; }
    .stat-card.admins::after { background: #f59e0b; }
    .stat-card.sub::after    { background: #3b82f6; }

    .stat-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 22px;
    }

    .stat-icon.total  { background: #ede9fe; }
    .stat-icon.users  { background: #dcfce7; }
    .stat-icon.admins { background: #fef3c7; }
    .stat-icon.sub    { background: #dbeafe; }

    .stat-info {}
    .stat-value {
        font-size: 32px;
        font-weight: 800;
        color: var(--text);
        line-height: 1;
        margin-bottom: 4px;
    }
    .stat-label {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
    }
    .stat-trend {
        font-size: 11px;
        font-weight: 600;
        margin-top: 4px;
        color: var(--success);
    }

    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 900px) {
        .bottom-grid { grid-template-columns: 1fr; }
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

    .chart-wrap {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chart-center-label {
        position: absolute;
        text-align: center;
        pointer-events: none;
    }

    .chart-center-label .big   { font-size: 28px; font-weight: 800; color: var(--text); }
    .chart-center-label .small { font-size: 12px; color: var(--text-muted); font-weight: 500; }

    .legend {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 24px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .legend-left {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 500;
    }

    .legend-dot {
        width: 12px; height: 12px;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .legend-count {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
    }

    .legend-bar-wrap {
        height: 4px;
        background: var(--bg);
        border-radius: 4px;
        margin-top: 4px;
        overflow: hidden;
    }

    .legend-bar {
        height: 100%;
        border-radius: 4px;
        transition: width 1s ease;
    }

    /* Recent users table card */
    .recent-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
    }

    .recent-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .recent-title { font-size: 16px; font-weight: 700; }

    .recent-table td { padding: 12px 16px; }
    .recent-table th { padding: 10px 16px; }
</style>
@endsection

@section('content')

<!-- Stat Cards -->
<div class="stats-grid">
    <div class="stat-card total">
        <div class="stat-icon total">
            <svg width="22" height="22" fill="none" stroke="#6366f1" stroke-width="2" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-value" data-count="{{ $totalAll }}">0</div>
            <div class="stat-label">Total Accounts</div>
        </div>
    </div>

    <div class="stat-card users">
        <div class="stat-icon users">
            <svg width="22" height="22" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-value" data-count="{{ $totalUsers }}">0</div>
            <div class="stat-label">Regular Users</div>
        </div>
    </div>

    <div class="stat-card admins">
        <div class="stat-icon admins">
            <svg width="22" height="22" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-value" data-count="{{ $totalAdmins }}">0</div>
            <div class="stat-label">Admins</div>
        </div>
    </div>

    <div class="stat-card sub">
        <div class="stat-icon sub">
            <svg width="22" height="22" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-value" data-count="{{ $totalSubAdmins }}">0</div>
            <div class="stat-label">Sub Admins</div>
        </div>
    </div>
</div>

<!-- Bottom Grid: Chart + Recent Users -->
<div class="bottom-grid">

    <!-- Pie Chart -->
    <div class="chart-card">
        <div class="chart-card-title">User Distribution</div>
        <div class="chart-card-sub">Breakdown by role across all accounts</div>

        <div class="chart-wrap">
            <canvas id="roleChart" width="220" height="220"></canvas>
            <div class="chart-center-label">
                <div class="big">{{ $totalAll }}</div>
                <div class="small">Total</div>
            </div>
        </div>

        <div class="legend">
            @php
                $legendItems = [
                    ['label' => 'Regular Users', 'count' => $totalUsers,     'color' => '#10b981'],
                    ['label' => 'Admins',         'count' => $totalAdmins,    'color' => '#f59e0b'],
                    ['label' => 'Sub Admins',     'count' => $totalSubAdmins, 'color' => '#3b82f6'],
                ];
            @endphp
            @foreach($legendItems as $item)
            <div>
                <div class="legend-item">
                    <div class="legend-left">
                        <div class="legend-dot" style="background:{{ $item['color'] }};"></div>
                        {{ $item['label'] }}
                    </div>
                    <div class="legend-count">{{ $item['count'] }}</div>
                </div>
                <div class="legend-bar-wrap">
                    <div class="legend-bar"
                         style="background:{{ $item['color'] }};width:0%;"
                         data-width="{{ $totalAll > 0 ? round(($item['count'] / $totalAll) * 100) : 0 }}%">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Users -->
    <div class="recent-card">
        <div class="recent-header">
            <div class="recent-title">Recent Accounts</div>
            <a href="{{ route('admin.users') }}" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="table-wrap">
            <table class="recent-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $user)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div>
                                    <div class="user-cell-name">{{ $user->name }}</div>
                                    <div class="user-cell-email">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge badge-{{ $user->role }}">{{ ucfirst(str_replace('_',' ',$user->role)) }}</span></td>
                        <td style="font-size:12px;color:var(--text-muted);">{{ $user->created_at?->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:30px;">No accounts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // ── Count-up animation ──
    document.querySelectorAll('.stat-value[data-count]').forEach(el => {
        const target = parseInt(el.dataset.count);
        if (target === 0) { el.textContent = '0'; return; }
        let current = 0;
        const step = Math.ceil(target / 40);
        const timer = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current;
            if (current >= target) clearInterval(timer);
        }, 30);
    });

    // ── Legend bar animation ──
    setTimeout(() => {
        document.querySelectorAll('.legend-bar').forEach(bar => {
            bar.style.width = bar.dataset.width;
        });
    }, 300);

    // ── Pie Chart (pure canvas, no library) ──
    const canvas  = document.getElementById('roleChart');
    const ctx     = canvas.getContext('2d');
    const cx = canvas.width / 2;
    const cy = canvas.height / 2;
    const radius  = 85;
    const inner   = 55;

    const data = [
        { value: {{ $totalUsers }},     color: '#10b981', label: 'Users' },
        { value: {{ $totalAdmins }},    color: '#f59e0b', label: 'Admins' },
        { value: {{ $totalSubAdmins }}, color: '#3b82f6', label: 'Sub Admins' },
    ];

    const total = data.reduce((s, d) => s + d.value, 0);

    function drawChart(progress) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (total === 0) {
            // Empty state ring
            ctx.beginPath();
            ctx.arc(cx, cy, radius, 0, Math.PI * 2);
            ctx.arc(cx, cy, inner, Math.PI * 2, 0, true);
            ctx.fillStyle = '#e2e8f0';
            ctx.fill();
            return;
        }

        let startAngle = -Math.PI / 2;
        const gap = 0.03;

        data.forEach(slice => {
            if (slice.value === 0) return;
            const sliceAngle = (slice.value / total) * Math.PI * 2 * progress;

            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, radius, startAngle + gap, startAngle + sliceAngle - gap);
            ctx.arc(cx, cy, inner, startAngle + sliceAngle - gap, startAngle + gap, true);
            ctx.closePath();
            ctx.fillStyle = slice.color;
            ctx.fill();

            startAngle += sliceAngle;
        });
    }

    // Animate draw
    let progress = 0;
    const animTimer = setInterval(() => {
        progress = Math.min(progress + 0.04, 1);
        drawChart(progress);
        if (progress >= 1) clearInterval(animTimer);
    }, 16);
</script>
@endsection
