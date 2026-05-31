@extends('admin.layout')
@section('title', 'Reservation Details #' . $reservation->id)

@section('content')
    <div class="topbar">
        <div class="topbar-left">
            <h2>Reservation #{{ $reservation->id }}</h2>
            <p>Detailed view of booking information</p>
        </div>
        <div class="topbar-right">
            <a href="{{ route('admin.reservations.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="page-content" style='padding: 2px;'>
        <div class="form-grid">

            <!-- Booking Info Card -->
            <div class="card animate__animated animate__fadeInUp">
                <div class="card-header">
                    <div class="card-title">Booking Details</div>
                    <div style="display:flex; gap:8px;">
                        <!-- Payment Status -->
                        @if($reservation->status === 'success' || $reservation->status === 'completed')
                            <span class="badge badge-user" style="background:#dcfce7; color:#166534;"><i class="fas fa-check-circle" style="margin-right:4px;"></i> Payment: Success</span>
                        @elseif($reservation->status === 'pending')
                            <span class="badge badge-sub_admin" style="background:#fef08a; color:#854d0e;"><i class="fas fa-clock" style="margin-right:4px;"></i> Payment: Pending</span>
                        @else
                            <span class="badge badge-danger" style="background:#fee2e2; color:#b91c1c;"><i class="fas fa-times-circle" style="margin-right:4px;"></i> Payment: {{ ucfirst($reservation->status) }}</span>
                        @endif

                        <!-- Reservation Status -->
                        @if($reservation->reservation_status === 'accepted')
                            <span class="badge badge-user" style="background:#dbeafe; color:#1e40af;"><i class="fas fa-calendar-check" style="margin-right:4px;"></i> Booking: Accepted</span>
                        @elseif($reservation->reservation_status === 'cancelled')
                            <span class="badge badge-danger" style="background:#fee2e2; color:#b91c1c;"><i class="fas fa-calendar-times" style="margin-right:4px;"></i> Booking: Cancelled</span>
                        @else
                            <span class="badge badge-sub_admin" style="background:#ffedd5; color:#c2410c;"><i class="fas fa-hourglass-half" style="margin-right:4px;"></i> Booking: Requested</span>
                        @endif
                    </div>
                </div>
                <div class="card-body" style="display:flex; flex-direction:column; gap:20px;">

                    <div
                        style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border); padding-bottom:12px;">
                        <div>
                            <span
                                style="font-size:12px; color:var(--text-muted); display:block; margin-bottom:4px;">Room</span>
                            <strong
                                style="font-size:15px;">{{ $reservation->room->title ?? $reservation->room->name ?? 'Unknown Room' }}</strong>
                        </div>
                        <div style="text-align:right;">
                            <span
                                style="font-size:12px; color:var(--text-muted); display:block; margin-bottom:4px;">Guests</span>
                            <strong style="font-size:15px;">{{ $reservation->guests }} Person(s)</strong>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div style="background:var(--bg-2); padding:16px; border-radius:12px;">
                            <span
                                style="font-size:11px; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Check-in</span>
                            <div style="font-size:16px; font-weight:600; margin-top:4px; color:var(--text);">
                                {{ \Carbon\Carbon::parse($reservation->checkin)->format('F d, Y') }}
                            </div>
                        </div>
                        <div style="background:var(--bg-2); padding:16px; border-radius:12px;">
                            <span
                                style="font-size:11px; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Check-out</span>
                            <div style="font-size:16px; font-weight:600; margin-top:4px; color:var(--text);">
                                {{ \Carbon\Carbon::parse($reservation->checkout)->format('F d, Y') }}
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:10px; display:flex; flex-direction:column; gap:20px;">
                        <!-- Guest Info -->
                        <div>
                            <div
                                style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                <span style="font-size:13px; font-weight:600; color:var(--text);">Guest Information</span>
                                @if($reservation->user && $reservation->user->email)
                                    <button type="button" class="btn btn-outline btn-sm"
                                        onclick="openContactModal('guest', '{{ $reservation->user->email }}', '{{ $reservation->status }}')">
                                        <i class="fas fa-envelope"></i> Contact Guest
                                    </button>
                                @endif
                            </div>
                            <div class="user-cell"
                                style="background:var(--bg); padding:16px; border-radius:12px; border:1px solid var(--border); align-items:flex-start;">
                                @if($reservation->user && $reservation->user->profile_image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($reservation->user->profile_image) }}"
                                        alt="Avatar" class="user-avatar" style="width:42px; height:42px; object-fit:cover;"
                                        onerror="this.onerror=null; this.outerHTML='<div class=\'user-avatar\' style=\'width:42px; height:42px; font-size:16px;\'>{{ strtoupper(substr($reservation->user->name ?? 'G', 0, 1)) }}</div>';">
                                @else
                                    <div class="user-avatar" style="width:42px; height:42px; font-size:16px;">
                                        {{ strtoupper(substr($reservation->user->name ?? 'G', 0, 1)) }}
                                    </div>
                                @endif
                                <div style="min-width:0;">
                                    <div class="user-cell-name"
                                        style="font-size:15px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $reservation->user->name ?? 'Guest User' }}
                                    </div>
                                    <div class="user-cell-email"
                                        style="font-size:13px; margin-bottom:4px; word-break:break-all;">
                                        {{ $reservation->user->email ?? 'N/A' }}
                                    </div>
                                    @if($reservation->user && $reservation->user->phone)
                                        <div style="font-size:12px; color:var(--text-muted);"><i class="fas fa-phone-alt"></i>
                                            {{ $reservation->user->phone }}</div>
                                    @endif
                                    @if($reservation->user && $reservation->user->country)
                                        <div style="font-size:12px; color:var(--text-muted);"><i
                                                class="fas fa-map-marker-alt"></i> {{ $reservation->user->country }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Host Info -->
                        <div>
                            <div
                                style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                <span style="font-size:13px; font-weight:600; color:var(--text);">Host Information</span>
                                @if($reservation->room && $reservation->room->user && $reservation->room->user->email)
                                    <button type="button" class="btn btn-outline btn-sm"
                                        onclick="openContactModal('host', '{{ $reservation->room->user->email }}', '{{ $reservation->status }}')">
                                        <i class="fas fa-envelope"></i> Contact Host
                                    </button>
                                @endif
                            </div>
                            <div class="user-cell"
                                style="background:var(--bg); padding:16px; border-radius:12px; border:1px solid var(--border); align-items:flex-start;">
                                @if($reservation->room && $reservation->room->user && $reservation->room->user->profile_image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($reservation->room->user->profile_image) }}"
                                        alt="Avatar" class="user-avatar" style="width:42px; height:42px; object-fit:cover;"
                                        onerror="this.onerror=null; this.outerHTML='<div class=\'user-avatar\' style=\'width:42px; height:42px; font-size:16px; background:#ede9fe; color:#7c3aed;\'>{{ strtoupper(substr($reservation->room->user->name ?? 'H', 0, 1)) }}</div>';">
                                @else
                                    <div class="user-avatar"
                                        style="width:42px; height:42px; font-size:16px; background:#ede9fe; color:#7c3aed;">
                                        {{ strtoupper(substr($reservation->room->user->name ?? 'H', 0, 1)) }}
                                    </div>
                                @endif
                                <div style="min-width:0;">
                                    <div class="user-cell-name"
                                        style="font-size:15px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $reservation->room->user->name ?? 'Unknown Host' }}
                                    </div>
                                    <div class="user-cell-email"
                                        style="font-size:13px; margin-bottom:4px; word-break:break-all;">
                                        {{ $reservation->room->user->email ?? 'N/A' }}
                                    </div>
                                    @if($reservation->room && $reservation->room->user && $reservation->room->user->phone)
                                        <div style="font-size:12px; color:var(--text-muted);"><i class="fas fa-phone-alt"></i>
                                            {{ $reservation->room->user->phone }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Financial Breakdown Card -->
            <div class="card animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div class="card-header">
                    <div class="card-title">Payment & Financials</div>
                </div>
                <div class="card-body">

                    <div style="margin-bottom:24px;">
                        <div
                            style="display:flex; justify-content:space-between; margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid var(--border);">
                            <span style="color:var(--text-muted); font-size:14px;">Payment Method</span>
                            <span
                                style="font-weight:600; color:var(--text);">{{ $reservation->payment_type ?? 'N/A' }}</span>
                        </div>
                        <div
                            style="display:flex; justify-content:space-between; margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid var(--border);">
                            <span style="color:var(--text-muted); font-size:14px;">Transaction ID</span>
                            <span
                                style="font-family:monospace; color:var(--text); font-size:13px; background:var(--bg-2); padding:2px 6px; border-radius:4px;">
                                {{ $reservation->transaction_id ?? 'Pending/None' }}
                            </span>
                        </div>
                    </div>

                    <div style="background:var(--bg-2); border-radius:12px; padding:20px;">
                        <span
                            style="font-size:11px; text-transform:uppercase; color:var(--text-muted); font-weight:700; margin-bottom:12px; display:block;">Cost
                            Breakdown</span>

                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span style="color:var(--text); font-size:14px;">Base Amount</span>
                            <span
                                style="font-weight:500;">{{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($reservation->base_amount, 2) }}</span>
                        </div>

                        @if($reservation->food_amount > 0)
                            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                <span style="color:var(--text); font-size:14px;">Enhancements / Food</span>
                                <span
                                    style="font-weight:500;">{{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($reservation->food_amount, 2) }}</span>
                            </div>
                        @endif

                        @if($reservation->service_fee > 0)
                            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                <span style="color:var(--text); font-size:14px;">Service Fee</span>
                                <span
                                    style="font-weight:500;">{{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($reservation->service_fee, 2) }}</span>
                            </div>
                        @endif

                        @if($reservation->tax > 0)
                            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                <span style="color:var(--text); font-size:14px;">Tax</span>
                                <span
                                    style="font-weight:500;">{{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($reservation->tax, 2) }}</span>
                            </div>
                        @endif

                        <div
                            style="display:flex; justify-content:space-between; margin-top:16px; padding-top:16px; border-top:1px dashed var(--border);">
                            <span style="color:var(--text); font-size:16px; font-weight:700;">Total Paid</span>
                            <span style="font-weight:800; font-size:18px; color:var(--primary);">
                                {{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($reservation->total_amount, 2) }}
                            </span>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-top:8px;">
                            <span style="color:var(--text-muted); font-size:13px;">Cancellation Policy</span>
                            <span style="font-weight:600; font-size:13px; color:var(--text);">
                                {{ ucfirst($reservation->room->cancellation_policy ?? 'Flexible') }}
                            </span>
                        </div>
                    </div>

                    @if(!empty($reservation->enhancements_data))
                        <div style="margin-top:24px;">
                            <span
                                style="font-size:13px; font-weight:600; color:var(--text); margin-bottom:10px; display:block;">Selected
                                Enhancements</span>
                            <div style="display:flex; flex-direction:column; gap:8px;">
                                @foreach($reservation->enhancements_data as $enhancement)
                                    <div
                                        style="display:flex; justify-content:space-between; background:var(--bg); padding:10px 14px; border-radius:8px; border:1px solid var(--border);">
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <i class="fas fa-concierge-bell" style="color:var(--text-muted); font-size:12px;"></i>
                                            <span
                                                style="font-size:13px; color:var(--text);">{{ $enhancement['name'] ?? 'Add-on' }}</span>
                                        </div>
                                        <span style="font-size:13px; font-weight:600;">
                                            {{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($enhancement['total'] ?? 0, 2) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    <!-- Contact Modal -->
    <div id="contactModal"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; padding:20px;">
        <div
            style="background:var(--card); width:100%; max-width:500px; border-radius:16px; overflow:hidden; box-shadow:0 10px 40px rgba(0,0,0,0.2);">
            <div
                style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                <h3 style="font-size:16px; font-weight:700; margin:0;" id="modalTitle">Contact</h3>
                <button type="button" onclick="closeContactModal()"
                    style="background:none; border:none; cursor:pointer; font-size:18px; color:var(--text-muted);"><i
                        class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.reservations.send-email', $reservation->id) }}" method="POST">
                @csrf
                <div style="padding:24px; display:flex; flex-direction:column; gap:16px;">
                    <input type="hidden" name="recipient_type" id="recipientType">

                    <div>
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:var(--text-muted);">To
                            Email</label>
                        <input type="email" name="to_email" id="toEmail" class="form-control" readonly
                            style="background:var(--bg-2);">
                    </div>

                    <div>
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:var(--text-muted);">Subject</label>
                        <input type="text" name="subject" id="emailSubject" class="form-control" required>
                    </div>

                    <div>
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:var(--text-muted);">Message
                            Body</label>
                        <textarea name="message_body" id="emailBody" class="form-control" rows="6" required
                            style="resize:vertical;"></textarea>
                    </div>
                </div>
                <div
                    style="padding:16px 24px; border-top:1px solid var(--border); background:var(--bg); display:flex; justify-content:flex-end; gap:12px;">
                    <button type="button" class="btn btn-outline" onclick="closeContactModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send Email</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function openContactModal(type, email, status) {
            document.getElementById('contactModal').style.display = 'flex';
            document.getElementById('recipientType').value = type;
            document.getElementById('toEmail').value = email;
            document.getElementById('modalTitle').innerText = type === 'guest' ? 'Contact Guest' : 'Contact Host';

            let subject = '';
            let body = '';

            const roomName = "{{ addslashes($reservation->room->title ?? $reservation->room->name ?? 'the room') }}";

            if (type === 'guest') {
                if (status === 'success' || status === 'completed') {
                    subject = 'Your Reservation is Confirmed!';
                    body = `Hello,\n\nWe are pleased to inform you that your reservation for ${roomName} has been confirmed successfully.\n\nThank you for choosing us!`;
                } else if (status === 'failed') {
                    subject = 'Issue with your Reservation Request';
                    body = `Hello,\n\nWe received your reservation request for ${roomName}, but unfortunately, the payment failed. Can you please try again to secure your booking?\n\nLet us know if you need assistance.`;
                } else {
                    subject = 'Update on your Reservation';
                    body = `Hello,\n\nYour reservation for ${roomName} is currently pending. We will notify you once it is confirmed.\n\nThank you for your patience.`;
                }
            } else {
                // Host email
                if (status === 'success' || status === 'completed') {
                    subject = 'New Confirmed Booking!';
                    body = `Hello,\n\nYou have a new confirmed booking for ${roomName}. The guest has successfully completed the payment.\n\nPlease check your dashboard for details.`;
                } else {
                    subject = 'Pending Booking Notification';
                    body = `Hello,\n\nA guest has initiated a booking for ${roomName}, but the payment is not yet complete.\n\nWe will let you know when it is confirmed.`;
                }
            }

            document.getElementById('emailSubject').value = subject;
            document.getElementById('emailBody').value = body;
        }

        function closeContactModal() {
            document.getElementById('contactModal').style.display = 'none';
        }
    </script>
@endsection