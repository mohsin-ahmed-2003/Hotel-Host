@extends('host.layout')

@section('styles')
    <style>
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 24px;
        }

        .media-item {
            position: relative;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
        }

        .media-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        }

        .media-item img,
        .media-item video {
            width: 100%;
            height: 160px;
            object-fit: cover;
            display: block;
        }

        .media-content {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-sizing: border-box;
        }

        .upload-box {
            border: 2px dashed var(--border);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: var(--bg-primary);
        }

        .upload-box:hover {
            border-color: var(--primary);
            background: var(--card-bg);
        }

        .btn-media-danger {
            color: var(--error);
            background: none;
            border: none;
            font-size: 13px;
            font-weight: 700;
            text-decoration: underline;
            cursor: pointer;
            padding: 0;
            text-align: left;
        }
    </style>
@endsection

@section('host-content')
    <h1 class="host-title">Showcase your space</h1>
    <p class="host-subtitle">Great photos help guests imagine staying at your place.</p>

    <div class="mb-5">
        <div class="upload-box" onclick="document.getElementById('photoInput').click()">
            <i class="fas fa-camera fa-2x mb-2 text-primary"></i>
            <div class="fw-bold">Click to upload photos</div>
            <div class="text-muted small">JPG, PNG or WEBP (Max 5MB)</div>
        </div>
        <input type="file" id="photoInput" multiple accept="image/*" style="display:none;" onchange="uploadPhotos(this)">

        <div class="media-grid" id="photoGrid">
            @foreach($room->photos as $photo)
                <div class="media-item" id="photo_{{ $photo->id }}">
                    <img src="{{ Storage::url($photo->photo_path) }}" alt="Room photo">
                    <div class="media-content">
                        <div class="form-floating-airbnb" id="wrap_desc_{{ $photo->id }}" style="margin-bottom: 0;">
                            <input type="text" class="form-control-airbnb" id="desc_{{ $photo->id }}"
                                value="{{ $photo->description }}" placeholder=" "
                                oninput="updatePhotoDesc({{ $photo->id }}, this.value)"
                                style="height: 48px; padding: 22px 12px 6px; font-size: 14px; border-radius: 10px;">
                            <label for="desc_{{ $photo->id }}" style="top: 18px; left: 10px; font-size: 12px;">Caption</label>
                        </div>
                        <button class="btn-media-danger" onclick="deletePhoto({{ $photo->id }})">Delete</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mb-5">
        <h5 class="fw-bold mb-3">Video Tour (Optional)</h5>
        <div class="upload-box" onclick="document.getElementById('videoInput').click()" style="padding: 20px;">
            <i class="fas fa-video fa-xl mb-2 text-primary"></i>
            <div class="fw-bold">Upload a video tour</div>
        </div>
        <input type="file" id="videoInput" accept="video/*" style="display:none;" onchange="uploadVideo(this)">

        <div id="videoContainer" style="margin-top: 20px;">
            @if($room->video_path)
                <div class="rounded-4 overflow-hidden border">
                    <video src="{{ Storage::url($room->video_path) }}" controls style="width:100%; max-height: 300px;"></video>
                </div>
            @endif
        </div>
    </div>

    <div class="host-actions">
        <a href="{{ route('host.step', ['room' => $room->id, 'step' => 1]) }}" class="btn-prev">Back</a>
        <a href="{{ route('host.step', ['room' => $room->id, 'step' => 3]) }}" class="btn-next">
            <span class="btn-text">Save & Next</span>
            <div class="btn-spinner"></div>
        </a>
    </div>
@endsection

@section('scripts')
    <script>
        function uploadPhotos(input) {
            if (!input.files.length) return;
            showToast('saving', 'Uploading...');

            Array.from(input.files).forEach(file => {
                const formData = new FormData();
                formData.append('photo', file);

                fetch(`/host/{{ $room->id }}/upload-photo`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF },
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToast('success', 'Uploaded');
                            addPhotoToGrid(data.photo);
                            if (data.step_valid !== null) updateStepperStatus(data.step, data.step_valid);
                        } else {
                            showToast('error', 'Upload failed');
                        }
                    })
                    .catch(() => showToast('error', 'Upload failed'));
            });
            input.value = ''; // reset
        }

        function addPhotoToGrid(photo) {
            const grid = document.getElementById('photoGrid');
            const html = `
                <div class="media-item" id="photo_${photo.id}">
                    <img src="/storage/${photo.photo_path}" alt="Room photo">
                    <div class="media-content">
                        <div class="form-floating-airbnb" id="wrap_desc_${photo.id}" style="margin-bottom: 0;">
                            <input type="text" class="form-control-airbnb" id="desc_${photo.id}" placeholder=" " oninput="updatePhotoDesc(${photo.id}, this.value)" style="height: 48px; padding: 22px 12px 6px; font-size: 14px; border-radius: 10px;">
                            <label for="desc_${photo.id}" style="top: 12px; left: 12px; font-size: 12px;">Caption</label>
                        </div>
                        <button class="btn-media-danger" onclick="deletePhoto(${photo.id})">Delete</button>
                    </div>
                </div>
            `;
            grid.insertAdjacentHTML('beforeend', html);
        }

        let photoDescTimeout;
        function updatePhotoDesc(id, description) {
            clearTimeout(photoDescTimeout);
            showToast('saving', 'Saving caption...');
            photoDescTimeout = setTimeout(() => {
                fetch(`/host/photo/${id}/update-desc`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({ description })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) showToast('success', 'Caption saved');
                    });
            }, 500);
        }

        function deletePhoto(id) {
            if (!confirm('Delete this photo?')) return;
            fetch(`/host/photo/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('photo_' + id).remove();
                        if (data.step_valid !== null) updateStepperStatus(data.step, data.step_valid);
                    }
                });
        }

        function uploadVideo(input) {
            if (!input.files.length) return;
            showToast('saving', 'Uploading video...');
            const formData = new FormData();
            formData.append('video', input.files[0]);

            fetch(`/host/{{ $room->id }}/upload-video`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF },
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Video uploaded');
                        location.reload();
                    } else {
                        showToast('error', 'Upload failed');
                    }
                })
                .catch(() => showToast('error', 'Upload failed'));
        }
    </script>
@endsection