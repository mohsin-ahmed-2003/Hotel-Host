@extends('admin.layout')
@section('title', 'Add Property Type')
@section('page-title', 'Add Property Type')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('admin.property-types.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">
                    <input type="checkbox" name="status" value="1" checked> Active
                </label>
            </div>
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('admin.property-types.index') }}" class="btn btn-outline">Cancel</a>
        </form>
    </div>
</div>
@endsection
