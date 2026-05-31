@extends('admin.layout')

@section('title', 'Manage Admins')
@section('page-title', 'Manage Admins')
@section('page-subtitle', 'Manage roles and permissions for all users')

@section('styles')
<style>
    .role-form {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .permissions-panel {
        display: none;
        margin-top: 14px;
        background: var(--bg-2);
        border: 1.5px solid var(--border);
        border-radius: 12px;
        padding: 16px 20px;
    }

    .permissions-panel.visible { display: block; }

    .permissions-panel h4 {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .perm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px; }

    .perm-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        color: var(--text);
        transition: border-color 0.2s, background 0.2s;
    }

    .perm-item:has(input:checked) {
        border-color: var(--primary);
        background: var(--primary-light);
        color: #a5b4fc;
    }

    .perm-item input[type="checkbox"] {
        accent-color: var(--primary);
        width: 15px; height: 15px;
    }

    .user-row-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 20px 24px;
        margin-bottom: 14px;
        transition: box-shadow 0.2s;
    }

    .user-row-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.3); }

    .user-row-top { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }

    .user-meta { display: flex; align-items: center; gap: 12px; }
    .user-details-name { font-size: 15px; font-weight: 700; color: var(--text); }
    .user-details-email { font-size: 13px; color: var(--text-muted); }
</style>
@endsection

@section('content')

@php
    $modules = [
        'manage_users'  => ['label' => 'Manage Users',  'icon' => '👥'],
        'manage_admins' => ['label' => 'Manage Admins', 'icon' => '🛡️'],
    ];
@endphp

@if($admins->isEmpty())
    <div class="card">
        <div class="empty-state">
            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            <h3>No admins found</h3>
            <p>Promote a user to admin from the Users section.</p>
        </div>
    </div>
@else
    @foreach($admins as $admin)
    <div class="user-row-card">
        <div class="user-row-top">
            <div class="user-meta">
                <div class="user-avatar" style="width:46px;height:46px;font-size:17px;border-radius:12px;">
                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                </div>
                <div>
                    <div class="user-details-name">{{ $admin->name }}</div>
                    <div class="user-details-email">{{ $admin->email }} &bull; {{ $admin->phone }}</div>
                </div>
            </div>
            <span class="badge badge-{{ $admin->role }}">{{ ucfirst(str_replace('_', ' ', $admin->role)) }}</span>
        </div>

        <form action="{{ route('admin.admins.role', $admin) }}" method="POST" class="role-update-form" style="margin-top:16px;">
            @csrf @method('PUT')

            <div class="role-form">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px;">USER ROLE</label>
                    <select name="role" class="form-control" style="width:180px;"
                            onchange="handleRoleChange(this)">
                        <option value="user"      {{ $admin->role === 'user'      ? 'selected' : '' }}>User</option>
                        <option value="sub_admin" {{ $admin->role === 'sub_admin' ? 'selected' : '' }}>Sub Admin</option>
                        <option value="admin"     {{ $admin->role === 'admin'     ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div style="align-self:flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding:14px 20px;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        Update Role
                    </button>
                </div>
            </div>

            <!-- Permissions panel -->
            <div class="permissions-panel {{ in_array($admin->role, ['admin','sub_admin']) ? 'visible' : '' }}"
                 id="perms-{{ $admin->id }}">
                <h4>Module Access Permissions</h4>
                <div class="perm-grid">
                    @foreach($modules as $key => $mod)
                    <label class="perm-item">
                        <input type="checkbox" name="permissions[]" value="{{ $key }}"
                            {{ ($admin->role === 'admin' || in_array($key, $admin->permissions ?? [])) ? 'checked' : '' }}
                            {{ $admin->role === 'admin' ? 'disabled' : '' }}>
                        {{ $mod['icon'] }} {{ $mod['label'] }}
                    </label>
                    @endforeach
                </div>
                @if($admin->role === 'admin')
                    <p style="font-size:12px;color:var(--text-muted);margin-top:10px;">
                        ✅ Admin has full access to all modules.
                    </p>
                @endif
            </div>
        </form>
    </div>
    @endforeach
@endif

@endsection

@section('scripts')
<script>
    function handleRoleChange(select) {
        const form   = select.closest('form');
        const userId = form.action.match(/\/admins\/(\d+)\//)?.[1];
        const panel  = form.querySelector('.permissions-panel');
        const checkboxes = panel.querySelectorAll('input[type="checkbox"]');
        const role   = select.value;

        if (role === 'user') {
            panel.classList.remove('visible');
        } else {
            panel.classList.add('visible');
        }

        if (role === 'admin') {
            checkboxes.forEach(cb => { cb.checked = true; cb.disabled = true; });
        } else {
            checkboxes.forEach(cb => { cb.disabled = false; });
        }
    }
</script>
@endsection
