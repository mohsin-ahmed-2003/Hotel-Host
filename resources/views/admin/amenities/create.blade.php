@extends('admin.layout')
@section('title', 'Add Amenity')
@section('page-title', 'Add Amenity')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('admin.amenities.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('admin.amenities.index') }}" class="btn btn-outline">Cancel</a>
        </form>
    </div>
</div>
@endsection
