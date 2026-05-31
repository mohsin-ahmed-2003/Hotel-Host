@extends('admin.layout')
@section('title', 'Edit Bed Type')
@section('page-title', 'Edit Bed Type')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('admin.room-beds.update', $roomBed->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Bed Name</label>
                <input type="text" name="name" class="form-control" value="{{ $roomBed->name }}" required>
            </div>
            @if($roomBed->image)
                <div class="form-group">
                    <label class="form-label">Current Image</label>
                    <div>
                        <img src="{{ Storage::url($roomBed->image) }}" alt="image" style="width: 64px; height: 64px; object-fit: contain; border-radius: 8px; border: 1px solid #ddd;">
                    </div>
                </div>
            @endif
            <div class="form-group">
                <label class="form-label">Update Image / Icon</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <button class="btn btn-primary">Save Changes</button>
            <a href="{{ route('admin.room-beds.index') }}" class="btn btn-outline">Cancel</a>
        </form>
    </div>
</div>
@endsection
