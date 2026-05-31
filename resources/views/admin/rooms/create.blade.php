@extends('admin.layout')

@section('title', (isset($room) ? 'Edit' : 'Add') . ' Room')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    :root {
        --primary-color: #ff385c;
    }

    .price-per-guest-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(16, 185, 129, 0.06);
        border: 1px solid rgba(16, 185, 129, 0.15);
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 12.5px !important;
        font-weight: 600 !important;
        color: #10b981 !important;
        cursor: pointer;
        user-select: none;
        transition: all 0.25s ease;
    }
    .price-per-guest-toggle:hover {
        background: rgba(16, 185, 129, 0.12);
        border-color: rgba(16, 185, 129, 0.3);
        transform: translateY(-1px);
    }
    .price-per-guest-toggle input[type="checkbox"] {
        width: 15px !important;
        height: 15px !important;
        accent-color: #10b981 !important;
        cursor: pointer;
        margin: 0 !important;
    }

    /* Multi-step Container */
    .step-container-wrap {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        display: flex;
        min-height: 600px;
        overflow: hidden;
        border: 1px solid var(--border);
    }

    /* Step Navigation */
    .step-nav-sidebar {
        width: 150px;
        background: #f8f9fa;
        border-right: 1px solid #eee;
        padding: 30px 15px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

        .nav-item-step {
        padding: 10px 12px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid #eee;
        font-size: 12px;
        font-weight: 700;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }

    .nav-item-step .step-icon { display: flex; align-items: center; justify-content: center; opacity: 0.5; transition: opacity 0.3s; }
    .nav-item-step .step-label { font-size: 11px; }

    .nav-item-step.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(255, 56, 92, 0.2);
    }

    .nav-item-step.active .step-icon { opacity: 1; }

    .nav-item-step.completed {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
    }

    .nav-item-step.completed .step-icon { opacity: 1; }

    /* Form Content */
    .step-form-content {
        flex: 1;
        padding: 40px 60px;
        display: flex;
        flex-direction: column;
    }

    .step-section {
        display: none;
        animation: fadeIn 0.4s ease-out;
    }

    .step-section.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    h1 {
        font-weight: 800;
        font-size: 32px;
        margin-bottom: 10px;
        color: #222;
    }

    .step-desc {
        font-size: 16px;
        color: #717171;
        margin-bottom: 30px;
    }

    .form-control-airbnb {
        border: 1px solid #b0b0b0;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 15px;
        transition: all 0.2s;
    }

    .form-control-airbnb:focus {
        border-color: #222;
        box-shadow: 0 0 0 1px #222;
        outline: none;
    }

    .amenity-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
    }

    .amenity-card {
        border: 1px solid #ddd;
        border-radius: 12px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }

    .amenity-card:hover { border-color: #222; }
    .amenity-card.active {
        border: 2px solid #222;
        background: #f8f9fa;
        font-weight: 600;
    }

    .form-footer {
        margin-top: auto;
        padding-top: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-next {
        background: var(--primary-color);
        color: white;
        padding: 12px 35px;
        border-radius: 10px;
        font-weight: 700;
        border: none;
        transition: transform 0.2s;
    }

    .btn-next:hover { transform: scale(1.02); background: #e31c5f; color: white; }

    .btn-back {
        background: none;
        border: none;
        text-decoration: underline;
        font-weight: 600;
        color: #222;
    }

    /* Side Image Panel - Hidden as requested */
    .image-panel {
        display: none;
    }

    /* Media Grid */
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 24px;
    }

    .media-item {
        position: relative;
        border-radius: 16px;
        border: 1px solid #eee;
        background: #fff;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .media-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }

    .media-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        display: block;
    }

    .media-content {
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .btn-delete-media {
        color: #ff385c;
        background: none;
        border: none;
        font-size: 13px;
        font-weight: 700;
        text-decoration: underline;
        cursor: pointer;
        padding: 0;
        text-align: right;
    }

    /* Flex Utilities (Admin layout lacks Bootstrap) */
    .d-flex { display: flex !important; }
    .justify-content-between { justify-content: space-between !important; }
    .align-items-center { align-items: center !important; }
    .mb-1 { margin-bottom: 0.25rem !important; }
    .mb-4 { margin-bottom: 1.5rem !important; }
    .m-0 { margin: 0 !important; }

    /* Dynamic Enhancement card styling */
    .enhancement-row-card {
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 20px;
        transition: border-color 0.3s;
    }
    .enhancement-row-card:hover {
        border-color: var(--primary-color);
    }
    .btn-counter-bed {
        background: #fff;
        border: 1.5px solid #ddd;
        color: #222;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s;
        outline: none;
    }
    .btn-counter-bed:hover {
        border-color: #222;
        background: #f1f1f1;
    }
    .btn-edit-beds, .btn-menu-setup, .btn-add-discount {
        background: transparent;
        border: 1.5px solid var(--primary-color);
        color: var(--primary-color);
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 30px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-edit-beds:hover, .btn-menu-setup:hover, .btn-add-discount:hover {
        background: var(--primary-color);
        color: white;
    }
    .discount-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 16px;
    }
    .discount-header {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
    }
    .discount-checkbox {
        width: 20px;
        height: 20px;
        accent-color: var(--primary-color);
        cursor: pointer;
    }
    .discount-label {
        font-weight: 700;
        font-size: 15px;
        color: #222;
        margin: 0;
    }
    .discount-content {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #eee;
    }
    .discount-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        flex-wrap: nowrap;
    }
    .discount-input-group {
        display: flex;
        align-items: center;
        border: 1.5px solid #ddd;
        border-radius: 8px;
        background: #fff;
        overflow: hidden;
        height: 46px;
    }
    .discount-input-prefix {
        background: #f8f9fa;
        padding: 0 12px;
        font-size: 13px;
        font-weight: 700;
        color: #666;
        border-right: 1.5px solid #ddd;
        height: 100%;
        display: flex;
        align-items: center;
    }
    .discount-input-field {
        border: none;
        padding: 0 12px;
        height: 100%;
        font-size: 14px;
        font-weight: 600;
        outline: none;
        background: transparent;
        color: #222;
    }
    .discount-select {
        height: 46px;
        border: 1.5px solid #ddd;
        border-radius: 8px;
        background: #fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") no-repeat right 12px center/12px auto;
        padding: 0 32px 0 12px;
        font-size: 14px;
        font-weight: 600;
        color: #222;
        outline: none;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        width: 180px;
        transition: border-color 0.2s;
    }
    .btn-remove-discount {
        background: none;
        border: none;
        color: #ff385c;
        cursor: pointer;
        padding: 8px;
        font-size: 14px;
        transition: opacity 0.2s;
    }
    .btn-remove-discount:hover {
        opacity: 0.8;
    }
    .food-items-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
        margin-top: 15px;
    }
    .food-item-card {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 10px 14px;
        background: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
    }
    /* Modal setup */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        backdrop-filter: blur(4px);
    }
    .modal-card {
        background: #fff;
        width: 100%;
        max-width: 500px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-body {
        padding: 24px;
        max-height: 400px;
        overflow-y: auto;
    }
    .modal-footer {
        padding: 20px 24px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
    .dynamic-item-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }
    .bed-counter-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid rgba(0,0,0,0.04);
    }
    .bed-info-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .bed-icon-img {
        width: 24px;
        height: 24px;
        object-fit: contain;
    }
    .bed-name-text {
        font-size: 14px;
        font-weight: 600;
        color: #222;
    }
    .bed-count-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .bed-count-value {
        font-size: 15px;
        font-weight: 700;
        width: 20px;
        text-align: center;
    }
    .dropdown-add-bed-wrap {
        margin-top: 15px;
        border-top: 1px solid #ddd;
        padding-top: 15px;
    }
    .select-add-bed {
        width: 100%;
        height: 48px;
        border-radius: 12px;
        border: 1.5px solid #ddd;
        background: #fff;
        color: #222;
        padding: 0 16px;
        font-size: 14px;
        font-weight: 600;
        outline: none;
        cursor: pointer;
    }

    /* Custom Flatpickr alignment and size styling matching rooms/show.blade.php */
    .flatpickr-calendar, 
    .flatpickr-calendar.open, 
    .flatpickr-calendar.animate.open {
        transform: scale(0.85) !important; 
        transform-origin: top left !important;
        border-radius: 16px !important;
        border: 1px solid rgba(255, 56, 92, 0.4) !important;
        box-shadow: 0 15px 40px rgba(255, 56, 92, 0.15) !important;
        padding: 5px;
    }
    .flatpickr-day.is-sunday:not(.flatpickr-disabled):not(.disabled) {
        color: #ef4444 !important; 
        font-weight: bold;
    }
    .flatpickr-day.today {
        background: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: white !important;
        box-shadow: 0 4px 10px rgba(255, 56, 92, 0.3);
    }
    .flatpickr-day.start-date-highlight {
        background: rgba(255, 56, 92, 0.15) !important;
        border-color: var(--primary-color) !important;
        color: var(--primary-color) !important;
        font-weight: 700;
        border-radius: 50%;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h2 class="fw-bold m-0">{{ isset($room) ? 'Edit Room' : 'Add New Room' }}</h2>
        <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary btn-sm" style="padding: 7px 34px 10px; background-color: red;margin-top: 10px;">
            <i class="fas fa-times me-1"></i> Cancel
        </a>
    </div>
    <p class="text-muted small mb-4">Complete all steps to list your property.</p>

    <div class="step-container-wrap">
        <!-- Inner Step Sidebar -->
        <div class="step-nav-sidebar">
            @php
                $stepLabels = [
                    1 => ['label' => 'Basics',     'icon' => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>'],
                    2 => ['label' => 'Media',      'icon' => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>'],
                    3 => ['label' => 'Location',   'icon' => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>'],
                    4 => ['label' => 'Amenities',  'icon' => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>'],
                    5 => ['label' => 'Pricing',    'icon' => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'],
                    6 => ['label' => 'Rules & Policy', 'icon' => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>'],
                    7 => ['label' => 'Review',     'icon' => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'],
                ];
            @endphp
            @foreach($stepLabels as $num => $step)
                <div class="nav-item-step {{ $num == 1 ? 'active' : '' }}" id="nav-step-{{ $num }}" onclick="goToStep({{ $num }})">
                    <div class="step-icon">{!! $step['icon'] !!}</div>
                    <div class="step-label">{{ $step['label'] }}</div>
                </div>
            @endforeach
        </div>

        <!-- Form Content -->
        <div class="step-form-content">
            <div id="adminToastContainer" style="position: sticky; top: 0; z-index: 1000; margin-bottom: 20px;"></div>

            <form id="multiStepForm" enctype="multipart/form-data">
                @csrf
                @if(isset($room)) @method('PUT') @endif
                
                <input type="hidden" name="room_id" value="{{ $room->id ?? '' }}">
                <input type="hidden" name="bedroom_allocations" id="bedroomAllocationsInput">
                <input type="hidden" name="enhancements" id="enhancementsInput">
                <input type="hidden" name="discounts" id="discountsInput">

                <!-- Step 1: Basic -->
                <div class="step-section active" id="step-1">
                    <h1>Let's start with the basics</h1>
                    <p class="step-desc">{{ $stepSettings['basic']->description ?? 'Help guests understand what your property is like.' }}</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Name of room</label>
                            <input type="text" name="name" class="form-control form-control-airbnb" placeholder="e.g. Master Bedroom" value="{{ $room->name ?? '' }}" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Property Title</label>
                            <input type="text" name="title" class="form-control form-control-airbnb" placeholder="e.g. Beautiful Beachfront Villa" value="{{ $room->title ?? '' }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control form-control-airbnb" rows="5" placeholder="Atmosphere, location, unique features...">{{ $room->description ?? '' }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Property Type</label>
                            <select name="property_type_id" class="form-select form-control-airbnb">
                                @foreach($propertyTypes as $type)
                                    <option value="{{ $type->id }}" {{ (isset($room) && $room->property_type_id == $type->id) ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Space Type</label>
                            <select name="space_type_id" class="form-select form-control-airbnb">
                                @foreach($spaceTypes as $type)
                                    <option value="{{ $type->id }}" {{ (isset($room) && $room->space_type_id == $type->id) ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Accommodation (Guests)</label>
                        <input type="number" name="accommodation" class="form-control form-control-airbnb" value="{{ $room->accommodation ?? 1 }}">
                    </div>
                </div>

                <!-- Step 2: Media -->
                <div class="step-section" id="step-2">
                    <h1>Add some photos</h1>
                    <p class="step-desc">{{ $stepSettings['media']->description ?? 'Upload clear photos to attract more guests.' }}</p>
                    
                    <div class="border rounded-4 p-5 text-center bg-light mb-4" style="border-style: dashed !important; border-width: 2px !important;">
                        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3 opacity-75"></i>
                        <h5 class="fw-bold">Upload photos</h5>
                        <p class="text-muted small">JPG, PNG or WEBP (Max. 5MB each)</p>
                        <input type="file" name="photos[]" id="photoInput" multiple class="d-none" accept="image/*">
                        <button type="button" class="btn btn-primary px-4 rounded-pill mt-2" onclick="document.getElementById('photoInput').click()">
                            <i class="fas fa-plus me-2"></i>Select Photos
                        </button>
                    </div>

                    <div id="photoPreview" class="media-grid mb-4"></div>

                    @if(isset($room) && $room->photos->count() > 0)
                        <hr class="my-4">
                        <h5 class="fw-bold mb-3">Uploaded Photos</h5>
                        <div class="media-grid">
                            @foreach($room->photos as $photo)
                                <div class="media-item" id="photo_{{ $photo->id }}">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Room Photo">
                                    <div class="media-content">
                                        <input type="text" name="existing_captions[{{ $photo->id }}]" class="form-control form-control-sm border-0 bg-light" placeholder="Add a caption..." value="{{ $photo->description ?? '' }}" style="font-size: 12px; border-radius: 8px;">
                                        <button type="button" class="btn-delete-media" onclick="deletePhoto({{ $photo->id }}, this)">
                                            <i class="fas fa-trash me-1"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Step 3: Location -->
                <div class="step-section" id="step-3">
                    <h1>Where's your place located?</h1>
                    <p class="step-desc">{{ $stepSettings['location']->description ?? 'Your address is only shared with guests after they book.' }}</p>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Street Address</label>
                        <input type="text" name="address" class="form-control form-control-airbnb" value="{{ $room->roomLocation->location_name ?? $room->address ?? '' }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">City</label>
                            <input type="text" name="city" class="form-control form-control-airbnb" value="{{ $room->roomLocation->city ?? $room->city ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">State</label>
                            <input type="text" name="state" class="form-control form-control-airbnb" value="{{ $room->roomLocation->state ?? $room->state ?? '' }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Country</label>
                        <input type="text" name="country" class="form-control form-control-airbnb" value="{{ $room->roomLocation->country ?? $room->country ?? '' }}">
                    </div>
                </div>

                <!-- Step 4: Amenities -->
                <div class="step-section" id="step-4">
                    <h1>What amenities & spaces do you offer?</h1>
                    <p class="step-desc">{{ $stepSettings['amenities']->description ?? 'Detail your property\'s amenities, sleeping arrangements, and extra services.' }}</p>
                    
                    <div class="mb-5">
                        <h3 class="fw-bold h5 mb-3">Essentials & Features</h3>
                        <div class="amenity-grid">
                            @foreach($amenities as $amenity)
                                <div class="amenity-card {{ (isset($room) && $room->amenities->contains($amenity->id)) ? 'active' : '' }}" onclick="toggleAmenity(this, {{ $amenity->id }})">
                                    @if($amenity->image)
                                        <img src="{{ asset('storage/' . $amenity->image) }}" alt="{{ $amenity->name }}" width="24" height="24" style="object-fit: contain;">
                                    @else
                                        <i class="{{ $amenity->icon ?? 'fas fa-check' }} text-primary"></i>
                                    @endif
                                    <span>{{ $amenity->name }}</span>
                                    <input type="checkbox" name="amenity_ids[]" value="{{ $amenity->id }}" class="d-none" {{ (isset($room) && $room->amenities->contains($amenity->id)) ? 'checked' : '' }}>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <hr style="margin: 40px 0; border: 0; border-top: 1px solid #eee;">

                    <!-- Sleeping Arrangements Section -->
                    <div class="mb-5">
                        <h3 class="fw-bold h5 mb-2">Sleeping Arrangements</h3>
                        <p class="text-muted small mb-4">Specify bedrooms and the counts/types of beds in each.</p>

                        <div class="enhancement-row-card" style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div class="bed-icon-wrapper" style="width: 42px; height: 42px; border-radius: 50%; background: rgba(255, 56, 92, 0.08); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                                    <i class="fas fa-bed" style="transform: translateY(-1px);"></i>
                                </div>
                                <div>
                                    <h4 style="font-size: 1.02rem; font-weight: 700; margin: 0; color: #222;">Number of Bedrooms</h4>
                                    <p class="text-muted small mb-0" style="margin-top: 4px;">How many bedrooms does this room have?</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px; margin-left: auto;">
                                <button type="button" class="btn-counter-bed" onclick="adjustBedrooms(-1)">−</button>
                                <span class="fw-bold" style="font-size: 16px; min-width: 20px; text-align: center;" id="bedrooms_count_display">{{ $room->bedrooms_count ?? 1 }}</span>
                                <input type="hidden" name="bedrooms_count" id="bedroomsCountInput" value="{{ $room->bedrooms_count ?? 1 }}">
                                <button type="button" class="btn-counter-bed" onclick="adjustBedrooms(1)">+</button>
                            </div>
                        </div>

                        <div class="bedrooms-list" id="bedrooms_container"></div>
                    </div>

                    <hr style="margin: 40px 0; border: 0; border-top: 1px solid #eee;">

                    <!-- Food & Services Section -->
                    <div class="mb-5">
                        <h3 class="fw-bold h5 mb-2">Food & Services (Optional)</h3>
                        <p class="text-muted small mb-4">Offer extra services like meals to your guests.</p>
                        
                        <div id="enhancementsContainer"></div>
                    </div>
                </div>

                <!-- Step 5: Pricing -->
                <div class="step-section" id="step-5">
                    <h1>Now, set your price</h1>
                    <p class="step-desc">{{ $stepSettings['pricing']->description ?? 'You can change this anytime.' }}</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Price</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-tag text-muted"></i></span>
                                <input type="number" name="price" class="form-control form-control-airbnb border-start-0" placeholder="0.00" value="{{ $room->roomPrice->price ?? $room->price ?? '' }}" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Currency</label>
                            <select name="currency" class="form-select form-control-airbnb">
                                @foreach($currencies as $cur)
                                    <option value="{{ $cur->currency_code }}" {{ (isset($room->roomPrice) && $room->roomPrice->currency == $cur->currency_code) ? 'selected' : '' }}>
                                        {{ $cur->currency_code }} ({{ $cur->symbol }}) - {{ $cur->currency_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Tax Type</label>
                            <select name="tax_type" class="form-select form-control-airbnb">
                                <option value="percentage" {{ (isset($room->roomPrice) && $room->roomPrice->tax_type == 'percentage') ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="currency" {{ (isset($room->roomPrice) && $room->roomPrice->tax_type == 'currency') ? 'selected' : '' }}>Fixed Amount</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Tax Amount</label>
                            <input type="number" name="tax_amount" class="form-control form-control-airbnb" placeholder="0.00" value="{{ $room->roomPrice->tax_amount ?? '' }}" step="0.01">
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_tax_included" id="isTaxIncluded" value="1" {{ (isset($room->roomPrice) && $room->roomPrice->is_tax_included) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="isTaxIncluded">Is Tax Included?</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Security Deposit</label>
                            <input type="number" name="security_deposit" class="form-control form-control-airbnb" placeholder="0.00" value="{{ $room->roomPrice->security_deposit ?? '' }}" step="0.01">
                        </div>
                    </div>

                    <hr style="margin: 40px 0; border: 0; border-top: 1px solid #eee;">

                    <!-- Promotions & Discounts -->
                    <div class="mb-5">
                        <h3 class="fw-bold h5 mb-2">Promotions & Discounts</h3>
                        <p class="text-muted small mb-4">Set up early bird, last minute, or custom stay discounts.</p>
                        
                        <div id="discounts_wrapper"></div>
                    </div>
                </div>

                <!-- Step 6: Rules & Policy -->
                <div class="step-section" id="step-6">
                    <h1>Rules & Cancellation Policy</h1>
                    <p class="step-desc">Establish standard rules and cancellation penalty terms for this listing.</p>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Booking Type</label>
                            <select name="booking_type" class="form-select form-control-airbnb">
                                <option value="Instant Booking" {{ (isset($room) && $room->booking_type == 'Instant Booking') ? 'selected' : '' }}>Instant Booking</option>
                                <option value="Request to Book" {{ (isset($room) && $room->booking_type == 'Request to Book') ? 'selected' : '' }}>Request to book</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-4" id="wrap_admin_cancellation_policy">
                            <label class="form-label fw-bold">Cancellation Policy</label>
                            <select name="cancellation_policy" class="form-select form-control-airbnb">
                                <option value="Flexible" {{ (isset($room) && $room->cancellation_policy == 'Flexible') ? 'selected' : '' }}>Flexible</option>
                                <option value="Moderate" {{ (isset($room) && $room->cancellation_policy == 'Moderate') ? 'selected' : '' }}>Moderate</option>
                                <option value="Strict" {{ (isset($room) && $room->cancellation_policy == 'Strict') ? 'selected' : '' }}>Strict</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-4" id="wrap_admin_checkout_policy">
                            <label class="form-label fw-bold">Checkout Policy (Checkout Time)</label>
                            <select name="checkout_policy" class="form-select form-control-airbnb">
                                @php
                                    $times = ['08:00 AM', '09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM', '06:00 PM', '07:00 PM', '08:00 PM'];
                                    $currentCheckout = isset($room) ? ($room->checkout_policy ?: '11:00 AM') : '11:00 AM';
                                @endphp
                                @foreach($times as $time)
                                    <option value="{{ $time }}" {{ $currentCheckout === $time ? 'selected' : '' }}>{{ $time }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-4">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="custom_cancellation" id="customCancellationToggle" value="1" {{ (isset($room) && $room->custom_cancellation) ? 'checked' : '' }} onchange="toggleAdminCustomCancellation(this.checked)">
                                <label class="form-check-label fw-bold" for="customCancellationToggle">Custom Cancellation Policy?</label>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="adminCustomCancelFields" style="{{ (isset($room) && $room->custom_cancellation) ? '' : 'display:none;' }}">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Free Cancellation Days</label>
                            <input type="number" name="free_cancellation_days" id="adminFreeCancelDays" class="form-control form-control-airbnb" value="{{ $room->free_cancellation_days ?? 0 }}" oninput="generateAdminCancellationMessage()">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Cancellation Fee (%)</label>
                            <input type="number" name="cancellation_fee" id="adminCancelFee" class="form-control form-control-airbnb" value="{{ $room->cancellation_fee ?? 0 }}" oninput="generateAdminCancellationMessage()">
                        </div>

                        <div class="col-12 mb-4">
                            <div class="p-3 bg-light rounded-3 border-start border-primary border-3">
                                <span class="text-primary text-uppercase fw-bold d-block small mb-1">Dynamic Cancellation Preview</span>
                                <p id="adminCancelMessagePreview" class="m-0 fw-semibold text-dark small">Guests receive a 100% free cancellation until 0 days before check-in.</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">Official Guest Rules</h5>
                        <div class="row">
                            @php
                                $selectedRules = isset($room) && is_array($room->selected_rules) ? $room->selected_rules : [];
                            @endphp
                            @foreach($roomRules as $index => $rule)
                                <div class="col-md-6 mb-3 {{ $index >= 4 ? 'admin-rule-card-hidden' : '' }}" style="{{ $index >= 4 ? 'display: none;' : '' }}">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="selected_rules[]" value="{{ $rule->id }}" id="rule_{{ $rule->id }}" {{ in_array($rule->id, $selectedRules) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="rule_{{ $rule->id }}" style="cursor: pointer;">
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                @if($rule->icon)
                                                    <i class="{{ $rule->icon }}" style="color: var(--primary-color, #2563eb); font-size: 13px;"></i>
                                                @endif
                                                <strong>{{ $rule->rule_name }}</strong>
                                            </div>
                                            <span class="d-block text-muted small" style="margin-left: {{ $rule->icon ? '21px' : '0' }};">{{ $rule->rule_text }}</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Show More rules toggle -->
                        <div class="col-12 text-center mt-3">
                            <button type="button" id="btn_toggle_admin_rules" onclick="toggleAdminRules()" class="btn btn-outline-primary rounded-pill px-4" style="border: 1.5px solid var(--primary-color); color: var(--primary-color); background: transparent; font-weight: 700; font-size: 13px;">
                                Show More <i class="fas fa-chevron-down ms-1" style="font-size: 11px;"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 7: Review -->
                <div class="step-section" id="step-7">
                    <h1>Final Review & Assignment</h1>
                    <p class="step-desc">Assign this room to a host and set its initial status.</p>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Host (User)</label>
                        <select name="user_id" class="form-select form-control-airbnb">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (isset($room) && $room->user_id == $user->id) ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" id="statusSelect" class="form-select form-control-airbnb" onchange="toggleResubmitField()">
                            <option value="pending" {{ (isset($room) && $room->status == 'pending') ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ (isset($room) && $room->status == 'approved') ? 'selected' : '' }}>Approved</option>
                            <option value="resubmit" {{ (isset($room) && $room->status == 'resubmit') ? 'selected' : '' }}>Resubmit</option>
                        </select>
                    </div>

                    <div id="resubmitReasonContainer" class="mb-4" style="{{ (isset($room) && $room->status == 'resubmit') ? '' : 'display:none;' }}">
                        <label class="form-label fw-bold">Resubmit Reason</label>
                        <textarea name="resubmit_reason" class="form-control form-control-airbnb" rows="4" placeholder="Explain what needs to be fixed...">{{ $room->resubmit_reason_text ?? '' }}</textarea>
                    </div>
                </div>
            </form>

            <div class="form-footer">
                <button type="button" class="btn-back" id="backBtn" onclick="prevStep()" style="visibility: hidden;">Back</button>
                <button type="button" class="btn-next" id="nextBtn" onclick="nextStep()">Next</button>
            </div>
        </div>
    </div>
</div>

<!-- Food Menu Setup Modal -->
<div class="modal-overlay" id="menuModal">
    <div class="modal-card animate__animated animate__zoomIn animate__faster">
        <div class="modal-header">
            <h5 class="fw-bold m-0" id="modalTitle">Setup Menu</h5>
            <button type="button" class="btn-close border-0 bg-transparent" onclick="closeMenuModal()" style="font-size: 20px; cursor: pointer;">&times;</button>
        </div>
        <div class="modal-body">
            <p class="text-muted small mb-4">Add menu items and their pricing for this service.</p>
            <div id="modalItemsContainer"></div>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 mt-2" onclick="addModalItemRow()">
                <i class="fas fa-plus me-1"></i> Add Item
            </button>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light rounded-pill px-4" onclick="closeMenuModal()">Cancel</button>
            <button type="button" class="btn btn-primary rounded-pill px-4" onclick="saveModalMenu()" style="background-color: var(--primary-color); border-color: var(--primary-color);">Save Menu</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    let currentStep = 1;
    const totalSteps = 7;

    function toggleAdminCustomCancellation(isChecked) {
        const customFields = document.getElementById('adminCustomCancelFields');
        const standardPolicy = document.getElementById('wrap_admin_cancellation_policy');
        if (customFields) customFields.style.display = isChecked ? 'flex' : 'none';
        if (standardPolicy) standardPolicy.style.display = isChecked ? 'none' : 'block';
        if (isChecked) {
            generateAdminCancellationMessage();
        }
    }

    function generateAdminCancellationMessage() {
        const days = parseInt(document.getElementById('adminFreeCancelDays').value) || 0;
        const fee = parseInt(document.getElementById('adminCancelFee').value) || 0;

        let message = `Guests receive a 100% free cancellation until ${days} days before check-in. `;
        if (fee > 0) {
            message += `Afterwards, a fee of ${fee}% on the base price is applied for cancellation.`;
        } else {
            message += `No penalty fee applies for late cancellation.`;
        }

        const previewEl = document.getElementById('adminCancelMessagePreview');
        if (previewEl) previewEl.innerText = message;
    }

    // Admin Rules expand/collapse
    let adminRulesExpanded = false;
    function toggleAdminRules() {
        const hiddenCards = document.querySelectorAll('.admin-rule-card-hidden');
        const btn = document.getElementById('btn_toggle_admin_rules');
        adminRulesExpanded = !adminRulesExpanded;

        hiddenCards.forEach(card => {
            card.style.display = adminRulesExpanded ? 'block' : 'none';
        });

        if (adminRulesExpanded) {
            btn.innerHTML = 'Show Less <i class="fas fa-chevron-up ms-1" style="font-size: 11px;"></i>';
        } else {
            btn.innerHTML = 'Show More <i class="fas fa-chevron-down ms-1" style="font-size: 11px;"></i>';
        }
    }

    // Call cancellation message preview on document load
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('adminFreeCancelDays')) {
            generateAdminCancellationMessage();
        }

        // Toggle custom cancellation initially
        const customToggle = document.getElementById('customCancellationToggle');
        if (customToggle) {
            toggleAdminCustomCancellation(customToggle.checked);
        }
    });

    function goToStep(step) {
        document.querySelectorAll('.step-section').forEach(s => s.classList.remove('active'));
        document.getElementById('step-' + step).classList.add('active');

        document.querySelectorAll('.nav-item-step').forEach(n => {
            n.classList.remove('active', 'completed');
        });

        for (let i = 1; i < step; i++) {
            document.getElementById('nav-step-' + i).classList.add('completed');
        }
        document.getElementById('nav-step-' + step).classList.add('active');

        currentStep = step;

        document.getElementById('backBtn').style.visibility = currentStep === 1 ? 'hidden' : 'visible';
        document.getElementById('nextBtn').textContent = currentStep === totalSteps ? 'Save Room' : 'Next';
    }

    function nextStep() {
        if (currentStep < totalSteps) {
            goToStep(currentStep + 1);
        } else {
            submitForm();
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            goToStep(currentStep - 1);
        }
    }

    function toggleAmenity(el, id) {
        el.classList.toggle('active');
        const checkbox = el.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
    }

    function toggleResubmitField() {
        const status = document.getElementById('statusSelect').value;
        const container = document.getElementById('resubmitReasonContainer');
        container.style.display = status === 'resubmit' ? 'block' : 'none';
    }

    function showToast(message, type = 'success') {
        const container = document.getElementById('adminToastContainer');
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : 'success'} shadow-sm border-0 animate__animated animate__fadeInDown`;
        toast.style.marginBottom = '10px';
        toast.style.borderRadius = '12px';
        toast.style.display = 'flex';
        toast.style.alignItems = 'center';
        toast.style.gap = '10px';
        toast.style.padding = '12px 20px';
        
        const icon = type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
        toast.innerHTML = `<i class="${icon}"></i> <span>${message}</span>`;
        
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.replace('animate__fadeInDown', 'animate__fadeOutUp');
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    }

    function submitForm() {
        // Sync dynamic states prior to compilation
        syncBedroomAllocations();
        document.getElementById('enhancementsInput').value = JSON.stringify(enhancementsState);
        document.getElementById('discountsInput').value = JSON.stringify(discountsState);

        const form = document.getElementById('multiStepForm');
        const formData = new FormData(form);
        const roomId = form.querySelector('input[name="room_id"]').value;
        const url = roomId ? '{{ url("admin/rooms") }}/' + roomId : '{{ route("admin.rooms.store") }}';

        if (roomId) formData.append('_method', 'PUT');

        const btn = document.getElementById('nextBtn');
        const originalText = btn.textContent;
        btn.textContent = 'Saving...';
        btn.disabled = true;

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                if (response.status === 422) {
                    const errors = Object.values(data.errors).flat().join('<br>');
                    showToast(errors, 'error');
                } else {
                    showToast(data.message || 'Something went wrong', 'error');
                }
                throw new Error('Validation failed');
            }
            return data;
        })
        .then(res => {
            if (res.success) {
                showToast(res.message);
                setTimeout(() => window.location.href = res.redirect, 1000);
            } else {
                showToast(res.message || 'Error saving room', 'error');
                btn.textContent = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            if (err.message !== 'Validation failed') {
                showToast('Something went wrong. Please try again.', 'error');
            }
            btn.textContent = originalText;
            btn.disabled = false;
        });
    }

    function deletePhoto(id, btn) {
        if (!confirm('Are you sure you want to delete this photo?')) return;
        
        fetch('{{ url("admin/rooms/photo") }}/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                showToast('Photo deleted successfully');
                btn.closest('.media-item').remove();
            } else {
                showToast('Error deleting photo', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Something went wrong', 'error');
        });
    }

    document.getElementById('photoInput').addEventListener('change', function(e) {
        const preview = document.getElementById('photoPreview');
        preview.innerHTML = '';
        Array.from(e.target.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = ev => {
                const div = document.createElement('div');
                div.className = 'media-item';
                div.innerHTML = `
                    <img src="${ev.target.result}" alt="Preview">
                    <div class="media-content">
                        <input type="text" name="photo_captions[]" class="form-control form-control-sm border-0 bg-light" placeholder="Add a caption..." style="font-size: 12px; border-radius: 8px;">
                    </div>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    // --- Sleeping Arrangements Scripting ---
    const allBedTypes = @json($roomBeds ?? []);
    let dbAllocations = @json(isset($room) ? $room->bedroomBeds : []);
    
    let allocations = {};
    dbAllocations.forEach(alloc => {
        if (!allocations[alloc.bedroom_index]) {
            allocations[alloc.bedroom_index] = {};
        }
        allocations[alloc.bedroom_index][alloc.room_bed_id] = alloc.count;
    });

    let bedroomsCount = {{ isset($room) ? ($room->bedrooms_count ?: 1) : 1 }};

    function adjustBedrooms(change) {
        let newCount = bedroomsCount + change;
        if (newCount < 1) newCount = 1;
        if (newCount === bedroomsCount) return;
        bedroomsCount = newCount;
        document.getElementById('bedrooms_count_display').innerText = bedroomsCount;
        document.getElementById('bedroomsCountInput').value = bedroomsCount;
        renderBedrooms();
        syncBedroomAllocations();
    }

    function toggleEditBedPanel(idx) {
        const panel = document.getElementById('edit_beds_panel_' + idx);
        const btn = document.getElementById('btn_edit_beds_' + idx);
        const span = btn.querySelector('span');
        
        if (panel.style.display === 'none' || !panel.style.display) {
            panel.style.display = 'flex';
            span.innerText = 'Done';
        } else {
            panel.style.display = 'none';
            
            // Check if there are active beds
            let totalBeds = 0;
            const bedCounts = allocations[idx] || {};
            Object.values(bedCounts).forEach(c => totalBeds += c);
            span.innerText = totalBeds > 0 ? 'Edit Beds' : 'Add Beds';
        }
    }

    function addBedToBedroom(bedroomIdx, selectEl) {
        const bedId = selectEl.value;
        if (!bedId) return;

        if (!allocations[bedroomIdx]) {
            allocations[bedroomIdx] = {};
        }
        allocations[bedroomIdx][bedId] = 1; // start with 1
        
        selectEl.value = '';
        renderBedrooms();
        syncBedroomAllocations();
        
        // Keep edit panel open
        document.getElementById('edit_beds_panel_' + bedroomIdx).style.display = 'flex';
        document.getElementById('btn_edit_beds_' + bedroomIdx).querySelector('span').innerText = 'Done';
    }

    function updateBedCount(bedroomIdx, bedTypeId, change) {
        if (!allocations[bedroomIdx]) {
            allocations[bedroomIdx] = {};
        }
        
        let current = allocations[bedroomIdx][bedTypeId] || 0;
        let newCount = current + change;
        if (newCount < 0) newCount = 0;
        
        allocations[bedroomIdx][bedTypeId] = newCount;
        
        renderBedrooms();
        syncBedroomAllocations();
        
        // Keep edit panel open
        document.getElementById('edit_beds_panel_' + bedroomIdx).style.display = 'flex';
        document.getElementById('btn_edit_beds_' + bedroomIdx).querySelector('span').innerText = 'Done';
    }

    function syncBedroomAllocations() {
        const list = [];
        Object.keys(allocations).forEach(bedroomIdx => {
            if (parseInt(bedroomIdx) > bedroomsCount) return; // ignore deleted bedrooms
            const beds = allocations[bedroomIdx];
            Object.keys(beds).forEach(bedId => {
                const count = beds[bedId];
                if (count > 0) {
                    list.push({
                        bedroom_index: parseInt(bedroomIdx),
                        room_bed_id: parseInt(bedId),
                        count: count
                    });
                }
            });
        });
        document.getElementById('bedroomAllocationsInput').value = JSON.stringify(list);
    }

    function renderBedrooms() {
        const container = document.getElementById('bedrooms_container');
        if (!container) return;
        container.innerHTML = '';

        for (let i = 1; i <= bedroomsCount; i++) {
            const bedCounts = allocations[i] || {};
            let totalBeds = 0;
            let summaryParts = [];

            Object.keys(bedCounts).forEach(bedId => {
                const count = bedCounts[bedId];
                if (count > 0) {
                    totalBeds += count;
                    const bedType = allBedTypes.find(b => b.id == bedId);
                    if (bedType) {
                        summaryParts.push(`${count} ${bedType.name}`);
                    }
                }
            });

            const summaryText = summaryParts.length > 0
                ? summaryParts.join(', ')
                : 'No beds added yet';

            const editButtonText = totalBeds > 0 ? 'Edit Beds' : 'Add Beds';

            let selectOptionsHtml = '<option value="" disabled selected>Select another type of bed...</option>';
            let hasOptions = false;
            allBedTypes.forEach(bedType => {
                if (!bedCounts[bedType.id] || bedCounts[bedType.id] === 0) {
                    selectOptionsHtml += `<option value="${bedType.id}">${bedType.name}</option>`;
                    hasOptions = true;
                }
            });

            let bedRowsHtml = '';
            allBedTypes.forEach(bedType => {
                const count = bedCounts[bedType.id] || 0;
                if (count > 0) {
                    const iconPath = bedType.image ? `/storage/${bedType.image}` : '';
                    const iconHtml = iconPath
                        ? `<img src="${iconPath}" class="bed-icon-img" alt="${bedType.name}">`
                        : `<i class="fas fa-bed text-muted" style="font-size:18px;"></i>`;

                    bedRowsHtml += `
                        <div class="bed-counter-row">
                            <div class="bed-info-left">
                                ${iconHtml}
                                <span class="bed-name-text">${bedType.name}</span>
                            </div>
                            <div class="bed-count-actions">
                                <button type="button" class="btn-counter-bed" onclick="updateBedCount(${i}, ${bedType.id}, -1)">−</button>
                                <span class="bed-count-value">${count}</span>
                                <button type="button" class="btn-counter-bed" onclick="updateBedCount(${i}, ${bedType.id}, 1)">+</button>
                            </div>
                        </div>
                    `;
                }
            });

            const bedroomHtml = `
                <div class="enhancement-row-card" id="bedroom_row_${i}" style="margin-bottom: 20px;">
                    <div class="bedroom-header-row" style="display: flex; justify-content: space-between; align-items: center; gap: 12px; width: 100%;">
                        <div>
                            <h4 class="bedroom-title-bold" style="font-size: 1.05rem; font-weight: 700; color: #222; margin: 0 0 4px 0;">Bedroom ${i}</h4>
                            <p class="bedroom-summary-text" id="bedroom_summary_text_${i}" style="margin: 0; font-size: 13px; color: #666;">
                                <strong class="text-primary">${totalBeds} Bed${totalBeds === 1 ? '' : 's'}</strong> &nbsp;•&nbsp; 
                                <span>${summaryText}</span>
                            </p>
                        </div>
                        <button type="button" class="btn-edit-beds" id="btn_edit_beds_${i}" onclick="toggleEditBedPanel(${i})" style="display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fas fa-edit" style="font-size: 11px;"></i>
                            <span>${editButtonText}</span>
                        </button>
                    </div>

                    <div class="edit-beds-panel" id="edit_beds_panel_${i}" style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 12px; padding: 16px; margin-top: 15px; display: none; flex-direction: column; gap: 12px;">
                        <div class="active-bed-rows-container">
                            ${bedRowsHtml || '<p class="text-muted small text-center my-3">No bed types selected. Choose one below to start.</p>'}
                        </div>

                        ${hasOptions ? `
                            <div class="dropdown-add-bed-wrap">
                                <select class="select-add-bed" onchange="addBedToBedroom(${i}, this)">
                                    ${selectOptionsHtml}
                                </select>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', bedroomHtml);
        }
    }

    // --- Food & Services Enhancements Scripting ---
    let enhancementsState = @json(isset($room) ? $room->enhancements : []);
    let currentModalType = '';

    function renderEnhancements() {
        const container = document.getElementById('enhancementsContainer');
        if (!container) return;
        container.innerHTML = '';

        const types = ['Breakfast', 'Lunch', 'Dinner'];
        types.forEach(type => {
            const items = enhancementsState.filter(e => e.type.toLowerCase() === type.toLowerCase());
            
            // Check if per guest is active for this service type
            const isPerGuest = items.some(item => item.is_per_guest === true || item.is_per_guest === 1 || item.is_per_guest === '1');
            const perGuestItem = isPerGuest ? items.find(item => item.is_per_guest === true || item.is_per_guest === 1 || item.is_per_guest === '1') : null;
            const perGuestPrice = perGuestItem ? parseFloat(perGuestItem.price) : '';

            let itemsHtml = '';
            if (isPerGuest) {
                itemsHtml = `
                    <div class="enhancement-per-guest-banner" style="display: flex; align-items: center; gap: 12px; background: rgba(16, 185, 129, 0.08); border: 1.5px solid rgba(16, 185, 129, 0.2); border-radius: 12px; padding: 12px 16px; margin-top: 4px; width: 100%;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 14px; flex-shrink: 0;">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <div>
                            <div style="font-size: 10px; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Price per guest</div>
                            <div style="font-family: 'Outfit', sans-serif; font-size: 14px; font-weight: 800; color: #10b981; margin-top: 1px;">
                                $${perGuestPrice ? parseFloat(perGuestPrice).toFixed(2) : '0.00'}/guest
                            </div>
                        </div>
                    </div>
                `;
            } else if (items.length > 0) {
                items.forEach(item => {
                    itemsHtml += `
                        <div class="food-item-card">
                            <span>${item.item_name}</span>
                            <strong class="text-primary">$${parseFloat(item.price).toFixed(2)}</strong>
                        </div>
                    `;
                });
            } else {
                itemsHtml = `<p class="text-muted small m-0 py-2">No menu setup yet.</p>`;
            }

            const cardHtml = `
                <div class="enhancement-row-card" style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                        <h4 class="fw-bold m-0" style="font-size: 15px; color: #222;">${type} Service</h4>
                        
                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <!-- Price per guest toggle -->
                            <label class="price-per-guest-toggle">
                                <input type="checkbox" class="is-per-guest-checkbox-admin" id="per_guest_chk_admin_${type}" data-type="${type}" ${isPerGuest ? 'checked' : ''} onchange="toggleAdminPricePerGuest('${type}', this)">
                                <span>Price per guest</span>
                            </label>

                            <!-- Price Input (visible when per guest checked) -->
                            <div id="per_guest_input_wrap_admin_${type}" style="display: ${isPerGuest ? 'flex' : 'none'}; align-items: center; gap: 6px;">
                                <div style="position: relative; width: 100px;">
                                    <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 12px; font-weight: 600; color: #10b981;">$</span>
                                    <input type="number" class="form-control form-control-sm" id="per_guest_price_admin_${type}" value="${perGuestPrice}" placeholder="Price" min="0" onblur="saveAdminPricePerGuest('${type}')" style="height: 32px; padding-left: 20px; font-size: 12px; font-weight: 600; border-radius: 6px; border: 1px solid rgba(16, 185, 129, 0.3);">
                                </div>
                            </div>

                            <!-- Setup Menu button (visible when per guest unchecked) -->
                            <button type="button" class="btn-menu-setup" id="btn_menu_setup_admin_${type}" onclick="openMenuModal('${type}')" style="display: ${isPerGuest ? 'none' : 'inline-flex'}; font-size: 12px; padding: 4px 12px;">
                                <i class="fas ${ (items.length > 0 && !isPerGuest) ? 'fa-edit' : 'fa-plus' } me-1" style="font-size: 10px;"></i>
                                <span>${ (items.length > 0 && !isPerGuest) ? 'Edit Menu' : 'Setup Menu' }</span>
                            </button>
                        </div>
                    </div>
                    <div class="food-items-grid">
                        ${itemsHtml}
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', cardHtml);
        });
        
        // Sync to hidden input
        document.getElementById('enhancementsInput').value = JSON.stringify(enhancementsState);
    }

    function toggleAdminPricePerGuest(type, checkbox) {
        const inputWrap = document.getElementById(`per_guest_input_wrap_admin_${type}`);
        const setupBtn = document.getElementById(`btn_menu_setup_admin_${type}`);
        
        if (checkbox.checked) {
            inputWrap.style.display = 'flex';
            setupBtn.style.display = 'none';
            
            // Delete existing menu items of this type
            enhancementsState = enhancementsState.filter(e => e.type.toLowerCase() !== type.toLowerCase());
            // Seed a per guest item
            enhancementsState.push({
                type: type.toLowerCase(),
                item_name: type + " Service",
                price: 0,
                is_per_guest: true
            });
            renderEnhancements();
            // Focus on the input field
            setTimeout(() => {
                const inp = document.getElementById(`per_guest_price_admin_${type}`);
                if (inp) inp.focus();
            }, 50);
        } else {
            inputWrap.style.display = 'none';
            setupBtn.style.display = 'inline-flex';
            
            // Remove the per guest item
            enhancementsState = enhancementsState.filter(e => e.type.toLowerCase() !== type.toLowerCase());
            renderEnhancements();
        }
    }

    function saveAdminPricePerGuest(type) {
        const priceInput = document.getElementById(`per_guest_price_admin_${type}`);
        if (!priceInput) return;
        const val = parseFloat(priceInput.value) || 0;
        
        // Find existing per guest item and update price
        const item = enhancementsState.find(e => e.type.toLowerCase() === type.toLowerCase() && e.is_per_guest);
        if (item) {
            item.price = val;
            // Trigger a re-render to update the display
            renderEnhancements();
        }
    }

    function openMenuModal(type) {
        currentModalType = type;
        document.getElementById('modalTitle').innerText = `${type} Menu Setup`;
        
        const container = document.getElementById('modalItemsContainer');
        container.innerHTML = '';

        const items = enhancementsState.filter(e => e.type.toLowerCase() === type.toLowerCase());
        if (items.length === 0) {
            addModalItemRow();
        } else {
            items.forEach(item => {
                addModalItemRow(item.item_name, item.price);
            });
        }

        document.getElementById('menuModal').style.display = 'flex';
    }

    function closeMenuModal() {
        document.getElementById('menuModal').style.display = 'none';
    }

    function addModalItemRow(name = '', price = '') {
        const container = document.getElementById('modalItemsContainer');
        const rowId = 'row_' + Date.now() + Math.random().toString(36).substr(2, 5);
        const html = `
            <div class="dynamic-item-row" id="${rowId}">
                <div style="flex: 1;">
                    <input type="text" class="form-control form-control-airbnb modal-input-name" value="${name}" placeholder="Item Name" required>
                </div>
                <div style="width: 120px;">
                    <input type="number" step="0.01" class="form-control form-control-airbnb modal-input-price" value="${price}" placeholder="Price" required>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="document.getElementById('${rowId}').remove()" style="height: 46px; width: 46px; border-radius: 8px;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function saveModalMenu() {
        const rows = document.querySelectorAll('.dynamic-item-row');
        const items = [];
        
        // Keep other types intact
        enhancementsState = enhancementsState.filter(e => e.type.toLowerCase() !== currentModalType.toLowerCase());

        rows.forEach(row => {
            const name = row.querySelector('.modal-input-name').value.trim();
            const price = parseFloat(row.querySelector('.modal-input-price').value) || 0;
            if (name) {
                enhancementsState.push({
                    type: currentModalType.toLowerCase(),
                    item_name: name,
                    price: price
                });
            }
        });

        closeMenuModal();
        renderEnhancements();
    }

    // --- Promotions & Discounts Scripting ---
    let discountsState = @json(isset($room->roomPrice) ? ($room->roomPrice->discounts ?? []) : []);
    
    // Normalize if array (could be parsed from DB json cast)
    if (!discountsState || Array.isArray(discountsState) && discountsState.length === 0) {
        discountsState = {
            last_minute: { active: false, percentage: 10, nights: 14 },
            early_bird: { active: false, rules: [] },
            length_of_stay: { active: false, rules: [] },
            custom: { active: false, rules: [] }
        };
    }

    function renderDiscounts() {
        const container = document.getElementById('discounts_wrapper');
        if (!container) return;
        container.innerHTML = '';

        // 1. Last Minute
        const lm = discountsState.last_minute || { active: false, percentage: 10, nights: 14 };
        const lmHtml = `
            <div class="discount-card">
                <div class="discount-header" onclick="toggleDiscountType('last_minute')">
                    <input type="checkbox" class="discount-checkbox" ${lm.active ? 'checked' : ''} onclick="event.stopPropagation(); toggleDiscountType('last_minute')">
                    <label class="discount-label">Last-Minute Discounts</label>
                </div>
                ${lm.active ? `
                    <div class="discount-content">
                        <div class="discount-row">
                            <div class="discount-input-group" style="width: 140px;">
                                <span class="discount-input-prefix">%</span>
                                <input type="number" class="discount-input-field" value="${lm.percentage}" onchange="updateLMField('percentage', this.value)" style="width: 100%;">
                            </div>
                            <span class="fw-bold small text-muted">discount for bookings made within</span>
                            <div class="discount-input-group" style="width: 140px;">
                                <input type="number" class="discount-input-field" value="${lm.nights}" onchange="updateLMField('nights', this.value)" style="width: 100%;">
                                <span class="discount-input-prefix" style="border-left: 1.5px solid #ddd; border-right: none;">days</span>
                            </div>
                            <span class="fw-bold small text-muted">of check-in</span>
                        </div>
                    </div>
                ` : ''}
            </div>
        `;

        // 2. Early Bird
        const eb = discountsState.early_bird || { active: false, rules: [] };
        let ebRulesHtml = '';
        if (eb.active) {
            const ebRules = eb.rules || [];
            ebRules.forEach((rule, idx) => {
                ebRulesHtml += `
                    <div class="discount-row">
                        <div class="discount-input-group" style="width: 140px;">
                            <span class="discount-input-prefix">%</span>
                            <input type="number" class="discount-input-field" value="${rule.percentage}" onchange="updateEBRule(${idx}, 'percentage', this.value)" style="width: 100%;">
                        </div>
                        <span class="fw-bold small text-muted">discount for bookings made</span>
                        <div class="discount-input-group" style="width: 140px;">
                            <input type="number" class="discount-input-field" value="${rule.days_ahead}" onchange="updateEBRule(${idx}, 'days_ahead', this.value)" style="width: 100%;">
                            <span class="discount-input-prefix" style="border-left: 1.5px solid #ddd; border-right: none;">days</span>
                        </div>
                        <span class="fw-bold small text-muted">or more before check-in</span>
                        <button type="button" class="btn-remove-discount" onclick="removeEBRule(${idx})"><i class="fas fa-trash"></i></button>
                    </div>
                `;
            });
        }
        const ebHtml = `
            <div class="discount-card">
                <div class="discount-header" onclick="toggleDiscountType('early_bird')">
                    <input type="checkbox" class="discount-checkbox" ${eb.active ? 'checked' : ''} onclick="event.stopPropagation(); toggleDiscountType('early_bird')">
                    <label class="discount-label">Early-Bird Discounts</label>
                </div>
                ${eb.active ? `
                    <div class="discount-content">
                        ${ebRulesHtml}
                        <button type="button" class="btn-add-discount px-3 py-2 rounded-pill border-primary mt-2" onclick="addEBRule()">
                            <i class="fas fa-plus me-1" style="font-size:11px;"></i> Add Rule
                        </button>
                    </div>
                ` : ''}
            </div>
        `;

        // 3. Length of Stay
        const los = discountsState.length_of_stay || { active: false, rules: [] };
        let losRulesHtml = '';
        if (los.active) {
            const losRules = los.rules || [];
            losRules.forEach((rule, idx) => {
                losRulesHtml += `
                    <div class="discount-row">
                        <div class="discount-input-group" style="width: 140px;">
                            <span class="discount-input-prefix">%</span>
                            <input type="number" class="discount-input-field" value="${rule.percentage}" onchange="updateLOSRule(${idx}, 'percentage', this.value)" style="width: 100%;">
                        </div>
                        <span class="fw-bold small text-muted">discount for stays of</span>
                        <select class="discount-select" onchange="updateLOSRule(${idx}, 'nights', this.value)">
                            <option value="7" ${rule.nights == 7 ? 'selected' : ''}>Weekly (7+ nights)</option>
                            <option value="28" ${rule.nights == 28 ? 'selected' : ''}>Monthly (28+ nights)</option>
                            <option value="3" ${rule.nights == 3 ? 'selected' : ''}>3 Nights</option>
                            <option value="4" ${rule.nights == 4 ? 'selected' : ''}>4 Nights</option>
                            <option value="5" ${rule.nights == 5 ? 'selected' : ''}>5 Nights</option>
                            <option value="6" ${rule.nights == 6 ? 'selected' : ''}>6 Nights</option>
                            <option value="14" ${rule.nights == 14 ? 'selected' : ''}>2 Weeks (14+ nights)</option>
                        </select>
                        <button type="button" class="btn-remove-discount" onclick="removeLOSRule(${idx})"><i class="fas fa-trash"></i></button>
                    </div>
                `;
            });
        }
        const losHtml = `
            <div class="discount-card">
                <div class="discount-header" onclick="toggleDiscountType('length_of_stay')">
                    <input type="checkbox" class="discount-checkbox" ${los.active ? 'checked' : ''} onclick="event.stopPropagation(); toggleDiscountType('length_of_stay')">
                    <label class="discount-label">Length-of-Stay Discounts</label>
                </div>
                ${los.active ? `
                    <div class="discount-content">
                        ${losRulesHtml}
                        <button type="button" class="btn-add-discount px-3 py-2 rounded-pill border-primary mt-2" onclick="addLOSRule()">
                            <i class="fas fa-plus me-1" style="font-size:11px;"></i> Add Stay Option
                        </button>
                    </div>
                ` : ''}
            </div>
        `;

        // 4. Custom
        const cust = discountsState.custom || { active: false, rules: [] };
        let custRulesHtml = '';
        if (cust.active) {
            const custRules = cust.rules || [];
            custRules.forEach((rule, idx) => {
                custRulesHtml += `
                    <div class="discount-row">
                        <div class="discount-input-group" style="width: 140px;">
                            <span class="discount-input-prefix">%</span>
                            <input type="number" class="discount-input-field" value="${rule.percentage}" onchange="updateCustRule(${idx}, 'percentage', this.value)" style="width: 100%;">
                        </div>
                        <span class="fw-bold small text-muted">discount from</span>
                        <input type="text" class="form-control form-control-airbnb discount-datepicker start-date-picker" data-type="start" data-idx="${idx}" value="${rule.start_date || ''}" placeholder="Start Date" onchange="updateCustRule(${idx}, 'start_date', this.value)" style="width: 140px; height: 46px;">
                        <span class="fw-bold small text-muted">to</span>
                        <input type="text" class="form-control form-control-airbnb discount-datepicker end-date-picker" data-type="end" data-idx="${idx}" value="${rule.end_date || ''}" placeholder="End Date" onchange="updateCustRule(${idx}, 'end_date', this.value)" style="width: 140px; height: 46px;">
                        <button type="button" class="btn-remove-discount" onclick="removeCustRule(${idx})"><i class="fas fa-trash"></i></button>
                    </div>
                `;
            });
        }
        const custHtml = `
            <div class="discount-card">
                <div class="discount-header" onclick="toggleDiscountType('custom')">
                    <input type="checkbox" class="discount-checkbox" ${cust.active ? 'checked' : ''} onclick="event.stopPropagation(); toggleDiscountType('custom')">
                    <label class="discount-label">Custom Stay Period Discount</label>
                </div>
                ${cust.active ? `
                    <div class="discount-content">
                        ${custRulesHtml}
                        <button type="button" class="btn-add-discount px-3 py-2 rounded-pill border-primary mt-2" onclick="addCustRule()">
                            <i class="fas fa-plus me-1" style="font-size:11px;"></i> Add Date Range
                        </button>
                    </div>
                ` : ''}
            </div>
        `;

        container.innerHTML = lmHtml + ebHtml + losHtml + custHtml;

        // Initialize flatpickr on date fields
        initFlatpickrOnCustomFields();

        // Sync to hidden input
        document.getElementById('discountsInput').value = JSON.stringify(discountsState);
    }

    window.flatpickrInstances = window.flatpickrInstances || {};

    function initFlatpickrOnCustomFields() {
        document.querySelectorAll('.discount-datepicker').forEach(el => {
            const type = el.dataset.type; // 'start' or 'end'
            const idx = parseInt(el.dataset.idx);
            const key = `custom_${type}_${idx}`;

            // Set dynamic minDate for end picker based on selected start date
            let minDateVal = "today";
            if (type === 'end') {
                const rule = discountsState.custom.rules[idx];
                if (rule && rule.start_date) {
                    minDateVal = rule.start_date;
                }
            }

            const fpInstance = flatpickr(el, {
                minDate: minDateVal,
                dateFormat: 'Y-m-d',
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    if (dayElem.dateObj.getDay() === 0) {
                        dayElem.classList.add('is-sunday');
                    }
                    // Highlight the start date in the end date picker!
                    if (type === 'end') {
                        const rule = discountsState.custom.rules[idx];
                        if (rule && rule.start_date) {
                            const startD = new Date(rule.start_date + "T00:00:00");
                            if (dayElem.dateObj.getFullYear() === startD.getFullYear() &&
                                dayElem.dateObj.getMonth() === startD.getMonth() &&
                                dayElem.dateObj.getDate() === startD.getDate()) {
                                dayElem.classList.add("start-date-highlight");
                            }
                        }
                    }
                },
                onChange: function(selectedDates, dateStr) {
                    updateCustRule(idx, type === 'start' ? 'start_date' : 'end_date', dateStr);

                    if (type === 'start') {
                        const endPickerKey = `custom_end_${idx}`;
                        if (window.flatpickrInstances[endPickerKey]) {
                            window.flatpickrInstances[endPickerKey].set('minDate', dateStr);
                            
                            // If end date is now before the start date, clear it
                            const rule = discountsState.custom.rules[idx];
                            if (rule && rule.end_date && new Date(rule.end_date) < new Date(dateStr)) {
                                rule.end_date = '';
                                window.flatpickrInstances[endPickerKey].clear();
                                updateCustRule(idx, 'end_date', '');
                            }
                        }
                    }
                }
            });

            window.flatpickrInstances[key] = fpInstance;
        });
    }

    function toggleDiscountType(type) {
        discountsState[type].active = !discountsState[type].active;
        if (discountsState[type].active) {
            // Seed defaults if empty
            if (type === 'early_bird' && (!discountsState.early_bird.rules || discountsState.early_bird.rules.length === 0)) {
                discountsState.early_bird.rules = [{ percentage: 15, days_ahead: 30 }];
            }
            if (type === 'length_of_stay' && (!discountsState.length_of_stay.rules || discountsState.length_of_stay.rules.length === 0)) {
                discountsState.length_of_stay.rules = [{ percentage: 10, nights: 7 }];
            }
            if (type === 'custom' && (!discountsState.custom.rules || discountsState.custom.rules.length === 0)) {
                discountsState.custom.rules = [{ percentage: 20, start_date: '', end_date: '' }];
            }
        }
        renderDiscounts();
    }

    function updateLMField(field, val) {
        discountsState.last_minute[field] = parseInt(val) || 0;
        renderDiscounts();
    }

    function addEBRule() {
        discountsState.early_bird.rules.push({ percentage: 15, days_ahead: 30 });
        renderDiscounts();
    }

    function updateEBRule(idx, field, val) {
        discountsState.early_bird.rules[idx][field] = parseInt(val) || 0;
        renderDiscounts();
    }

    function removeEBRule(idx) {
        discountsState.early_bird.rules.splice(idx, 1);
        renderDiscounts();
    }

    function addLOSRule() {
        discountsState.length_of_stay.rules.push({ percentage: 10, nights: 7 });
        renderDiscounts();
    }

    function updateLOSRule(idx, field, val) {
        discountsState.length_of_stay.rules[idx][field] = parseInt(val) || 0;
        renderDiscounts();
    }

    function removeLOSRule(idx) {
        discountsState.length_of_stay.rules.splice(idx, 1);
        renderDiscounts();
    }

    function addCustRule() {
        discountsState.custom.rules.push({ percentage: 20, start_date: '', end_date: '' });
        renderDiscounts();
    }

    function updateCustRule(idx, field, val) {
        if (field === 'percentage') {
            discountsState.custom.rules[idx][field] = parseInt(val) || 0;
        } else {
            discountsState.custom.rules[idx][field] = val;
        }
        document.getElementById('discountsInput').value = JSON.stringify(discountsState);
    }

    function removeCustRule(idx) {
        discountsState.custom.rules.splice(idx, 1);
        renderDiscounts();
    }

    // Initialize all custom components on window load
    document.addEventListener('DOMContentLoaded', function() {
        renderBedrooms();
        syncBedroomAllocations();
        renderEnhancements();
        renderDiscounts();
    });
</script>
@endsection