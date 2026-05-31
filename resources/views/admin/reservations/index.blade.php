@extends('admin.layout')
@section('title', 'Manage Reservations')

@section('content')
    <div class="topbar">
        <div class="topbar-left">
            <h2>Manage Reservations</h2>
            <p>View and track all room bookings</p>
        </div>
    </div>

    <div class="page-content" style='padding: 2px;'>
        <div class="card animate__animated animate__fadeInUp">
            <div class="card-header">
                <div class="card-title">All Reservations</div>
            </div>

            <div class="card-body" style="padding:0;">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Guest</th>
                                <th>Room & Host</th>
                                <th>Room Metrics</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservations as $reservation)
                                <tr>
                                    <td>#{{ $reservation->id }}</td>
                                    <td>
                                        <div class="user-cell" style="align-items: center;">
                                            @if($reservation->user && $reservation->user->profile_image)
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($reservation->user->profile_image) }}"
                                                    alt="Avatar" class="user-avatar" style="object-fit:cover;"
                                                    onerror="this.onerror=null; this.outerHTML='<div class=\'user-avatar\'>{{ strtoupper(substr($reservation->user->name ?? 'G', 0, 1)) }}</div>';">
                                            @else
                                                <div class="user-avatar">
                                                    {{ strtoupper(substr($reservation->user->name ?? 'G', 0, 1)) }}
                                                </div>
                                            @endif
                                            <div style="min-width:0;">
                                                <div class="user-cell-name"
                                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $reservation->user->name ?? 'Guest' }}</div>
                                                <div class="user-cell-email"
                                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $reservation->user->email ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $reservation->room->title ?? $reservation->room->name ?? 'Unknown Room' }}</strong>
                                        <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">
                                            Host: {{ $reservation->room->user->name ?? 'Unknown' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size:13px; color:var(--text);">
                                            <i class="fas fa-eye" style="color:#64748b; width:16px;"></i> {{ $reservation->room->view_count ?? 0 }} Views<br>
                                            <i class="fas fa-check-circle" style="color:#22c55e; width:16px; margin-top:4px;"></i> {{ $reservation->room->book_count ?? 0 }} Bookings
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::parse($reservation->checkin)->format('M d, Y') }}</strong>
                                    </td>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::parse($reservation->checkout)->format('M d, Y') }}</strong>
                                    </td>
                                    <td>
                                        <strong>{{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($reservation->total_amount, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span style="font-size:12px; font-weight:600; color:var(--text);">{{ $reservation->payment_type ?? 'N/A' }}</span>
                                        <div style="margin-top: 4px;">
                                            @if($reservation->status === 'success' || $reservation->status === 'completed')
                                                <span class="badge badge-user">Success</span>
                                            @elseif($reservation->status === 'pending')
                                                <span class="badge badge-sub_admin">Pending</span>
                                            @else
                                                <span class="badge badge-danger" style="background:#fee2e2; color:#b91c1c;">Failed</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($reservation->reservation_status === 'accepted')
                                            <span class="badge badge-user">Accepted</span>
                                        @elseif($reservation->reservation_status === 'cancelled')
                                            <span class="badge badge-danger" style="background:#fee2e2; color:#b91c1c;">Cancelled</span>
                                        @else
                                            <span class="badge badge-sub_admin">Requested</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.reservations.show', $reservation->id) }}"
                                            class="btn btn-outline btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <div class="empty-state">
                                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5"
                                                viewBox="0 0 24 24">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                                <line x1="16" y1="2" x2="16" y2="6" />
                                                <line x1="8" y1="2" x2="8" y2="6" />
                                                <line x1="3" y1="10" x2="21" y2="10" />
                                            </svg>
                                            <h3>No Reservations Found</h3>
                                            <p>There are currently no bookings in the system.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($reservations->hasPages())
                    <div style="padding: 20px; border-top: 1px solid var(--border);">
                        {{ $reservations->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection