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

        .btn-info-solid {
            background: #3b82f6;
        }

        .btn-info-solid:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-primary-solid {
            background: #6366f1;
        }

        .btn-primary-solid:hover {
            background: #4f46e5;
            transform: translateY(-1px);
        }

        .btn-danger-solid {
            background: #ef4444;
        }

        .btn-danger-solid:hover {
            background: #dc2626;
        }

        /* Search Bar Styles */
        .search-container {
            display: flex;
            justify-content: flex-end;
            width: 85%;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 0;
            /* Use 0 to join input and button */
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

        .btn-search:hover {
            background: var(--primary-dark);
        }

        /* Table Badges & Styling */
        .bg-success-soft {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .bg-primary-soft {
            background-color: rgba(99, 102, 241, 0.1);
            color: #6366f1;
        }

        .bg-warning-soft {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

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

        /* Modern Status Dropdown */
        .custom-status-dropdown {
            position: relative;
            display: inline-block;
        }

        .status-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            border: 1.5px solid transparent;
            transition: all 0.2s;
            min-width: 115px;
            user-select: none;
        }

        .status-trigger svg {
            width: 14px;
            height: 14px;
            transition: transform 0.2s;
        }

        .custom-status-dropdown.open .status-trigger svg {
            transform: rotate(180deg);
        }

        .status-options {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            width: 100%;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 50;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-5px);
            transition: all 0.2s;
            overflow: hidden;
        }

        .custom-status-dropdown.open .status-options {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .status-option {
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            color: var(--text);
        }

        .status-option:hover {
            background: var(--bg-2);
        }

        .status-approved {
            background-color: rgba(16, 185, 129, 0.1) !important;
            color: #10b981 !important;
            border-color: rgba(16, 185, 129, 0.2) !important;
        }

        .status-pending {
            background-color: rgba(99, 102, 241, 0.1) !important;
            color: #6366f1 !important;
            border-color: rgba(99, 102, 241, 0.2) !important;
        }

        .status-resubmit {
            background-color: rgba(245, 158, 11, 0.1) !important;
            color: #f59e0b !important;
            border-color: rgba(245, 158, 11, 0.2) !important;
        }

        .status-badge-wrapper {
            display: inline-flex;
            align-items: center;
            gap: 8px;
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

    <div id="adminToastContainer"
        style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;">
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">All Properties</div>
            <div class="search-container">
                <form action="{{ route('admin.rooms.index') }}" method="GET" class="search-form">
                    <input type="text" name="search" class="search-input-custom" placeholder="Search ID, Title, Name..."
                        value="{{ request('search') }}">
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
                        <th style="min-width: 130px;">Room Metrics</th>
                        <th>Created At</th>
                        <th>Steps Completed</th>
                        <th>Status</th>
                        <th style="text-align: right; padding-right: 24px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $index => $room)
                        <tr>
                            <td style="padding-left: 24px;">{{ ($rooms->currentPage() - 1) * $rooms->perPage() + $index + 1 }}
                            </td>
                            <td><span class="badge" style="background: var(--bg-2);">#{{ $room->id }}</span></td>
                            <td>
                                <div style="font-weight: 600; color: var(--text);">{{ $room->display_name }}</div>
                                <div style="font-size: 12px; color: var(--text-muted);">{{ Str::limit($room->description, 50) }}
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $room->user->name ?? 'Deleted User' }}</div>
                                <div style="font-size: 12px; color: var(--text-muted);">{{ $room->user->email ?? '' }}</div>
                            </td>
                            <td style="font-weight: 700; color: var(--primary);">
                                {{ $room->currency_symbol }}{{ number_format($room->price, 2) }}
                            </td>
                            <td>
                                <div style="font-size:13px; color:var(--text);">
                                    <i class="fas fa-eye" style="color:#64748b; width:16px;"></i> {{ $room->view_count ?? 0 }}
                                    Views<br>
                                    <i class="fas fa-check-circle" style="color:#22c55e; width:16px; margin-top:4px;"></i>
                                    {{ $room->book_count ?? 0 }} Bookings
                                </div>
                            </td>
                            <td style="font-size: 13px;">{{ $room->created_at->format('M d, Y') }}</td>
                            <td>
                                @php
                                    $missing = $room->countMissingSteps();
                                    $completed = 6 - $missing;
                                    $status = empty($room->status) ? 'pending' : $room->status;
                                    $statusClass = 'status-' . strtolower($status);
                                @endphp
                                <span class="badge" style="background: var(--bg-2); color: var(--text);">
                                    {{ $completed }} / 6
                                </span>
                            </td>
                            <td>
                                <div class="custom-status-dropdown" id="status-dropdown-{{ $room->id }}"
                                    data-original="{{ $status }}">
                                    <div class="status-trigger {{ $statusClass }}"
                                        onclick="toggleStatusDropdown({{ $room->id }})">
                                        <span class="status-text">{{ ucfirst($status) }}</span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="6 9 12 15 18 9" />
                                        </svg>
                                    </div>
                                    <div class="status-options">
                                        <div class="status-option"
                                            onclick="updateRoomStatus({{ $room->id }}, 'pending', {{ $completed }})">Pending
                                        </div>
                                        <div class="status-option"
                                            onclick="updateRoomStatus({{ $room->id }}, 'approved', {{ $completed }})">Approved
                                        </div>
                                        <div class="status-option"
                                            onclick="updateRoomStatus({{ $room->id }}, 'resubmit', {{ $completed }})">Resubmit
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-right: 24px;">
                                <div class="action-btns">
                                    <a href="{{ route('admin.rooms.edit', $room->id) }}"
                                        class="btn-custom btn-primary-solid btn-table">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"
                                            viewBox="0 0 24 24">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <button type="button" class="btn-custom btn-danger-solid btn-table"
                                        onclick="openDeleteModal({{ $room->id }}, '{{ addslashes($room->display_name) }}', '{{ route('admin.rooms.destroy', $room->id) }}')">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"
                                            viewBox="0 0 24 24">
                                            <polyline points="3 6 5 6 21 6" />
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                            <path d="M10 11v6M14 11v6" />
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                                        </svg>
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

    function showToast(type, message) {
        // Handle backwards compatibility where first argument might be message and second is type
        if (type !== 'success' && type !== 'error' && type !== 'info' && type !== 'warning') {
            const temp = type;
            type = message || 'success';
            message = temp;
        }

        const container = document.getElementById('adminToastContainer');
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : 'success'} shadow-lg border-0 animate__animated animate__fadeInRight`;
        toast.style.borderRadius = '12px';
        toast.style.display = 'flex';
        toast.style.alignItems = 'center';
        toast.style.gap = '10px';
        toast.style.padding = '14px 20px';
        toast.style.margin = '0';
        toast.style.background = 'var(--card)';
        toast.style.borderLeft = `4px solid ${type === 'error' ? 'var(--danger)' : 'var(--success)'}`;
        toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';

        const iconColor = type === 'error' ? 'var(--danger)' : 'var(--success)';
        const icon = type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
        toast.innerHTML = `<i class="${icon}" style="color: ${iconColor}; font-size: 18px;"></i> <span style="color: var(--text); font-weight: 600; font-size: 14px;">${message}</span>`;

        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.replace('animate__fadeInRight', 'animate__fadeOutRight');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }

    // Dropdown Toggling logic
    function toggleStatusDropdown(id) {
        // Close all others
        document.querySelectorAll('.custom-status-dropdown.open').forEach(el => {
            if (el.id !== 'status-dropdown-' + id) el.classList.remove('open');
        });
        document.getElementById('status-dropdown-' + id).classList.toggle('open');
    }

    // Close on outside click
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.custom-status-dropdown')) {
            document.querySelectorAll('.custom-status-dropdown').forEach(el => el.classList.remove('open'));
        }
    });

    async function updateRoomStatus(roomId, status, completedSteps) {
        const dropdown = document.getElementById('status-dropdown-' + roomId);
        const trigger = dropdown.querySelector('.status-trigger');
        const textSpan = trigger.querySelector('.status-text');
        const originalStatus = dropdown.getAttribute('data-original');

        // Close dropdown
        dropdown.classList.remove('open');

        // Client-side validation
        if (status === 'approved' && completedSteps < 6) {
            showToast('error', 'Cannot approve. The host has not completed all 6 steps.');
            return;
        }

        // Optimistically update UI
        updateTriggerUI(trigger, textSpan, status);

        try {
            const response = await fetch(`/admin/rooms/${roomId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            });

            const data = await response.json();

            if (data.success) {
                showToast('success', data.message);
                dropdown.setAttribute('data-original', status);
            } else {
                throw new Error(data.message || 'Failed to update status');
            }
        } catch (error) {
            console.error('Error updating status:', error);
            showToast('error', error.message || 'An error occurred while updating the status.');
            // Revert UI on failure
            updateTriggerUI(trigger, textSpan, originalStatus);
        }
    }

    function updateTriggerUI(trigger, textSpan, status) {
        textSpan.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        trigger.classList.remove('status-approved', 'status-pending', 'status-resubmit');
        trigger.classList.add('status-' + status.toLowerCase());
    }
</script>