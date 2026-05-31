@extends('admin.layout')

@section('title', 'Manage Rooms')

@section('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    /* Custom button styles to match layout but ensure they look like buttons */
    .btn-custom {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        text-decoration: none;
        color: #fff;
    }

    .btn-info-solid { background: #3b82f6; }
    .btn-info-solid:hover { background: #2563eb; transform: translateY(-1px); }
    
    .btn-primary-solid { background: #6366f1; }
    .btn-primary-solid:hover { background: #4f46e5; transform: translateY(-1px); }

    .btn-danger-solid { background: #ef4444; }
    .btn-danger-solid:hover { background: #dc2626; }

    /* Search Bar Styles */
    .search-container {
        display: flex;
        justify-content: flex-end;
        width: 100%;
    }

    .search-form {
        display: flex;
        align-items: center;
        gap: 0; /* Use 0 to join input and button */
        background: var(--bg);
        border-radius: 10px;
        overflow: hidden;
        border: 1.5px solid var(--border);
    }

    .search-input-custom {
        border: none;
        background: transparent;
        padding: 10px 15px;
        color: var(--text);
        width: 250px;
        outline: none;
    }

    .btn-search {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 10px 20px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-search:hover { background: var(--primary-dark); }

    /* Table Badges & Styling */
    .bg-success-soft { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
    .bg-primary-soft { background-color: rgba(99, 102, 241, 0.1); color: #6366f1; }
    .bg-warning-soft { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }

    .action-btns {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .btn-table {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 6px;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 style="font-size: 24px; font-weight: 700; margin: 0;">Manage Rooms</h1>
    <div class="header-actions">
        <a href="{{ route('admin.rooms.settings') }}" class="btn-custom btn-info-solid">
            <i class="fas fa-cog"></i> Rooms Settings
        </a>
        <a href="{{ route('admin.rooms.create') }}" class="btn-custom btn-primary-solid">
            <i class="fas fa-plus"></i> Add Room
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">All Properties</div>
        <div class="search-container">
            <form action="{{ route('admin.rooms.index') }}" method="GET" class="search-form">
                <input type="text" name="search" class="search-input-custom" placeholder="Search ID, Title, Name..." value="{{ request('search') }}">
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="padding-left: 24px;">S.No</th>
                    <th>Room ID</th>
                    <th>Room Name</th>
                    <th>Host</th>
                    <th>Price</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th style="text-align: right; padding-right: 24px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rooms as $index => $room)
                <tr>
                    <td style="padding-left: 24px;">{{ ($rooms->currentPage() - 1) * $rooms->perPage() + $index + 1 }}</td>
                    <td><span class="badge" style="background: var(--bg-2);">#{{ $room->id }}</span></td>
                    <td>
                        <div style="font-weight: 600; color: var(--text);">{{ $room->display_name }}</div>
                        <div style="font-size: 12px; color: var(--text-muted);">{{ Str::limit($room->description, 50) }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $room->user->name ?? 'Deleted User' }}</div>
                        <div style="font-size: 12px; color: var(--text-muted);">{{ $room->user->email ?? '' }}</div>
                    </td>
                    <td style="font-weight: 700; color: var(--primary);">
                        {{ $room->currency_symbol }}{{ number_format($room->price, 2) }}
                    </td>
                    <td style="font-size: 13px;">{{ $room->created_at->format('M d, Y') }}</td>
                    <td>
                        @if($room->status == 'approved')
                            <span class="badge bg-success-soft">Approved</span>
                        @elseif($room->status == 'resubmit')
                            <span class="badge bg-warning-soft" title="{{ $room->resubmit_reason_text }}">Resubmit</span>
                        @else
                            <span class="badge bg-primary-soft">Pending</span>
                        @endif
                    </td>
                    <td style="padding-right: 24px;">
                        <div class="action-btns">
                            <a href="{{ route('admin.rooms.edit', $room->id) }}" class="btn-custom btn-primary-solid btn-table">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </a>
                            <button type="button" class="btn-custom btn-danger-solid btn-table"
                                onclick="openDeleteModal({{ $room->id }}, '{{ addslashes($room->display_name) }}', '{{ route('admin.rooms.destroy', $room->id) }}')">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 60px;">
                        <div style="opacity: 0.3; margin-bottom: 15px;"><i class="fas fa-search fa-3x"></i></div>
                        <div style="color: var(--text-muted);">No properties found.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 20px;">
    {{ $rooms->links() }}
</div>
@endsection

@section('scripts')
<script>
    function openDeleteModal(id, name, url) {
        document.getElementById('deleteUserName').textContent = name;
        document.getElementById('deleteForm').action = url;
        document.getElementById('deleteModal').classList.add('open');
    }
</script>
