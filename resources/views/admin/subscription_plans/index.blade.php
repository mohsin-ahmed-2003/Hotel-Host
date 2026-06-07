@extends('admin.layout')

@section('title', 'Manage Subscription Plans')

@section('page-title', 'Subscription Plans')

@section('content')
<div class="card">
        <div class="card-header">
            <span class="card-title">Subscription Plans</span>
            <div style="display:flex;gap:10px;">
                <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Plan
                </a>
            </div>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Plan Name</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Hosting Limit</th>
                        <th>Cancellations Allowed</th>
                        <th>Fee Reduction</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        <tr>
                            <td><strong>{{ $plan->name }}</strong></td>
                            <td>
                                @php
                                    $cur = \App\Models\Currency::where('currency_code', $plan->currency)->first();
                                    $symbol = $cur ? $cur->symbol : $plan->currency;
                                @endphp
                                {!! $symbol !!}{{ number_format($plan->price, 2) }}
                            </td>
                            <td>{{ $plan->duration_days }} Days</td>
                            <td>{{ is_null($plan->hosting_allowed) ? 'Unlimited' : $plan->hosting_allowed }}</td>
                            <td>{{ $plan->cancellations_allowed }}</td>
                            <td>{{ $plan->cancellation_fee_reduction }}%</td>
                            <td>
                                @if($plan->is_active)
                                    <span style="padding: 4px 8px; border-radius: 4px; background: #d1fae5; color: #065f46; font-size: 12px; font-weight: bold;">Active</span>
                                @else
                                    <span style="padding: 4px 8px; border-radius: 4px; background: #fee2e2; color: #991b1b; font-size: 12px; font-weight: bold;">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; gap:8px;">
                                    <a href="{{ route('admin.subscription-plans.edit', $plan->id) }}" class="btn btn-sm btn-outline">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.subscription-plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Delete this plan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-box-open" style="font-size: 32px;"></i>
                                    <h3>No Subscription Plans Found</h3>
                                    <p>Create your first plan to start monetizing hosts.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
</div>
@endsection
