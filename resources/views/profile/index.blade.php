<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Profile | {{ $siteSettings->get('site_name', 'Hotel Host') }}</title>
    @if($siteSettings->get('site_favicon'))
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($siteSettings->get('site_favicon')) }}" type="image/x-icon">
    @endif
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background 0.3s, color 0.3s;
        }

        body.dark-mode { background: #0f172a; color: #f1f5f9; }

        /* ── Toast ── */
        .toast-container {
            position: fixed; top: 20px; right: 20px;
            z-index: 9999;
            display: flex; flex-direction: column; gap: 10px;
        }

        .toast {
            position: relative;
            display: flex; align-items: center; gap: 12px;
            padding: 14px 18px;
            border-radius: 12px;
            min-width: 300px; max-width: 400px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            font-size: 14px; font-weight: 500;
            animation: toastIn 0.4s cubic-bezier(0.34,1.56,0.64,1) forwards;
        }

        .toast.success { background:#dcfce7; color:#15803d; border-left:4px solid #16a34a; }
        .toast.error   { background:#fee2e2; color:#b91c1c; border-left:4px solid #dc2626; }

        body.dark-mode .toast.success { background:rgba(16,185,129,0.15); color:#6ee7b7; border-left-color:#10b981; }
        body.dark-mode .toast.error   { background:rgba(239,68,68,0.15);  color:#fca5a5; border-left-color:#ef4444; }

        .toast-close { background:none; border:none; cursor:pointer; font-size:16px; color:inherit; opacity:0.6; margin-left:auto; }
        .toast-close:hover { opacity:1; }

        .toast-progress {
            position:absolute; bottom:0; left:0; height:3px;
            background:currentColor; opacity:0.3;
            border-radius:0 0 12px 12px;
            animation: toastProgress 5s linear forwards;
        }

        @keyframes toastIn {
            from { opacity:0; transform:translateX(60px); }
            to   { opacity:1; transform:translateX(0); }
        }
        @keyframes toastOut {
            from { opacity:1; transform:translateX(0); }
            to   { opacity:0; transform:translateX(60px); }
        }
        @keyframes toastProgress {
            from { width:100%; } to { width:0%; }
        }

        /* ── Layout ── */
        .page-content {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 24px;
            align-items: start;
            flex: 1;
            width: 100%;
        }

        .profile-left  { min-width: 0; }
        .profile-right { min-width: 0; }

        @media (max-width: 800px) {
            .page-content {
                grid-template-columns: 1fr;
                margin: 20px auto;
            }
            .profile-right { order: -1; }
            .profile-img-card { padding: 20px 16px; }
            .form-grid { grid-template-columns: 1fr; }
        }

        /* ── Card ── */
        .card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 20px;
        }

        body.dark-mode .card { background:#1e293b; box-shadow:0 4px 24px rgba(0,0,0,0.4); }

        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 16px; font-weight: 700; color: #1e293b;
        }

        body.dark-mode .card-header { border-color:#334155; color:#f1f5f9; }
        .card-body { padding: 24px; }

        /* ── Right sidebar ── */
        .profile-img-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 28px 20px;
            text-align: center;
            margin-bottom: 16px;
        }

        body.dark-mode .profile-img-card { background:#1e293b; }

        .profile-img-wrap {
            position: relative;
            display: inline-block;
            margin-bottom: 14px;
        }

        .profile-img {
            width: 100px; height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #667eea;
            box-shadow: 0 4px 20px rgba(102,126,234,0.3);
        }

        .profile-img-btn {
            position: absolute; bottom: 4px; right: 4px;
            width: 30px; height: 30px;
            background: #667eea;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            border: 3px solid #fff;
            transition: background 0.2s;
        }

        body.dark-mode .profile-img-btn { border-color:#1e293b; }
        .profile-img-btn:hover { background:#4f46e5; }
        .profile-img-btn input { display:none; }

        .profile-uname  { font-size:17px; font-weight:800; color:#1e293b; margin-bottom:4px; }
        .profile-uemail { font-size:13px; color:#64748b; display:flex; align-items:center; gap:6px; justify-content:center; }

        body.dark-mode .profile-uname  { color:#f1f5f9; }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #dcfce7;
            color: #15803d;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
        }

        body.dark-mode .verified-badge { background: rgba(16,185,129,0.15); color: #6ee7b7; }

        .verify-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: none;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s;
        }

        .verify-btn:hover { border-color: #2563eb; color: #2563eb; background: #eff6ff; }

        /* ── Sidebar tabs ── */
        .sidebar-tab {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px; font-weight: 600; color: #64748b;
            transition: all 0.2s;
            border: none; background: none;
            width: 100%; text-align: left;
            font-family: inherit;
            margin-bottom: 6px;
        }

        .sidebar-tab:hover { background:#f1f5f9; color:#1e293b; }
        .sidebar-tab.active { background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; }
        body.dark-mode .sidebar-tab:hover { background:#263348; color:#f1f5f9; }

        /* ── Form ── */
        @media (max-width:600px) { .form-grid { grid-template-columns:1fr; } }

        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .form-group { display:flex; flex-direction:column; gap:6px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        body.dark-mode .form-label { color: #94a3b8; }

        /* Input wrapper animation — same as login page */
        .input-wrapper {
            position: relative;
            border-radius: 8px;
        }

        .input-wrapper-clip {
            position: absolute;
            inset: 0;
            border-radius: 8px;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .input-wrapper-clip::before { content: none; }

        .input-wrapper.focused .input-wrapper-clip::before {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            width: 200%; height: 200%;
            background: conic-gradient(#667eea, #764ba2, #a78bfa, #667eea);
            transform: translate(-50%, -50%) rotate(0deg);
            animation: profileRotateBorder 2s linear infinite;
        }

        .input-wrapper::after {
            content: '';
            position: absolute;
            inset: 2px;
            border-radius: 7px;
            background: white;
            z-index: 1;
        }

        body.dark-mode .input-wrapper::after { background: #1e293b; }

        .input-wrapper input,
        .input-wrapper select {
            position: relative;
            z-index: 2;
            background: transparent;
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #667eea;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: #333;
            transition: border-color 0.2s;
        }

        body.dark-mode .input-wrapper input,
        body.dark-mode .input-wrapper select { color: #f1f5f9; border-color: #334155; }

        .input-wrapper.focused input,
        .input-wrapper.focused select { border-color: transparent; outline: none; }

        .input-wrapper input:focus,
        .input-wrapper select:focus { outline: none; }

        @keyframes profileRotateBorder {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to   { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .field-err { font-size:12px; color:#ef4444; display:none; margin-top:2px; }

        .btn-save {
            margin-top: 20px;
            padding: 11px 26px;
            background: linear-gradient(135deg,#667eea,#764ba2);
            color: #fff; border: none; border-radius: 10px;
            font-size: 14px; font-weight: 700; cursor: pointer;
            transition: opacity 0.2s, transform 0.2s;
            font-family: inherit;
        }

        .btn-save:hover { opacity:0.9; transform:translateY(-1px); }
        .btn-save:disabled { opacity:0.6; cursor:not-allowed; transform:none; }

        .tab-panel { display:none; }
        .tab-panel.active { display:block; }
    </style>
</head>
<body>

@include('partials.header')

<div class="toast-container" id="toastContainer"></div>

<div class="page-content">

    <!-- LEFT: panels -->
    <div class="profile-left">
        <!-- Personal Info -->
        <div class="tab-panel active" id="panel-info">
            <div class="card">
                <div class="card-header">Personal Information</div>
                <div class="card-body">
                    <form id="infoForm" novalidate>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Full Name</label>
                                <div class="input-wrapper">
                                    <div class="input-wrapper-clip"></div>
                                    <input type="text" id="f_name" value="{{ $user->name }}" required>
                                </div>
                                <div class="field-err" id="e_name"></div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email Address</label>
                                <div style="position:relative;">
                                    <div class="input-wrapper">
                                        <div class="input-wrapper-clip"></div>
                                        <input type="email" id="f_email" value="{{ $user->email }}" style="padding-right:80px;" required>
                                    </div>
                                    @if($user->email_verified)
                                        <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);z-index:10;" title="Email Verified">
                                            <img src="{{ asset('images/email_verify.png') }}" style="width:22px;height:22px;">
                                        </span>
                                    @else
                                        <button type="button" id="inlineVerifyBtn" onclick="sendVerificationEmail()"
                                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);z-index:10;background:#2563eb;border:none;border-radius:6px;color:#fff;font-size:11px;font-weight:700;padding:5px 10px;cursor:pointer;font-family:inherit;white-space:nowrap;"
                                            title="Verify email to get notifications on discounts and updates">
                                            Verify
                                        </button>
                                    @endif
                                </div>
                                <div class="field-err" id="e_email"></div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Gender</label>
                                <div class="input-wrapper">
                                    <div class="input-wrapper-clip"></div>
                                    <select id="f_gender" required>
                                        <option value="male"   {{ $user->gender=='male'   ?'selected':'' }}>Male</option>
                                        <option value="female" {{ $user->gender=='female' ?'selected':'' }}>Female</option>
                                        <option value="other"  {{ $user->gender=='other'  ?'selected':'' }}>Other</option>
                                    </select>
                                </div>
                                <div class="field-err" id="e_gender"></div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date of Birth</label>
                                <div class="input-wrapper">
                                    <div class="input-wrapper-clip"></div>
                                    <input type="date" id="f_dob" value="{{ $user->date_of_birth?->format('Y-m-d') }}" required>
                                </div>
                                <div class="field-err" id="e_dob"></div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Country</label>
                                <div class="input-wrapper">
                                    <div class="input-wrapper-clip"></div>
                                    <select id="f_country" required>
                                        @foreach($countries as $c)
                                            <option value="{{ $c->id }}" {{ $user->country_id==$c->id ?'selected':'' }}>{{ $c->country_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field-err" id="e_country"></div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <div style="position:relative;">
                                    <div class="input-wrapper">
                                        <div class="input-wrapper-clip"></div>
                                        <input type="text" id="f_phone" value="{{ $user->phone }}" style="padding-right:80px;" required>
                                    </div>
                                    @if($user->phone_verified)
                                        <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);z-index:10;" title="Phone Verified">
                                            <img src="{{ asset('images/email_verify.png') }}" style="width:22px;height:22px;">
                                        </span>
                                    @else
                                        <button type="button" id="sendPhoneOtpBtn" onclick="sendPhoneOtp()"
                                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);z-index:10;background:#2563eb;border:none;border-radius:6px;color:#fff;font-size:11px;font-weight:700;padding:5px 10px;cursor:pointer;font-family:inherit;white-space:nowrap;"
                                            title="Verify phone number">
                                            Verify
                                        </button>
                                    @endif
                                </div>
                                <div class="field-err" id="e_phone"></div>
                                <!-- OTP input appended here after send -->
                                <div id="phoneOtpGroup" style="display:none;margin-top:10px;">
                                    <label class="form-label">Enter Verification Code</label>
                                    <div style="position:relative;">
                                        <input type="text" id="phoneOtpInput" class="form-control"
                                               placeholder="000000" maxlength="6" inputmode="numeric"
                                               style="text-align:center;font-size:20px;font-weight:800;letter-spacing:8px;font-family:'Courier New',monospace;padding-right:80px;">
                                        <button type="button" onclick="verifyPhoneOtp()"
                                            id="verifyPhoneBtn"
                                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:#16a34a;border:none;border-radius:6px;color:#fff;font-size:11px;font-weight:700;padding:5px 10px;cursor:pointer;font-family:inherit;">
                                            Verify
                                        </button>
                                    </div>
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;">
                                        <button type="button" id="resendPhoneBtn" onclick="sendPhoneOtp()" disabled
                                            style="background:none;border:none;color:#2563eb;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;opacity:0.4;">Resend OTP</button>
                                        <span id="resendPhoneTimer" style="font-size:12px;color:#64748b;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn-save">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password -->
        <div class="tab-panel" id="panel-password">
            <div class="card">
                <div class="card-header">Change Password</div>
                <div class="card-body">
                    <form id="pwForm" novalidate>
                        <div class="form-group" style="margin-bottom:16px;">
                            <label class="form-label">New Password</label>
                            <div class="input-wrapper">
                                <div class="input-wrapper-clip"></div>
                                <input type="password" id="f_pw" placeholder="Min 8 chars, upper, lower, number, symbol">
                            </div>
                            <div class="field-err" id="e_pw"></div>
                        </div>
                        <div class="form-group" style="margin-bottom:16px;">
                            <label class="form-label">Confirm New Password</label>
                            <div class="input-wrapper">
                                <div class="input-wrapper-clip"></div>
                                <input type="password" id="f_pw_confirm" placeholder="Repeat new password">
                            </div>
                            <div class="field-err" id="e_pw_confirm"></div>
                        </div>
                        @if(\App\Models\SiteSetting::get('recaptcha_enabled') === '1' && \App\Models\SiteSetting::get('recaptcha_site_key'))
                        <div class="form-group" style="margin-bottom:16px;">
                            <div class="g-recaptcha" data-sitekey="{{ \App\Models\SiteSetting::get('recaptcha_site_key') }}" id="pwRecaptcha"></div>
                            <div class="field-err" id="e_recaptcha"></div>
                        </div>
                        @endif
                        <button type="submit" class="btn-save">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="profile-right">
        <div class="profile-img-card">
            <div class="profile-img-wrap">
                <img src="{{ asset($user->profile_image ?? 'images/image.png') }}"
                     alt="Profile" class="profile-img" id="profileImg">
                <label class="profile-img-btn" title="Change photo">
                    <svg width="13" height="13" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    <input type="file" id="avatarInput" accept="image/*">
                </label>
            </div>
            <div class="profile-uname" id="displayName">{{ $user->name }}</div>
            <div class="profile-uemail" id="displayEmail">
                {{ $user->email }}
                @if($user->email_verified)
                    <div style="margin-top:8px;">
                    <img src="{{ asset('images/email_verify.png') }}" alt="Verified" style="width:24px;height:24px;" title="Email Verified">
                </div>
                @endif
            </div>
            @if(!$user->email_verified)
                <div style="margin-top:10px;">
                    <button class="verify-btn" id="sendVerifyBtn" onclick="sendVerificationEmail()" title="Verify email to get notifications on discounts and updates">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        Verify Email
                    </button>
                </div>
                
            @endif
        </div>

        <div class="card">
            <div class="card-body" style="padding:14px;">
                <button class="sidebar-tab active" onclick="switchTab('info',this)">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Personal Info
                </button>
                <button class="sidebar-tab" onclick="switchTab('password',this)">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Account / Password
                </button>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')

@if(\App\Models\SiteSetting::get('recaptcha_enabled') === '1' && \App\Models\SiteSetting::get('recaptcha_site_key'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

<script>
    // Show toast from query param (e.g. after password reset redirect)
    const _urlParams = new URLSearchParams(window.location.search);
    const _toastMsg  = _urlParams.get('toast');
    if (_toastMsg) {
        showToast(decodeURIComponent(_toastMsg), 'success');
        // Clean URL without reload
        window.history.replaceState({}, '', window.location.pathname);
    }

    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    // Toast
    function showToast(msg, type = 'success') {
        const c = document.getElementById('toastContainer');
        const t = document.createElement('div');
        t.className = 'toast ' + type;
        t.innerHTML = `<span>${type==='success'?'✅':'❌'}</span><span style="flex:1">${msg}</span><button class="toast-close" onclick="this.closest('.toast').remove()">✕</button><div class="toast-progress"></div>`;
        c.appendChild(t);
        setTimeout(() => { t.style.animation='toastOut 0.4s ease forwards'; setTimeout(()=>t.remove(),400); }, 5000);
    }

    // Tab switch
    function switchTab(name, btn) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.sidebar-tab').forEach(b => b.classList.remove('active'));
        document.getElementById('panel-' + name).classList.add('active');
        btn.classList.add('active');
    }

    // Error helpers — target the input inside wrapper
    function se(id, msg) {
        document.getElementById('e_'+id).textContent = msg;
        document.getElementById('e_'+id).style.display = 'block';
        const inp = document.getElementById('f_'+id);
        if (inp) { inp.style.borderColor = '#ef4444'; }
    }
    function ce(id) {
        document.getElementById('e_'+id).style.display = 'none';
        const inp = document.getElementById('f_'+id);
        if (inp) { inp.style.borderColor = ''; }
    }

    // Focus/blur for rotating border animation
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.input-wrapper input, .input-wrapper select').forEach(el => {
            el.addEventListener('focus', () => el.closest('.input-wrapper').classList.add('focused'));
            el.addEventListener('blur',  () => el.closest('.input-wrapper').classList.remove('focused'));
        });
    });

    // Info form
    document.getElementById('infoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let valid = true;
        const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneReg = /^\+?[0-9\s\-\(\)]{7,20}$/;

        const name = document.getElementById('f_name').value.trim();
        if (!name) { se('name','Full name is required.'); valid=false; } else ce('name');

        const email = document.getElementById('f_email').value.trim();
        if (!email) { se('email','Email is required.'); valid=false; }
        else if (!emailReg.test(email)) { se('email','Enter a valid email.'); valid=false; }
        else ce('email');

        const gender = document.getElementById('f_gender').value;
        if (!gender) { se('gender','Select a gender.'); valid=false; } else ce('gender');

        const dob = document.getElementById('f_dob').value;
        if (!dob) { se('dob','Date of birth is required.'); valid=false; }
        else {
            const age = Math.floor((new Date()-new Date(dob))/(365.25*24*60*60*1000));
            if (age < 18) { se('dob','You must be at least 18 years old.'); valid=false; }
            else ce('dob');
        }

        const country = document.getElementById('f_country').value;
        if (!country) { se('country','Select a country.'); valid=false; } else ce('country');

        const phone = document.getElementById('f_phone').value.trim();
        if (!phone) { se('phone','Phone is required.'); valid=false; }
        else if (!phoneReg.test(phone)) { se('phone','Enter a valid phone number.'); valid=false; }
        else ce('phone');

        if (!valid) return;

        const btn = this.querySelector('.btn-save');
        btn.textContent = 'Saving...'; btn.disabled = true;

        fetch('{{ route("user.profile.update") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ name, email, phone, date_of_birth: dob, gender, country_id: country })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Reflect changes immediately
                document.getElementById('displayName').textContent  = name;
                document.getElementById('displayEmail').textContent = email;
                // Update header dropdown if present
                const hn = document.getElementById('headerDropdownName');
                const he = document.getElementById('headerDropdownEmail');
                if (hn) hn.textContent = name;
                if (he) he.textContent = email;
            } else {
                showToast(data.message || 'Update failed.', 'error');
            }
        })
        .catch(() => showToast('Something went wrong.', 'error'))
        .finally(() => { btn.textContent = 'Save Changes'; btn.disabled = false; });
    });

    // Password form
    document.getElementById('pwForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let valid = true;
        const pwReg = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

        const pw = document.getElementById('f_pw').value;
        if (!pw) { se('pw','Password is required.'); valid=false; }
        else if (!pwReg.test(pw)) { se('pw','Min 8 chars with upper, lower, number & symbol.'); valid=false; }
        else ce('pw');

        const confirm = document.getElementById('f_pw_confirm').value;
        if (!confirm) { se('pw_confirm','Please confirm your password.'); valid=false; }
        else if (pw !== confirm) { se('pw_confirm','Passwords do not match.'); valid=false; }
        else ce('pw_confirm');

        // reCAPTCHA check
        const recaptchaEl = document.getElementById('pwRecaptcha');
        let recaptchaToken = '';
        if (recaptchaEl) {
            recaptchaToken = grecaptcha.getResponse(0);
            if (!recaptchaToken) {
                document.getElementById('e_recaptcha').textContent = 'Please complete the reCAPTCHA.';
                document.getElementById('e_recaptcha').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('e_recaptcha').style.display = 'none';
            }
        }

        if (!valid) return;

        const btn = this.querySelector('.btn-save');
        btn.textContent = 'Updating...'; btn.disabled = true;

        fetch('{{ route("user.profile.password") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ password: pw, password_confirmation: confirm, recaptcha_token: recaptchaToken })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                document.getElementById('f_pw').value = '';
                document.getElementById('f_pw_confirm').value = '';
                if (recaptchaEl) grecaptcha.reset();
                // Logout and redirect
                if (data.redirect) {
                    setTimeout(() => { window.location.href = data.redirect; }, 2000);
                }
            } else {
                showToast(data.message || 'Update failed.', 'error');
                if (recaptchaEl) grecaptcha.reset();
                btn.textContent = 'Update Password'; btn.disabled = false;
            }
        })
        .catch(() => { showToast('Something went wrong.', 'error'); btn.textContent = 'Update Password'; btn.disabled = false; });
    });

    // Send email verification
    function sendVerificationEmail() {
        const btn1 = document.getElementById('sendVerifyBtn');
        const btn2 = document.getElementById('inlineVerifyBtn');

        [btn1, btn2].forEach(b => { if (b) { b.textContent = 'Sending...'; b.disabled = true; } });

        fetch('{{ route("email.send.verification") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }
        })
        .then(r => r.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            [btn1, btn2].forEach(b => { if (b) { b.textContent = data.success ? 'Sent ✓' : 'Retry'; b.disabled = data.success; } });
        })
        .catch(() => {
            showToast('Something went wrong.', 'error');
            [btn1, btn2].forEach(b => { if (b) { b.textContent = 'Verify'; b.disabled = false; } });
        });
    }

    // Phone OTP verification
    let phoneResendInterval = null;

    function startPhoneResendTimer(sec = 30) {
        const btn   = document.getElementById('resendPhoneBtn');
        const timer = document.getElementById('resendPhoneTimer');
        btn.disabled = true; btn.style.opacity = '0.4';
        let remaining = sec;
        clearInterval(phoneResendInterval);
        phoneResendInterval = setInterval(() => {
            remaining--;
            timer.textContent = `Resend in ${remaining}s`;
            if (remaining <= 0) {
                clearInterval(phoneResendInterval);
                btn.disabled = false; btn.style.opacity = '1';
                timer.textContent = '';
            }
        }, 1000);
        timer.textContent = `Resend in ${remaining}s`;
    }

    function sendPhoneOtp() {
        const phone = document.getElementById('f_phone').value.trim();
        const phoneReg = /^\+?[0-9\s\-\(\)]{7,20}$/;
        if (!phone || !phoneReg.test(phone)) {
            se('phone', 'Enter a valid phone number.'); return;
        }
        ce('phone');

        const btn = document.getElementById('sendPhoneOtpBtn');
        if (btn) { btn.textContent = 'Sending...'; btn.disabled = true; }

        fetch('{{ route("profile.phone.send") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ phone })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                document.getElementById('phoneOtpGroup').style.display = 'block';
                if (btn) { btn.textContent = 'Sent'; btn.disabled = true; }
                startPhoneResendTimer(data.resend_after || 30);
                if (data.dev_otp) {
                    document.getElementById('phoneOtpInput').value = data.dev_otp;
                    showToast('DEV OTP: ' + data.dev_otp, 'success');
                }
            } else {
                showToast(data.message, 'error');
                if (btn) { btn.textContent = 'Verify'; btn.disabled = false; }
            }
        })
        .catch(() => { showToast('Something went wrong.', 'error'); if (btn) { btn.textContent = 'Verify'; btn.disabled = false; } });
    }

    function verifyPhoneOtp() {
        const otp = document.getElementById('phoneOtpInput').value.trim();
        if (!otp || otp.length !== 6 || !/^\d{6}$/.test(otp)) {
            showToast('Enter the 6-digit OTP.', 'error'); return;
        }

        const btn = document.getElementById('verifyPhoneBtn');
        btn.textContent = 'Verifying...'; btn.disabled = true;

        fetch('{{ route("profile.phone.verify") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ otp })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Replace verify button with verified badge
                document.getElementById('phoneOtpGroup').style.display = 'none';
                const sendBtn = document.getElementById('sendPhoneOtpBtn');
                if (sendBtn) {
                    sendBtn.outerHTML = '<span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);z-index:10;" title="Phone Verified"><img src="{{ asset("images/email_verify.png") }}" style="width:22px;height:22px;"></span>';
                }
            } else {
                showToast(data.message, 'error');
                btn.textContent = 'Verify'; btn.disabled = false;
            }
        })
        .catch(() => { showToast('Something went wrong.', 'error'); btn.textContent = 'Verify'; btn.disabled = false; });
    }

    // Avatar upload
    document.getElementById('avatarInput').addEventListener('change', function() {
        if (!this.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => document.getElementById('profileImg').src = e.target.result;
        reader.readAsDataURL(this.files[0]);

        const formData = new FormData();
        formData.append('profile_image', this.files[0]);
        formData.append('_token', CSRF);

        fetch('{{ route("user.profile.avatar") }}', { method:'POST', body:formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('profileImg').src = data.url;
                // Update header profile image too
                const hi = document.getElementById('headerProfileImg');
                if (hi) hi.src = data.url;
                showToast(data.message, 'success');
            } else {
                showToast('Image upload failed.', 'error');
            }
        })
        .catch(() => showToast('Something went wrong.', 'error'));
    });
</script>
</body>
</html>
