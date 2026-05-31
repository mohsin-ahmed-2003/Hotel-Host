@extends('admin.layout')

@section('title', 'Manage Users')
@section('page-title', 'Manage Users')
@section('page-subtitle', 'View, add, edit and delete users')

@section('styles')
<style>
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .stat-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex; align-items: center; gap: 14px;
    }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .stat-value { font-size: 22px; font-weight: 800; color: var(--text); }
    .stat-label { font-size: 12px; color: var(--text-muted); font-weight: 500; }
</style>
@endsection

@section('content')

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon" style="background:#e0e7ff;">👥</div>
        <div>
            <div class="stat-value">{{ $users->count() }}</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dcfce7; color:red">♂</div>
        <div>
            <div class="stat-value">{{ $users->where('gender','male')->count() }}</div>
            <div class="stat-label">Male</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fce7f3; color:red">♀</div>
        <div>
            <div class="stat-value">{{ $users->where('gender','female')->count() }}</div>
            <div class="stat-label">Female</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">All Users</span>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <div class="search-box">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" class="search-input" id="searchInput" placeholder="Search users...">
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add User
            </a>
        </div>
    </div>

    <div class="table-wrap">
        @if($users->isEmpty())
            <div class="empty-state">
                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                </svg>
                <h3>No users found</h3>
                <p>Start by adding your first user.</p>
            </div>
        @else
        <table id="usersTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Country</th>
                    <th>Login Type</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $i => $user)
                <tr>
                    <td style="color:var(--text-muted);font-size:13px;">{{ $i + 1 }}</td>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <div>
                                <div class="user-cell-name">{{ $user->name }}</div>
                                <div class="user-cell-email">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;">{{ $user->phone }}</td>
                    <td><span class="badge badge-{{ $user->gender }}">{{ ucfirst($user->gender) }}</span></td>
                    <td style="font-size:13px;">
                        {{ $user->countryRelation?->country_name ?? $user->country ?? '—' }}
                    </td>
                    <td>
                        @php $lt = $user->login_type ?? 'form'; @endphp
                        @if($lt === 'google')
                            <span class="badge" style="background:rgba(66,133,244,0.12);color:#4285F4;">
                                <svg width="11" height="11" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:3px;"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                Google
                            </span>
                        @elseif($lt === 'facebook')
                            <span class="badge" style="background:rgba(24,119,242,0.12);color:#1877F2;">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:3px;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                Facebook
                            </span>
                        @elseif($lt === 'apple')
                            <span class="badge" style="background:rgba(0,0,0,0.08);color:var(--text, #000);">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:3px;"><path d="M17.05 20.28c-.98.95-2.05 1.78-3.14 1.76-1.09-.02-1.74-.46-2.87-.46-1.13 0-1.9.46-2.86.48-1.1.02-2.11-.87-3.12-1.85-2.01-1.98-3.04-5.63-1.63-8.03.71-1.22 1.95-1.99 3.23-2.01 1.03-.02 1.91.56 2.6.56.68 0 1.74-.69 3-.56 1.25.13 2.19.74 2.83 1.62-2.58 1.41-2.13 4.88.46 6.13-.59 1.36-1.41 2.36-1.5 2.36zM12.03 7.25c-.02-2.13 1.6-4.05 3.52-4.25.21 2.25-1.71 4.29-3.52 4.25z"/></svg>
                                Apple
                            </span>
                        @else
                            <span class="badge" style="background:rgba(99,102,241,0.12);color:var(--primary, #6366F1);">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:3px;"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                Website
                            </span>
                        @endif
                    </td>
                    <td>
                        <select class="status-select" data-id="{{ $user->id }}"
                                style="padding:5px 10px;border-radius:8px;border:1.5px solid var(--border);background:var(--bg);color:var(--text);font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;">
                            <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </td>
                    <td style="font-size:13px;color:var(--text-muted);">{{ $user->created_at?->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </a>
                            <button onclick="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ route('admin.users.delete', $user) }}')"
                                    class="btn btn-danger btn-sm">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    document.getElementById('searchInput')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#usersTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    document.querySelectorAll('.status-select').forEach(function(sel) {
        sel.addEventListener('change', function() {
            const userId   = this.dataset.id;
            const isActive = this.value;
            const original = isActive === '1' ? '0' : '1';

            fetch('/admin/users/' + userId + '/toggle-status', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
                body: JSON.stringify({ is_active: isActive })
            })
            .then(r => r.json())
            .then(data => {
                this.value = data.is_active ? '1' : '0';
                this.style.color = data.is_active ? 'var(--success)' : 'var(--danger)';
            })
            .catch(() => { this.value = original; });
        });

        // Set initial color
        sel.style.color = sel.value === '1' ? 'var(--success)' : 'var(--danger)';
    });
</script>
@endsection
