@extends('admin.layout')
@section('title', 'Edit Property Type')
@section('page-title', 'Edit Property Type')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('admin.property-types.update', $propertyType) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ $propertyType->name }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">
                    <input type="checkbox" name="status" value="1" {{ $propertyType->status ? 'checked' : '' }}> Active
                </label>
            </div>
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.property-types.index') }}" class="btn btn-outline">Cancel</a>
        </form>
    </div>
</div>
@endsection
