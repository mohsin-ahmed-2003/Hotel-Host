@extends('admin.layout')

@section('title', 'Manage Currency Rates')
@section('page-title', 'Manage Currency Rates')

@section('content')
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <span class="card-title">Currency Rates (Base: USD)</span>
        <div style="display:flex; gap:10px; align-items:center;">
            <form action="{{ route('admin.currency-rates.index') }}" method="GET" style="display:flex; border: 1.5px solid var(--border); border-radius:10px; overflow:hidden;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code/name..." style="border:none; padding:10px 15px; background:transparent; color:var(--text); outline:none;">
                <button type="submit" style="background:var(--primary); color:#fff; border:none; padding:10px 20px; font-weight:600; cursor:pointer;"><i class="fas fa-search"></i> Search</button>
            </form>
            <a href="{{ route('admin.currency-rates.create') }}" class="btn btn-outline">Add Rate</a>
            <form action="{{ route('admin.currency-rates.sync') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-primary" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Syncing...';"><i class="fas fa-sync-alt"></i> Sync currency</button>
            </form>
        </div>
    </div>

    <div class="table-wrap" style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
            <thead>
                <tr style="border-bottom:1px solid var(--border); background:rgba(0,0,0,0.02);">
                    <th style="padding:16px;">ID</th>
                    <th style="padding:16px;">Currency Code</th>
                    <th style="padding:16px;">Currency Name</th>
                    <th style="padding:16px;">Symbol</th>
                    <th style="padding:16px;">Rate (1 USD =)</th>
                    <th style="padding:16px;">Date</th>
                    <th style="padding:16px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rates as $rate)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:16px;">{{ $rate->id }}</td>
                    <td style="padding:16px; font-weight:600;">{{ $rate->target_currency }}</td>
                    <td style="padding:16px;">{{ $rate->currency ? $rate->currency->currency_name : '-' }}</td>
                    <td style="padding:16px;">{{ $rate->currency ? $rate->currency->symbol : '-' }}</td>
                    <td style="padding:16px; font-weight:600; color:var(--primary);">{{ number_format($rate->rate, 6) }}</td>
                    <td style="padding:16px;">{{ $rate->rate_date }}<br><span style="color:var(--text-muted); font-size:12px;">Synced: {{ $rate->updated_at->diffForHumans() }}</span></td>
                    <td style="padding:16px;">
                        <a href="{{ route('admin.currency-rates.edit', $rate) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                        <form action="{{ route('admin.currency-rates.destroy', $rate) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this currency rate?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:20px; text-align:center; color:var(--text-muted);">No currency rates found. Click "Sync currency" to fetch the latest rates or "Add Rate" to enter one manually.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 20px;">
        {{ $rates->links() }}
    </div>
</div>
@endsection
