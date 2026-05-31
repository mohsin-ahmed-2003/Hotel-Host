@extends('admin.layout')

@section('title', 'Manage Room Rules')
@section('page-title', 'Room Rules')

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">Room Rules</span>
        <div style="display:flex;gap:10px;align-items:center;">
            <form action="{{ route('admin.room-rules.index') }}" method="GET" class="search-box">
                <input type="text" name="search" class="search-input" placeholder="Search rules..." value="{{ request('search') }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="top:11px;">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </form>
            <a href="{{ route('admin.room-rules.create') }}" class="btn btn-primary">Add Room Rule</a>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th style="width: 80px; text-align: center;">Icon</th>
                    <th>Rule Name</th>
                    <th>Rule Text</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roomRules as $rule)
                <tr>
                    <td>{{ $rule->id }}</td>
                    <td style="text-align: center;">
                        <span style="font-size: 18px; color: var(--primary); display: inline-block; width: 36px; height: 36px; line-height: 36px; background: var(--primary-light); border-radius: 50%;">
                            <i class="{{ $rule->icon ?? 'fas fa-clipboard-list' }}"></i>
                        </span>
                    </td>
                    <td><strong>{{ $rule->rule_name }}</strong></td>
                    <td class="text-muted">{{ $rule->rule_text }}</td>
                    <td>
                        <a href="{{ route('admin.room-rules.edit', $rule) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.room-rules.destroy', $rule) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this room rule?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px;" class="text-muted">
                        No Room Rules found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 20px;">{{ $roomRules->appends(request()->query())->links() }}</div>
</div>
@endsection
