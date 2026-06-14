@extends('admin.layout')

@section('title', 'Manage Reviews')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manage Reviews & Checkouts</h2>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Room</th>
                        <th>Guest</th>
                        <th>Checkout Date</th>
                        <th>Review Status</th>
                        <th>Host Approval</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                    <tr>
                        <td>#{{ $reservation->id }}</td>
                        <td>
                            @if($reservation->room)
                                <strong>{{ $reservation->room->title }}</strong>
                            @else
                                <span class="text-muted">Room Deleted</span>
                            @endif
                        </td>
                        <td>
                            @if($reservation->user)
                                {{ $reservation->user->name }}
                                <br><small class="text-muted">{{ $reservation->user->email }}</small>
                            @else
                                <span class="text-muted">User Deleted</span>
                            @endif
                        </td>
                        <td>
                            {{ $reservation->checkout ? $reservation->checkout->format('d M, Y') : 'N/A' }}
                        </td>
                        <td>
                            @if($reservation->review)
                                <span class="badge bg-success">Given</span>
                            @else
                                <span class="badge bg-secondary">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($reservation->review)
                                @if($reservation->review->host_approved === true)
                                    <span class="badge bg-success">Approved</span>
                                @elseif($reservation->review->host_approved === false)
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <form action="{{ route('admin.reviews.send-email', $reservation->id) }}" method="POST" class="d-inline send-email-form">
                                @csrf
                                @if($reservation->review_email_sent)
                                    <button type="button" class="btn btn-sm btn-success" disabled title="Email Already Sent">
                                        <i class="fas fa-check"></i> Sent
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-primary send-email-btn" {{ $reservation->review ? 'disabled' : '' }} title="Send Review Prompt Email">
                                        <i class="fas fa-envelope"></i> <span class="btn-text">Send Email</span>
                                    </button>
                                @endif
                            </form>
                            
                            <a href="{{ route('admin.reviews.view', $reservation->id) }}" class="btn btn-sm btn-info text-white {{ !$reservation->review ? 'disabled' : '' }}">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No reservations past checkout found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reservations->hasPages())
    <div class="card-footer bg-white pb-0">
        {{ $reservations->links() }}
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.send-email-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const btn = this.querySelector('.send-email-btn');
            if (btn) {
                // Prevent multiple clicks
                if (btn.disabled) {
                    e.preventDefault();
                    return;
                }
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Sending...</span>';
            }
        });
    });
</script>
@endsection
