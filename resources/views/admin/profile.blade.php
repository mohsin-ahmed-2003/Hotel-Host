@extends('admin.layout')

@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('page-subtitle', 'View and update your account details')

@section('styles')
<style>
    .profile-page { display: grid; grid-template-columns: 300px 1fr; gap: 24px; align-items: start; }
    @media (max-width: 900px) { .profile-page { grid-template-columns: 1fr; } }

    .profile-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; text-align: center; }
    .profile-card-banner { height: 90px; background: linear-gradient(135deg, #6366f1, #764ba2); }
    .profile-card-body { padding: 0 24px 28px; }

    .profile-img-wrap { position: relative; display: inline-block; margin-top: -44px; margin-bottom: 14px; }
    .profile-img { width: 88px; height: 88px; border-radius: 50%; object-fit: cover; border: 4px solid var(--card); box-shadow: 0 4px 16px rgba(0,0,0,0.12); display: block; }
    .profile-img-edit { position: absolute; bottom: 2px; right: 2px; width: 26px; height: 26px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid var(--card); transition: background 0.2s; }
    .profile-img-edit:hover { background: var(--primary-dark); }
    .profile-img-edit input { display: none; }

    .profile-name  { font-size: 18px; font-weight: 800; color: var(--text); margin-bottom: 4px; }
    .profile-email { font-size: 13px; color: var(--text-muted); margin-bottom: 12px; }

    .profile-meta { display: flex; flex-direction: column; gap: 10px; margin-top: 20px; text-align: left; }
    .profile-meta-item { display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--text-muted); padding: 10px 14px; background: var(--bg); border-radius: 10px; }
    .profile-meta-item strong { color: var(--text); font-weight: 600; }

    .profile-form-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; }
    .profile-form-tabs { display: flex; border-bottom: 1px solid var(--border); background: var(--bg); }
    .profile-tab { padding: 14px 24px; font-size: 13px; font-weight: 600; color: var(--text-muted); cursor: pointer; border-bottom: 2px solid transparent; transition: all 0.2s; background: none; border-top: none; border-left: none; border-right: none; font-family: inherit; }
    .profile-tab.active { color: var(--primary); border-bottom-color: var(--primary); background: var(--card); }
    .tab-panel { display: none; padding: 28px; }
    .tab-panel.active { display: block; }

    .field-err { font-size: 12px; color: var(--danger); margin-top: 4px; display: none; }
    .form-control.err { border-color: var(--danger); }
</style>
@endsection

@section('content')
<div class="profile-page">

    <!-- Left: Profile Card -->
    <div class="profile-card">
        <div class="profile-card-banner"></div>
        <div class="profile-card-body">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" id="imgForm">
                @csrf @method('PUT')
                <div class="profile-img-wrap">
                    <img src="{{ asset($user->profile_image ?? 'images/Admin-Profile.png') }}"
                         alt="Profile" class="profile-img" id="profileImgPreview">
                    <label class="profile-img-edit" title="Change photo">
                        <svg width="12" height="12" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        <input type="file" name="profile_image" accept="image/*" id="profileImgInput">
                    </label>
                </div>
                <input type="hidden" name="name"          value="{{ $user->name }}">
                <input type="hidden" name="email"         value="{{ $user->email }}">
                <input type="hidden" name="phone"         value="{{ $user->phone }}">
                <input type="hidden" name="date_of_birth" value="{{ $user->date_of_birth?->format('Y-m-d') }}">
                <input type="hidden" name="gender"        value="{{ $user->gender }}">
                <input type="hidden" name="country_id"    value="{{ $user->country_id }}">
            </form>

            <div class="profile-name">{{ $user->name }}</div>
            <div class="profile-email">{{ $user->email }}</div>
            <span class="badge badge-{{ $user->role }}">{{ ucfirst(str_replace('_',' ',$user->role)) }}</span>

            <div class="profile-meta">
                <div class="profile-meta-item">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.18 2 2 0 0 1 3.6 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.6a16 16 0 0 0 6 6l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16z"/></svg>
                    <div><div style="font-size:11px;">Phone</div><strong>{{ $user->phone ?? '—' }}</strong></div>
                </div>
                <div class="profile-meta-item">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <div><div style="font-size:11px;">Date of Birth</div><strong>{{ $user->date_of_birth?->format('d M Y') ?? '—' }}</strong></div>
                </div>
                <div class="profile-meta-item">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <div><div style="font-size:11px;">Country</div><strong>{{ $user->countryRelation?->country_name ?? $user->country ?? '—' }}</strong></div>
                </div>
                <div class="profile-meta-item">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <div><div style="font-size:11px;">Gender</div><strong>{{ ucfirst($user->gender ?? '—') }}</strong></div>
                </div>
                <div class="profile-meta-item">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <div><div style="font-size:11px;">Member Since</div><strong>{{ $user->created_at?->format('d M Y') }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Edit Form -->
    <div class="profile-form-card">
        <div class="profile-form-tabs">
            <button class="profile-tab active" onclick="switchTab('info', this)">Personal Info</button>
            <button class="profile-tab" onclick="switchTab('password', this)">Change Password</button>
        </div>

        <!-- Personal Info Tab -->
        <div class="tab-panel active" id="tab-info">
            <form id="infoForm" action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf @method('PUT')
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" id="p_name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        <div class="field-err" id="pe_name"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" id="p_email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        <div class="field-err" id="pe_email"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" id="p_phone" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                        <div class="field-err" id="pe_phone"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" id="p_dob" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}" required>
                        <div class="field-err" id="pe_dob"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select id="p_gender" name="gender" class="form-control" required>
                            <option value="male"   {{ old('gender',$user->gender)=='male'   ? 'selected':'' }}>Male</option>
                            <option value="female" {{ old('gender',$user->gender)=='female' ? 'selected':'' }}>Female</option>
                            <option value="other"  {{ old('gender',$user->gender)=='other'  ? 'selected':'' }}>Other</option>
                        </select>
                        <div class="field-err" id="pe_gender"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Country</label>
                        <select id="p_country" name="country_id" class="form-control" required>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id',$user->country_id)==$country->id ? 'selected':'' }}>
                                    {{ $country->country_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="field-err" id="pe_country"></div>
                    </div>
                </div>
                <input type="hidden" name="password" value="">
                <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Tab -->
        <div class="tab-panel" id="tab-password">
            <form id="pwForm" action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf @method('PUT')
                <input type="hidden" name="name"          value="{{ $user->name }}">
                <input type="hidden" name="email"         value="{{ $user->email }}">
                <input type="hidden" name="phone"         value="{{ $user->phone }}">
                <input type="hidden" name="date_of_birth" value="{{ $user->date_of_birth?->format('Y-m-d') }}">
                <input type="hidden" name="gender"        value="{{ $user->gender }}">
                <input type="hidden" name="country_id"    value="{{ $user->country_id }}">

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" id="pw_new" name="password" class="form-control" placeholder="Min 8 chars, upper, lower, number, symbol" required>
                    <div class="field-err" id="pwe_new"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" id="pw_confirm" name="password_confirmation" class="form-control" placeholder="Repeat new password" required>
                    <div class="field-err" id="pwe_confirm"></div>
                </div>
                <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function switchTab(name, btn) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.profile-tab').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
    }

    function se(id, msg) {
        document.getElementById('pe_' + id).textContent = msg;
        document.getElementById('pe_' + id).style.display = 'block';
        document.getElementById('p_' + id).classList.add('err');
    }
    function ce(id) {
        document.getElementById('pe_' + id).style.display = 'none';
        document.getElementById('p_' + id).classList.remove('err');
    }

    // Info form validation
    document.getElementById('infoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let valid = true;
        const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneReg = /^\+?[0-9\s\-\(\)]{7,20}$/;

        const name = document.getElementById('p_name').value.trim();
        if (!name) { se('name', 'Full name is required.'); valid = false; } else ce('name');

        const email = document.getElementById('p_email').value.trim();
        if (!email) { se('email', 'Email is required.'); valid = false; }
        else if (!emailReg.test(email)) { se('email', 'Enter a valid email address.'); valid = false; }
        else ce('email');

        const phone = document.getElementById('p_phone').value.trim();
        if (!phone) { se('phone', 'Phone number is required.'); valid = false; }
        else if (!phoneReg.test(phone)) { se('phone', 'Enter a valid phone number.'); valid = false; }
        else ce('phone');

        const dob = document.getElementById('p_dob').value;
        if (!dob) { se('dob', 'Date of birth is required.'); valid = false; }
        else {
            const age = Math.floor((new Date() - new Date(dob)) / (365.25 * 24 * 60 * 60 * 1000));
            if (age < 18) { se('dob', 'You must be at least 18 years old.'); valid = false; }
            else ce('dob');
        }

        if (valid) this.submit();
    });

    // Password form validation
    document.getElementById('pwForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let valid = true;
        const pwReg = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

        const pw = document.getElementById('pw_new').value;
        const confirm = document.getElementById('pw_confirm').value;

        if (!pw) {
            document.getElementById('pwe_new').textContent = 'New password is required.';
            document.getElementById('pwe_new').style.display = 'block';
            document.getElementById('pw_new').classList.add('err');
            valid = false;
        } else if (!pwReg.test(pw)) {
            document.getElementById('pwe_new').textContent = 'Min 8 chars with uppercase, lowercase, number & symbol.';
            document.getElementById('pwe_new').style.display = 'block';
            document.getElementById('pw_new').classList.add('err');
            valid = false;
        } else {
            document.getElementById('pwe_new').style.display = 'none';
            document.getElementById('pw_new').classList.remove('err');
        }

        if (!confirm) {
            document.getElementById('pwe_confirm').textContent = 'Please confirm your password.';
            document.getElementById('pwe_confirm').style.display = 'block';
            document.getElementById('pw_confirm').classList.add('err');
            valid = false;
        } else if (pw !== confirm) {
            document.getElementById('pwe_confirm').textContent = 'Passwords do not match.';
            document.getElementById('pwe_confirm').style.display = 'block';
            document.getElementById('pw_confirm').classList.add('err');
            valid = false;
        } else {
            document.getElementById('pwe_confirm').style.display = 'none';
            document.getElementById('pw_confirm').classList.remove('err');
        }

        if (valid) this.submit();
    });

    // Profile image preview + auto-submit
    document.getElementById('profileImgInput').addEventListener('change', function() {
        if (!this.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => document.getElementById('profileImgPreview').src = e.target.result;
        reader.readAsDataURL(this.files[0]);
        document.getElementById('imgForm').submit();
    });
</script>
@endsection
