@extends('admin.layout')

@section('title', 'Manage Countries')
@section('page-title', 'Manage Countries')

@section('content')
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <span class="card-title">Countries</span>
        <div style="display:flex; gap:10px; align-items:center;">
            <form action="{{ route('admin.countries.index') }}" method="GET" style="display:flex; border: 1.5px solid var(--border); border-radius:10px; overflow:hidden;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search countries..." style="border:none; padding:10px 15px; background:transparent; color:var(--text); outline:none;">
                <button type="submit" style="background:var(--primary); color:#fff; border:none; padding:10px 20px; font-weight:600; cursor:pointer;"><i class="fas fa-search"></i> Search</button>
            </form>
            <a href="{{ route('admin.countries.create') }}" class="btn btn-primary">Add Country</a>
        </div>
    </div>
    <div class="table-wrap" style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
            <thead>
                <tr style="border-bottom:1px solid var(--border); background:rgba(0,0,0,0.02);">
                    <th style="padding:16px;">ID</th>
                    <th style="padding:16px;">Name</th>
                    <th style="padding:16px;">Short Name</th>
                    <th style="padding:16px;">Phone Code</th>
                    <th style="padding:16px;">Currency</th>
                    <th style="padding:16px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($countries as $country)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:16px;">{{ $country->id }}</td>
                    <td style="padding:16px; font-weight:600;">{{ $country->country_name }}</td>
                    <td style="padding:16px;">{{ $country->short_name }}</td>
                    <td style="padding:16px;">{{ $country->phone_code }}</td>
                    <td style="padding:16px;">{{ $country->currency }}</td>
                    <td style="padding:16px;">
                        <a href="{{ route('admin.countries.edit', $country) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                        <form action="{{ route('admin.countries.destroy', $country) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this country?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:20px; text-align:center; color:var(--text-muted);">No countries found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 20px;">
        {{ $countries->links() }}
    </div>
</div>
@endsection
