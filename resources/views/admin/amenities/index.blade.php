@extends('admin.layout')

@section('title', 'Manage Amenities')
@section('page-title', 'Amenities')

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">Amenities</span>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('admin.amenities.create') }}" class="btn btn-primary">Add Amenity</a>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($amenities as $am)
                <tr>
                    <td>{{ $am->id }}</td>
                    <td>
                        @if($am->image)
                            <img src="{{ Storage::url($am->image) }}" alt="image" style="width: 32px; height: 32px; object-fit: contain;">
                        @else
                            <span style="color:#999;font-size:12px;">No Image</span>
                        @endif
                    </td>
                    <td>{{ $am->name }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($am->description, 50) }}</td>
                    <td>
                        <a href="{{ route('admin.amenities.edit', $am) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.amenities.destroy', $am) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this amenity?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding: 20px;">{{ $amenities->links() }}</div>
</div>
@endsection
