@extends('admin.layout')
@section('title', 'Edit Amenity')
@section('page-title', 'Edit Amenity')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('admin.amenities.update', $amenity) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ $amenity->name }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $amenity->description }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Image</label>
                @if($amenity->image)
                    <div style="margin-bottom: 10px;">
                        <img src="{{ Storage::url($amenity->image) }}" alt="image" style="height: 40px; border-radius: 4px;">
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
                <small class="field-hint">Leave empty to keep current image</small>
            </div>
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.amenities.index') }}" class="btn btn-outline">Cancel</a>
        </form>
    </div>
</div>
@endsection
