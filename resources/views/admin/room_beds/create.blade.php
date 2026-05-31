@extends('admin.layout')
@section('title', 'Add Bed Type')
@section('page-title', 'Add Bed Type')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('admin.room-beds.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Bed Name</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Single Bed, Double Bed">
            </div>
            <div class="form-group">
                <label class="form-label">Image / Icon</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('admin.room-beds.index') }}" class="btn btn-outline">Cancel</a>
        </form>
    </div>
</div>
@endsection
