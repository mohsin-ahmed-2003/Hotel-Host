<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') | {{ $siteSettings->get('site_name', 'AdminPanel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if($siteSettings->get('site_favicon'))
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($siteSettings->get('site_favicon')) }}"
            type="image/x-icon">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        /* ── Theme variables ── */
        :root {
            /* Light Mode (Default) */
            --bg: #f8fafc;
            --bg-2: #f1f5f9;
            --card: #ffffff;
            --card-2: #f8fafc;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-active: #6366f1;
            --sidebar-hover: #1e293b;
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: rgba(99, 102, 241, 0.1);
            --sidebar-width: 260px;
            --sidebar-collapsed: 72px;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
        }

        body.dark-mode {
            /* Dark Mode */
            --bg: #0f172a;
            --bg-2: #1e293b;
            --card: #1e293b;
            --card-2: #263348;
            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --border: #334155;
            --sidebar-bg: #0a0f1e;
            --sidebar-hover: #1e293b;
            --primary-light: rgba(99, 102, 241, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            /* background: var(--sidebar-bg); */
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            transition: width 0.3s ease;
            z-index: 100;
            overflow: hidden;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            border-bottom: 1px solid #1e293b;
            min-height: 70px;
            overflow: hidden;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            flex: 1;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            line-height: 1;
        }

        .brand-name {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            max-width: 140px;
            transition: max-width 0.3s ease, opacity 0.3s ease;
            opacity: 1;
        }

        .sidebar.collapsed .brand-name {
            max-width: 0;
            opacity: 0;
        }

        .toggle-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--sidebar-text);
            padding: 6px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, transform 0.3s;
            flex-shrink: 0;
        }

        .toggle-btn:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar.collapsed .toggle-btn {
            transform: rotate(180deg);
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 0;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #334155;
            padding: 8px 20px 4px;
            white-space: nowrap;
            transition: opacity 0.2s;
        }

        .sidebar.collapsed .nav-section-label {
            opacity: 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: #e8edf8;
            /* color: var(--sidebar-text); */
            text-decoration: none;
            border-radius: 0;
            transition: all 0.2s;
            white-space: nowrap;
            position: relative;
        }

        .nav-item:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .nav-item.active {
            background: #337fe4a5;
            /* background: var(--primary); */
            color: #fff;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #a5b4fc;
            border-radius: 0 2px 2px 0;
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-label {
            font-size: 14px;
            font-weight: 500;
            transition: opacity 0.2s, width 0.3s;
            overflow: hidden;
        }

        .sidebar.collapsed .nav-label {
            opacity: 0;
            width: 0;
        }

        /* Tooltip on collapsed */
        .sidebar.collapsed .nav-item::after {
            content: attr(data-tooltip);
            position: absolute;
            left: calc(var(--sidebar-collapsed) + 8px);
            background: #1e293b;
            color: #fff;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
            z-index: 200;
        }

        .sidebar.collapsed .nav-item:hover::after {
            opacity: 1;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid #1e293b;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            overflow: hidden;
        }

        .admin-avatar {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .admin-info {
            overflow: hidden;
        }

        .admin-name {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
        }

        .admin-role {
            font-size: 11px;
            color: var(--sidebar-text);
            white-space: nowrap;
        }

        .sidebar.collapsed .admin-info {
            display: none;
        }

        /* ── Main ── */
        .main {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
            min-width: 0;
        }

        .main.expanded {
            margin-left: var(--sidebar-collapsed);
        }

        .topbar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-left h2 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
        }

        .topbar-left p {
            font-size: 13px;
            color: var(--text-muted);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--card);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            transition: all 0.2s;
            text-decoration: none;
        }

        .topbar-btn:hover {
            background: var(--bg-2);
            color: var(--text);
        }

        /* Profile dropdown */
        .profile-dropdown-wrap {
            position: relative;
        }

        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 4px 10px 4px 4px;
            border-radius: 50px;
            border: 1.5px solid var(--border);
            background: var(--card);
            transition: all 0.2s;
        }

        .profile-trigger:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .profile-trigger img {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .profile-trigger-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            max-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-trigger-chevron {
            color: var(--text-muted);
            transition: transform 0.2s;
            flex-shrink: 0;
        }

        .profile-dropdown-wrap.open .profile-trigger-chevron {
            transform: rotate(180deg);
        }

        .profile-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            min-width: 200px;
            overflow: hidden;
            display: none;
            z-index: 200;
            animation: dropIn 0.2s ease;
        }

        @keyframes dropIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-dropdown-wrap.open .profile-dropdown {
            display: block;
        }

        .dropdown-header {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            background: var(--bg);
        }

        .dropdown-header-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
        }

        .dropdown-header-role {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            font-size: 14px;
            color: var(--text);
            text-decoration: none;
            transition: background 0.15s;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-family: inherit;
        }

        .dropdown-item:hover {
            background: var(--bg-2);
        }

        .dropdown-item.danger {
            color: #f87171;
        }

        .dropdown-item.danger:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 0;
        }

        .page-content {
            padding: 28px;
            flex: 1;
        }

        /* ── Cards ── */
        .card {
            background: var(--card);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .card-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--text);
        }

        .card-body {
            padding: 24px;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-warning {
            background: var(--warning);
            color: #fff;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-success {
            background: var(--success);
            color: #fff;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .btn-outline:hover {
            background: var(--bg-2);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 8px;
        }

        /* ── Table ── */
        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: var(--bg);
        }

        th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        td {
            padding: 14px 16px;
            font-size: 14px;
            border-top: 1px solid var(--border);
            vertical-align: middle;
            color: var(--text);
        }

        tr:hover td {
            background: var(--bg-2);
        }

        /* ── Badges ── */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            color: var(--primary);
            background: var(--primary-light);
        }

        .badge-admin {
            background: #ede9fe;
            color: #7c3aed;
        }

        .badge-sub_admin {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-user {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-male {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-female {
            background: #fce7f3;
            color: #be185d;
        }

        .badge-other {
            background: #f3f4f6;
            color: #374151;
        }

        /* ── Avatar ── */
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-cell-name {
            font-weight: 600;
            font-size: 14px;
        }

        .user-cell-email {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ── Forms ── */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            color: var(--text);
            background: var(--bg);
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-control.is-invalid {
            border-color: var(--danger);
        }

        .invalid-feedback {
            font-size: 12px;
            color: var(--danger);
            margin-top: 4px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* ── Alerts ── */
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: #065f46;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.12);
            color: #991b1b;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }

        body:not(.light-mode) .alert-success {
            color: #6ee7b7;
        }

        body:not(.light-mode) .alert-danger {
            color: #fca5a5;
        }

        /* ── Search ── */
        .search-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-box svg {
            position: absolute;
            left: 12px;
            color: var(--text-muted);
        }

        .search-input {
            padding: 9px 14px 9px 38px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            width: 240px;
            transition: border-color 0.2s;
            font-family: inherit;
            background: var(--bg);
            color: var(--text);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
        }

        /* ── Modal ── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 28px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
            animation: modalIn 0.25s ease;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text);
        }

        .modal-desc {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        /* ── Permissions ── */
        .permissions-box {
            background: var(--bg);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            margin-top: 12px;
        }

        .permissions-box label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            font-size: 14px;
            cursor: pointer;
        }

        .permissions-box input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state svg {
            margin-bottom: 16px;
            opacity: 0.4;
        }

        .empty-state h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--text);
        }

        .empty-state p {
            font-size: 14px;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .sidebar {
                width: var(--sidebar-collapsed);
            }

            .sidebar .brand-name,
            .sidebar .nav-label,
            .sidebar .admin-info {
                opacity: 0;
                width: 0;
            }

            .main {
                margin-left: var(--sidebar-collapsed);
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    @yield('styles')
</head>

<body>

    @php $sessionUser = App\Models\User::find(session('admin_id')); @endphp

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                @if($siteSettings->get('site_logo'))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($siteSettings->get('site_logo')) }}"
                        alt="{{ $siteSettings->get('site_name', 'AdminPanel') }}"
                        style="height:32px;width:auto;object-fit:contain;flex-shrink:0;">
                @else
                    <div class="brand-icon">⚡</div>
                @endif
                <span class="brand-name">{{ $siteSettings->get('site_name', 'AdminPanel') }}</span>
            </div>
            <button class="toggle-btn" id="toggleSidebar" title="Toggle sidebar">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 18l-6-6 6-6" />
                </svg>
            </button>
        </div>

        <nav class="sidebar-nav">
            <!-- <div class="nav-section-label">Main Menu</div> -->

            <a href="{{ route('admin.dashboard') }}"
                class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
                <span class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" />
                        <rect x="14" y="3" width="7" height="7" />
                        <rect x="14" y="14" width="7" height="7" />
                        <rect x="3" y="14" width="7" height="7" />
                    </svg>
                </span>
                <span class="nav-label">Dashboard</span>
            </a>

            <a href="{{ route('admin.admins') }}"
                class="nav-item {{ request()->routeIs('admin.admins*') ? 'active' : '' }}" data-tooltip="Manage Admins">
                <span class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                </span>
                <span class="nav-label">Manage Admins</span>
            </a>

            <a href="{{ route('admin.users') }}"
                class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}" data-tooltip="Manage Users">
                <span class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </span>
                <span class="nav-label">Manage Users</span>
            </a>

            <a href="{{ route('admin.rooms.index') }}"
                class="nav-item {{ request()->routeIs('admin.rooms*') ? 'active' : '' }}" data-tooltip="Manage Rooms">
                <span class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                </span>
                <span class="nav-label">Manage Rooms</span>
            </a>

            <a href="{{ route('admin.reservations.index') }}"
                class="nav-item {{ request()->routeIs('admin.reservations*') ? 'active' : '' }}" data-tooltip="Manage Reservations">
                <span class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                </span>
                <span class="nav-label">Manage Reservations</span>
            </a>

            <a href="{{ route('admin.property-types.index') }}"
                class="nav-item {{ request()->routeIs('admin.property-types*') ? 'active' : '' }}"
                data-tooltip="Manage Property Types">
                <span class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                </span>
                <span class="nav-label">Property Types</span>
            </a>

            <a href="{{ route('admin.space-types.index') }}"
                class="nav-item {{ request()->routeIs('admin.space-types*') ? 'active' : '' }}"
                data-tooltip="Manage Space Types">
                <span class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                        <line x1="9" y1="3" x2="9" y2="21" />
                    </svg>
                </span>
                <span class="nav-label">Space Types</span>
            </a>

            <a href="{{ route('admin.room-beds.index') }}"
                class="nav-item {{ request()->routeIs('admin.room-beds*') ? 'active' : '' }}"
                data-tooltip="Manage Bed Arrangements">
                <span class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M2 4v16M22 4v16M2 8h20M2 14h20M6 8v6M18 8v6M6 11h12" />
                    </svg>
                </span>
                <span class="nav-label">Bed Arrangement</span>
            </a>

            <a href="{{ route('admin.room-rules.index') }}"
                class="nav-item {{ request()->routeIs('admin.room-rules*') ? 'active' : '' }}"
                data-tooltip="Manage Room Rules">
                <span class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </span>
                <span class="nav-label">Room Rules</span>
            </a>

            <a href="{{ route('admin.subscription-plans.index') }}"
                class="nav-item {{ request()->routeIs('admin.subscription-plans*') ? 'active' : '' }}"
                data-tooltip="Subscription Plans">
                <span class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                    </svg>
                </span>
                <span class="nav-label">Subscription Plans</span>
            </a>

            <a href="{{ route('admin.user-subscriptions.index') }}"
                class="nav-item {{ request()->routeIs('admin.user-subscriptions*') ? 'active' : '' }}"
                data-tooltip="User Subscriptions">
                <span class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </span>
                <span class="nav-label">User Subscriptions</span>
            </a>

            <a href="{{ route('admin.settings') }}"
                class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"
                data-tooltip="Site Settings">
                <span class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3" />
                        <path
                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                    </svg>
                </span>
                <span class="nav-label">Site Settings</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar">{{ strtoupper(substr($sessionUser->name ?? 'A', 0, 1)) }}</div>
                <div class="admin-info">
                    <div class="admin-name">{{ $sessionUser->name ?? 'Admin' }}</div>
                    <div class="admin-role">{{ ucfirst(str_replace('_', ' ', $sessionUser->role ?? 'admin')) }}</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <div class="main" id="mainContent">
        <header class="topbar" style='padding: 10px 28px;'>
            <div class="topbar-left">
                <h2>@yield('page-title', 'Dashboard')</h2>
                <p>@yield('page-subtitle', 'Welcome to admin panel')</p>
            </div>
            <div class="topbar-right">
                <!-- Theme Toggle -->
                <button class="topbar-btn" id="themeToggle" title="Toggle theme">
                    <svg id="themeIconDark" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                    </svg>
                    <svg id="themeIconLight" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" style="display:none">
                        <circle cx="12" cy="12" r="5" />
                        <line x1="12" y1="1" x2="12" y2="3" />
                        <line x1="12" y1="21" x2="12" y2="23" />
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                        <line x1="1" y1="12" x2="3" y2="12" />
                        <line x1="21" y1="12" x2="23" y2="12" />
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                    </svg>
                </button>

                <a href="{{ route('homepage') }}" class="topbar-btn" title="View Site">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <path
                            d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                    </svg>
                </a>

                <!-- Profile Dropdown -->
                <div class="profile-dropdown-wrap" id="profileDropdownWrap">
                    <div class="profile-trigger" id="profileTrigger">
                        <img src="{{ asset('images/Admin-Profile.png') }}" alt="Profile">
                        <span class="profile-trigger-name">{{ $sessionUser->name ?? 'Admin' }}</span>
                        <svg class="profile-trigger-chevron" width="14" height="14" fill="none" stroke="currentColor"
                            stroke-width="2.5" viewBox="0 0 24 24">
                            <polyline points="6 9 12 15 18 9" />
                        </svg>
                    </div>
                    <div class="profile-dropdown">
                        <div class="dropdown-header">
                            <div class="dropdown-header-name">{{ $sessionUser->name ?? 'Admin' }}</div>
                            <div class="dropdown-header-role">
                                {{ ucfirst(str_replace('_', ' ', $sessionUser->role ?? 'admin')) }}
                            </div>
                        </div>
                        <a href="{{ route('admin.profile') }}" class="dropdown-item">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            My Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('admin.logout') }}" class="dropdown-item danger">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="page-content">
            @if(session('success'))
                <div class="alert alert-success">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal">
            <div class="modal-title">⚠️ Confirm Delete</div>
            <div class="modal-desc">Are you sure you want to delete <strong id="deleteUserName"></strong>? This action
                cannot be undone.</div>
            <div class="modal-actions">
                <button class="btn btn-outline" onclick="closeDeleteModal()">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const THEME_KEY = 'site_theme';
        function applyAdminTheme(theme) {
            document.body.classList.toggle('dark-mode', theme === 'dark');
            document.getElementById('themeIconDark').style.display = theme === 'dark' ? 'none' : 'block';
            document.getElementById('themeIconLight').style.display = theme === 'dark' ? 'block' : 'none';
        }
        applyAdminTheme(localStorage.getItem(THEME_KEY) || 'light');
        document.getElementById('themeToggle').addEventListener('click', () => {
            const next = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
            localStorage.setItem(THEME_KEY, next);
            applyAdminTheme(next);
        });

        const sidebar = document.getElementById('sidebar');
        const main = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebar');
        const STORAGE_KEY = 'admin_sidebar_collapsed';

        if (localStorage.getItem(STORAGE_KEY) === '1') {
            sidebar.classList.add('collapsed');
            main.classList.add('expanded');
        }

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');
            localStorage.setItem(STORAGE_KEY, sidebar.classList.contains('collapsed') ? '1' : '0');
        });

        function confirmDelete(userId, userName, deleteUrl) {
            document.getElementById('deleteUserName').textContent = userName;
            document.getElementById('deleteForm').action = deleteUrl;
            document.getElementById('deleteModal').classList.add('open');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('open');
        }

        document.getElementById('deleteModal').addEventListener('click', function (e) {
            if (e.target === this) closeDeleteModal();
        });

        const profileWrap = document.getElementById('profileDropdownWrap');
        const profileTrigger = document.getElementById('profileTrigger');

        profileTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            profileWrap.classList.toggle('open');
        });

        document.addEventListener('click', () => profileWrap.classList.remove('open'));
        profileWrap.addEventListener('click', (e) => e.stopPropagation());
    </script>
    <script src="{{ asset('js/global.js') }}"></script>
    @yield('scripts')
</body>

</html>