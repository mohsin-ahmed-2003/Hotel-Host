@extends('admin.layout')
@section('title', 'Edit Space Type')
@section('page-title', 'Edit Space Type')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('admin.space-types.update', $spaceType) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ $spaceType->name }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">
                    <input type="checkbox" name="status" value="1" {{ $spaceType->status ? 'checked' : '' }}> Active
                </label>
            </div>
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.space-types.index') }}" class="btn btn-outline">Cancel</a>
        </form>
    </div>
</div>
@endsection
