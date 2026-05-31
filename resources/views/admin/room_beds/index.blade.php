@extends('admin.layout')

@section('title', 'Manage Bed Arrangements')
@section('page-title', 'Bed Arrangement')

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">Bed Arrangements</span>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('admin.room-beds.create') }}" class="btn btn-primary">Add Bed Type</a>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roomBeds as $bed)
                <tr>
                    <td>{{ $bed->id }}</td>
                    <td>
                        @if($bed->image)
                            <img src="{{ Storage::url($bed->image) }}" alt="image" style="width: 32px; height: 32px; object-fit: contain;">
                        @else
                            <span style="color:#999;font-size:12px;">No Image</span>
                        @endif
                    </td>
                    <td>{{ $bed->name }}</td>
                    <td>
                        <a href="{{ route('admin.room-beds.edit', $bed->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.room-beds.destroy', $bed->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this bed arrangement?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding: 20px;">{{ $roomBeds->links() }}</div>
</div>
@endsection
