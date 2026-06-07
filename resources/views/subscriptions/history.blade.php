@extends('layouts.app')

@section('title', 'Subscription History')

@section('styles')
    <style>
        .history-container {
            max-width: 1000px;
            margin: 20px auto 60px;
            padding: 0 24px;
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .history-title {
            font-size: 28px;
            font-weight: 800;
            color: #1e293b;
        }

        .history-card {
            background: #fff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th,
        td {
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
        }

        th {
            background: #f8fafc;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }

        td {
            font-size: 15px;
            color: #1e293b;
            vertical-align: middle;
        }

        .plan-name {
            font-weight: 700;
            color: #0f172a;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-expired {
            background: #fef3c7;
            color: #92400e;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-pending {
            background: #e0e7ff;
            color: #3730a3;
        }

        .btn-unsubscribe {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-unsubscribe:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 24px;
        }

        .empty-icon {
            font-size: 48px;
            color: #cbd5e1;
            margin-bottom: 16px;
        }
    </style>
@endsection

@section('content')
    <div class="history-container">
        <div class="history-header">
            <h1 class="history-title">My Subscription History</h1>
            <a href="{{ route('subscriptions.index') }}" class="btn btn-primary"
                style="background: #0ea5e9; color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 600;">
                View Plans
            </a>
        </div>

        @if(session('success'))
            <div
                style="background: #d1fae5; color: #065f46; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 500;">
                <i class="fas fa-check-circle" style="margin-right: 8px;"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div
                style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 500;">
                <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i> {{ session('error') }}
            </div>
        @endif

        <div class="history-card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Plan Name</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $sub)
                            <tr>
                                <td>
                                    <div class="plan-name">{{ $sub->plan_name }}</div>
                                    <div style="font-size: 12px; color: #64748b;">Subscribed on
                                        {{ $sub->created_at->format('M d, Y') }}</div>
                                </td>
                                <td>
                                    @php
                                        $cur = \App\Models\Currency::where('currency_code', $sub->currency)->first();
                                        $symbol = $cur ? $cur->symbol : $sub->currency;
                                    @endphp
                                    <strong>{!! $symbol !!}{{ number_format($sub->price, 2) }}</strong>
                                </td>
                                <td>{{ $sub->duration_days }} Days</td>
                                <td><span style="padding: 4px 8px; border-radius: 4px; background: #e2e8f0; color: #475569; font-size: 12px; font-weight: bold; text-transform: uppercase;">{{ $sub->payment_type ?? 'N/A' }}</span></td>
                                <td>
                                    <span class="status-badge status-{{ $sub->status }}">
                                        {{ ucfirst($sub->status) }}
                                    </span>
                                </td>
                                <td>{{ $sub->start_date ? $sub->start_date->format('M d, Y') : '-' }}</td>
                                <td>{{ $sub->end_date ? $sub->end_date->format('M d, Y') : '-' }}</td>
                                <td>
                                    @if($sub->status === 'active')
                                        <form action="{{ route('subscriptions.unsubscribe', $sub->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to unsubscribe from this plan? This action will cancel your host benefits.');">
                                            @csrf
                                            <button type="submit" class="btn-unsubscribe">Unsubscribe</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="fas fa-file-invoice-dollar empty-icon"></i>
                                        <h3 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">No
                                            Subscriptions Found</h3>
                                        <p style="color: #64748b;">You haven't subscribed to any plans yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection