@extends('admin.layout')
@section('title', 'Edit Room Rule')
@section('page-title', 'Edit Room Rule')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('admin.room-rules.update', $roomRule) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Rule Name</label>
                <input type="text" name="rule_name" class="form-control" value="{{ $roomRule->rule_name }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Rule Description / Text</label>
                <textarea name="rule_text" class="form-control" rows="3">{{ $roomRule->rule_text }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">FontAwesome Icon Class</label>
                <input type="text" name="icon" class="form-control" value="{{ $roomRule->icon }}">
                <span class="text-muted small mt-1 d-block">Standard rules icons: <code>fas fa-smog</code>, <code>fas fa-paw</code>, <code>fas fa-glass-cheers</code>, <code>fas fa-volume-mute</code>, <code>fas fa-camera</code>, <code>fas fa-baby</code>, <code>fas fa-utensils</code>.</span>
            </div>
            
            <button class="btn btn-primary">Update Rule</button>
            <a href="{{ route('admin.room-rules.index') }}" class="btn btn-outline">Cancel</a>
        </form>
    </div>
</div>
@endsection
