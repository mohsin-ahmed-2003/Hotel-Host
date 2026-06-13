@foreach($reservations as $res)
    <tr class="clickable-row" onclick="window.location='{{ route('user.reservations.itinerary', $res->id) }}'">
        <td>
            <div class="room-info">
                @if($res->room && $res->room->photos->first())
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($res->room->photos->first()->photo_path) }}" class="room-thumb" alt="Room">
                @else
                    <div class="room-thumb" style="background: var(--border); display:flex; align-items:center; justify-content:center;"><i class="fas fa-home" style="color: var(--body-muted)"></i></div>
                @endif
                <div>
                    <div class="room-name">{{ $res->room->title ?? 'Deleted Room' }}</div>
                    <div class="room-meta">
                        Guest: <strong style="color: var(--body-text);">{{ $res->user->name ?? 'Unknown' }}</strong> • 
                        {{ $res->checkin->format('M d') }} - {{ $res->checkout->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </td>
        <td style="text-align: right;">
            <strong style="font-size: 18px; color: var(--body-text);">${{ number_format($res->total_amount, 2) }}</strong>
            <div>
                <span style="font-size: 11px; color: var(--body-muted); text-transform: uppercase;">{{ $res->payment_type }}</span>
            </div>
            <span class="status-pill status-{{ strtolower($res->status) }}">{{ $res->status }}</span>
        </td>
    </tr>
@endforeach
