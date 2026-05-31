@extends('host.layout')

@section('host-content')
<h1 class="host-title">Let's start with the basics</h1>
<p class="host-subtitle">Help guests understand what your property is like.</p>

<div class="row g-3">
    <div class="col-12">
        <div class="form-floating-airbnb" id="wrap_propName">
            <input type="text" class="form-control-airbnb" name="name" id="propName" value="{{ $room->name }}" placeholder=" " oninput="autoSave('name', this.value, 'rooms', 'propName')">
            <label for="propName">Internal Property Name (e.g. My Beach House)</label>
        </div>
    </div>
    
    <div class="col-12">
        <div class="form-floating-airbnb" id="wrap_propTitle">
            <input type="text" class="form-control-airbnb" name="title" id="propTitle" value="{{ $room->title }}" placeholder=" " oninput="autoSave('title', this.value, 'rooms', 'propTitle')">
            <label for="propTitle">Public Listing Title (e.g. Beautiful Beachfront Villa)</label>
        </div>
    </div>

    <div class="col-12">
        <div class="form-floating-airbnb" id="wrap_propDesc">
            <textarea class="form-control-airbnb" name="description" id="propDesc" rows="4" placeholder=" " oninput="autoSave('description', this.value, 'rooms', 'propDesc')" style="min-height: 120px; max-height: 120px; resize: none; overflow-y: auto;">{{ $room->description }}</textarea>
            <label for="propDesc">Description (atmosphere, location, unique features)</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating-airbnb" id="wrap_propType">
            <select class="form-control-airbnb" name="property_type_id" id="propType" onchange="autoSave('property_type_id', this.value, 'rooms', 'propType')">
                <option value=""></option>
                @foreach($propertyTypes as $pt)
                    <option value="{{ $pt->id }}" {{ $room->property_type_id == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                @endforeach
            </select>
            <label for="propType">Property Type</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating-airbnb" id="wrap_spaceType">
            <select class="form-control-airbnb" name="space_type_id" id="spaceType" onchange="autoSave('space_type_id', this.value, 'rooms', 'spaceType')">
                <option value=""></option>
                @foreach($spaceTypes as $st)
                    <option value="{{ $st->id }}" {{ $room->space_type_id == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                @endforeach
            </select>
            <label for="spaceType">Space Type</label>
        </div>
    </div>

    <div class="col-12">
        <div class="form-floating-airbnb" id="wrap_accommodation">
            <select class="form-control-airbnb" name="accommodation" id="accommodation" onchange="autoSave('accommodation', this.value, 'rooms', 'accommodation')">
                <option value=""></option>
                @for($i = 1; $i <= 15; $i++)
                    <option value="{{ $i }}" {{ $room->accommodation == $i ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}</option>
                @endfor
                <option value="16" {{ $room->accommodation == 16 ? 'selected' : '' }}>16+ Guests</option>
            </select>
            <label for="accommodation">Accommodation</label>
        </div>
    </div>
</div>

<div class="host-actions">
    <a href="{{ route('host.step', ['room' => $room->id, 'step' => 2]) }}" class="btn-next">
        <span class="btn-text">Save & Next</span>
        <div class="btn-spinner"></div>
    </a>
</div>
@endsection
