@extends('admin.layout')

@section('title', 'Manage Currencies')
@section('page-title', 'Manage Currencies')

@section('content')
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <span class="card-title">Currencies</span>
        <div style="display:flex; gap:10px; align-items:center;">
            <form action="{{ route('admin.currencies.index') }}" method="GET" style="display:flex; border: 1.5px solid var(--border); border-radius:10px; overflow:hidden;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search currencies..." style="border:none; padding:10px 15px; background:transparent; color:var(--text); outline:none;">
                <button type="submit" style="background:var(--primary); color:#fff; border:none; padding:10px 20px; font-weight:600; cursor:pointer;"><i class="fas fa-search"></i> Search</button>
            </form>
            <a href="{{ route('admin.currencies.create') }}" class="btn btn-primary">Add Currency</a>
        </div>
    </div>
    <div class="table-wrap" style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
            <thead>
                <tr style="border-bottom:1px solid var(--border); background:rgba(0,0,0,0.02);">
                    <th style="padding:16px;">ID</th>
                    <th style="padding:16px;">Currency Code</th>
                    <th style="padding:16px;">Name</th>
                    <th style="padding:16px;">Symbol</th>
                    <th style="padding:16px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($currencies as $currency)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:16px;">{{ $currency->id }}</td>
                    <td style="padding:16px; font-weight:600;">{{ $currency->currency_code }}</td>
                    <td style="padding:16px;">{{ $currency->currency_name }}</td>
                    <td style="padding:16px;">{{ $currency->symbol }}</td>
                    <td style="padding:16px;">
                        <a href="{{ route('admin.currencies.edit', $currency) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                        <form action="{{ route('admin.currencies.destroy', $currency) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this currency?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:20px; text-align:center; color:var(--text-muted);">No currencies found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 20px;">
        {{ $currencies->links() }}
    </div>
</div>
@endsection
