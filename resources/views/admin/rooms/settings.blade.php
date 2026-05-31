@extends('admin.layout')

@section('title', 'Room Step Settings')

@section('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .btn-custom {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        text-decoration: none;
        color: #fff;
    }

    .btn-secondary-solid { background: #64748b; }
    .btn-secondary-solid:hover { background: #475569; transform: translateY(-1px); }

    .btn-primary-solid { background: #6366f1; }
    .btn-primary-solid:hover { background: #4f46e5; transform: translateY(-1px); }

    .settings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 40px; /* Space after the steps row */
    }

    .step-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
    }

    .step-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        background: rgba(0,0,0,0.02);
    }

    .step-card-body {
        padding: 20px;
    }

    .image-upload-box {
        background: var(--bg);
        border: 2px dashed var(--border);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
    }

    .preview-img {
        max-height: 140px;
        border-radius: 8px;
        margin-bottom: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .form-label-custom {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 8px;
    }

    .form-control-custom {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-size: 14px;
        background: var(--bg);
        color: var(--text);
        outline: none;
    }

    .form-control-custom:focus { border-color: var(--primary); }

    @media (max-width: 992px) {
        .settings-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 style="font-size: 24px; font-weight: 700; margin: 0;">Room Step Settings</h1>
    <a href="{{ route('admin.rooms.index') }}" class="btn-custom btn-secondary-solid">
        <i class="fas fa-arrow-left"></i> Back to Rooms
    </a>
</div>

<form action="{{ route('admin.rooms.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="settings-grid">
        @php
            $steps = [
                'basic' => 'Step 1: Basics',
                'media' => 'Step 2: Media Hub',
                'location' => 'Step 3: Location',
                'amenities' => 'Step 4: Amenities',
                'pricing' => 'Step 5: Pricing Options'
            ];
        @endphp

        @foreach($steps as $key => $title)
        <div class="step-card">
            <div class="step-card-header">
                <h6 style="margin: 0; font-weight: 700; color: var(--primary);">{{ $title }}</h6>
            </div>
            <div class="step-card-body">
                <div class="image-upload-box">
                    @if(isset($settings[$key]) && $settings[$key]->image)
                        <img src="{{ asset('storage/' . $settings[$key]->image) }}" class="preview-img">
                    @else
                        <div style="opacity: 0.2; margin-bottom: 10px;"><i class="fas fa-image fa-3x"></i></div>
                        <p style="font-size: 12px; color: var(--text-muted);">No image set</p>
                    @endif
                    <input type="file" name="img_{{ $key }}" class="form-control-custom" style="font-size: 12px; padding: 6px;">
                    <div style="font-size: 11px; color: var(--text-muted); mt-2;">Recommended: 16:9 Landscape</div>
                </div>

                <div>
                    <label class="form-label-custom">Step Description</label>
                    <textarea name="desc_{{ $key }}" class="form-control-custom" rows="3" placeholder="What should guests do here?">{{ $settings[$key]->description ?? '' }}</textarea>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div style="background: var(--card); border-radius: 16px; padding: 20px; border: 1px solid var(--border); text-align: right; margin-bottom: 40px;">
        <button type="submit" class="btn-custom btn-primary-solid" style="padding: 12px 40px;">
            <i class="fas fa-save"></i> Save All Settings
        </button>
    </div>
</form>
@endsection
