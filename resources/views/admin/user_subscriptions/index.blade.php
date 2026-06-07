@extends('admin.layout')

@section('title', 'Manage User Subscriptions')

@section('page-title', 'User Subscriptions')

@section('content')
<div class="card">
        <div class="card-header">
            <span class="card-title">User Subscriptions</span>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Plan Name</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Payment Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $sub)
                        <tr>
                            <td>
                                <strong>{{ $sub->user->name ?? 'Deleted User' }}</strong>
                                <br><small style="color: #64748b;">{{ $sub->user->email ?? '' }}</small>
                            </td>
                            <td>{{ $sub->plan_name }}</td>
                            <td>
                                @php
                                    $cur = \App\Models\Currency::where('currency_code', $sub->currency)->first();
                                    $symbol = $cur ? $cur->symbol : $sub->currency;
                                @endphp
                                {!! $symbol !!} {{ number_format($sub->price, 2) }}
                            </td>
                            <td>{{ $sub->duration_days }} Days</td>
                            <td><span style="padding: 4px 8px; border-radius: 4px; background: #e2e8f0; color: #475569; font-size: 12px; font-weight: bold; text-transform: uppercase;">{{ $sub->payment_type ?? 'N/A' }}</span></td>
                            <td>{{ $sub->start_date ? $sub->start_date->format('M d, Y') : '-' }}</td>
                            <td>{{ $sub->end_date ? $sub->end_date->format('M d, Y') : '-' }}</td>
                            <td>
                                @if($sub->status === 'active')
                                    <span style="padding: 4px 8px; border-radius: 4px; background: #d1fae5; color: #065f46; font-size: 12px; font-weight: bold;">Active</span>
                                @elseif($sub->status === 'expired')
                                    <span style="padding: 4px 8px; border-radius: 4px; background: #fef3c7; color: #92400e; font-size: 12px; font-weight: bold;">Expired</span>
                                @elseif($sub->status === 'cancelled')
                                    <span style="padding: 4px 8px; border-radius: 4px; background: #fee2e2; color: #991b1b; font-size: 12px; font-weight: bold;">Cancelled</span>
                                @else
                                    <span style="padding: 4px 8px; border-radius: 4px; background: #e2e8f0; color: #475569; font-size: 12px; font-weight: bold;">{{ ucfirst($sub->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $sub->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-file-invoice-dollar" style="font-size: 32px;"></i>
                                    <h3>No User Subscriptions Found</h3>
                                    <p>Users who subscribe to a plan will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
</div>
@endsection
