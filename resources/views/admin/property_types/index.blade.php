@extends('admin.layout')

@section('title', 'Manage Property Types')
@section('page-title', 'Property Types')

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">Property Types</span>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('admin.property-types.create') }}" class="btn btn-primary">Add Property Type</a>
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
                @foreach($propertyTypes as $pt)
                <tr>
                    <td>{{ $pt->id }}</td>
                    <td>{{ $pt->name }}</td>
                    <td>{{ $pt->status ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('admin.property-types.edit', $pt) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.property-types.destroy', $pt) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this property type?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding: 20px;">{{ $propertyTypes->links() }}</div>
</div>
@endsection
