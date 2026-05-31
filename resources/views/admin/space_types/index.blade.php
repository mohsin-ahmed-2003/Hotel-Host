@extends('admin.layout')

@section('title', 'Manage Space Types')
@section('page-title', 'Space Types')

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">Space Types</span>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('admin.space-types.create') }}" class="btn btn-primary">Add Space Type</a>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($spaceTypes as $st)
                <tr>
                    <td>{{ $st->id }}</td>
                    <td>{{ $st->name }}</td>
                    <td>{{ $st->status ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('admin.space-types.edit', $st) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.space-types.destroy', $st) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this space type?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding: 20px;">{{ $spaceTypes->links() }}</div>
</div>
@endsection
