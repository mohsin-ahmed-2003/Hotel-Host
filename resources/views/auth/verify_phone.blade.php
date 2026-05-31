<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Mobile Number | {{ $siteSettings->get('site_name', 'Hotel Host') }}</title>
    @if($siteSettings->get('site_favicon'))
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($siteSettings->get('site_favicon')) }}">
    @endif
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #F8FAFC;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background 0.3s;
        }

        body.dark-mode { background: #121212; color: #e2e8f0; }

        .page-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .vp-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(30,58,138,0.10);
            border: 1px solid #e2e8f0;
            width: 100%;
            max-width: 460px;
            overflow: hidden;
        }

        body.dark-mode .vp-card { background: #1f2937; border-color: #374151; }

        .vp-header {
            background: linear-gradient(135deg, #1E3A8A, #2563eb);
            padding: 28px 32px;
            text-align: center;
        }

        .vp-header h1 { color: #fff; font-size: 22px; font-weight: 800; margin-bottom: 4px; }
        .vp-header p  { color: #93afd4; font-size: 13px; }

        .vp-body { padding: 28px 32px; }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 7px;
        }

        .input-row {
            display: flex;
            gap: 8px;
            align-items: stretch;
        }

        .country-select {
            width: 110px;
            flex-shrink: 0;
            padding: 11px 10px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 13px;
            color: #0f172a;
            background: #f8fafc;
            font-family: inherit;
            transition: border-color 0.2s;
        }

        .country-select:focus { outline: none; border-color: #2563eb; }
        .country-select:disabled { opacity: 0.6; cursor: not-allowed; }

        body.dark-mode .country-select { background: #111827; border-color: #374151; color: #e2e8f0; }

        .phone-input {
            flex: 1;
            padding: 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            color: #0f172a;
            background: #f8fafc;
            font-family: inherit;
            transition: border-color 0.2s;
        }

        .phone-input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .phone-input:disabled { opacity: 0.6; cursor: not-allowed; }
        .phone-input.err { border-color: #ef4444; }

        body.dark-mode .phone-input { background: #111827; border-color: #374151; color: #e2e8f0; }

        .field-err { font-size: 12px; color: #ef4444; margin-top: 4px; display: none; }

        .edit-btn {
            display: none;
            margin-top: 8px;
            background: none;
            border: 1px solid #2563eb;
            color: #2563eb;
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s;
        }

        .edit-btn:hover { background: #eff6ff; }

        /* OTP input */
        .otp-group {
            display: none;
            margin-top: 18px;
        }

        .otp-input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 22px;
            font-weight: 800;
            text-align: center;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            color: #1E3A8A;
            background: #f8fafc;
            transition: border-color 0.2s;
        }

        .otp-input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .otp-input.err { border-color: #ef4444; }

        body.dark-mode .otp-input { background: #111827; border-color: #374151; color: #93c5fd; }

        /* Buttons */
        .btn-send {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1E3A8A, #2563eb);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: opacity 0.2s, transform 0.2s;
            margin-top: 20px;
        }

        .btn-send:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-send:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .resend-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 12px;
        }

        .resend-btn {
            background: none;
            border: none;
            color: #2563eb;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: opacity 0.2s;
        }

        .resend-btn:disabled { opacity: 0.4; cursor: not-allowed; }
        .resend-timer { font-size: 12px; color: #64748b; }

        body.dark-mode .resend-timer { color: #6b7280; }

        /* Toast */
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }

        .toast {
            position: relative;
            display: flex; align-items: center; gap: 12px;
            padding: 14px 18px;
            border-radius: 12px;
            min-width: 280px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            font-size: 14px; font-weight: 500;
            animation: toastIn 0.4s cubic-bezier(0.34,1.56,0.64,1) forwards;
        }

        .toast.success { background: #dcfce7; color: #15803d; border-left: 4px solid #16a34a; }
        .toast.error   { background: #fee2e2; color: #b91c1c; border-left: 4px solid #dc2626; }

        body.dark-mode .toast.success { background: rgba(16,185,129,0.15); color: #6ee7b7; }
        body.dark-mode .toast.error   { background: rgba(239,68,68,0.15); color: #fca5a5; }

        .toast-close { background: none; border: none; cursor: pointer; font-size: 16px; color: inherit; opacity: 0.6; margin-left: auto; }

        .toast-progress {
            position: absolute; bottom: 0; left: 0; height: 3px;
            background: currentColor; opacity: 0.3;
            border-radius: 0 0 12px 12px;
            animation: toastProgress 5s linear forwards;
        }

        @keyframes toastIn { from { opacity:0; transform:translateX(60px); } to { opacity:1; transform:translateX(0); } }
        @keyframes toastOut { from { opacity:1; } to { opacity:0; transform:translateX(60px); } }
        @keyframes toastProgress { from { width:100%; } to { width:0%; } }

        .info-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            color: #1e40af;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        body.dark-mode .info-note { background: rgba(37,99,235,0.1); border-color: #1e40af; color: #93c5fd; }
    </style>
</head>
<body>

@include('partials.header')

<div class="toast-container" id="toastContainer"></div>

<div class="page-body">
    <div class="vp-card">
        <div class="vp-header">
            <div style="font-size:36px;margin-bottom:8px;">📱</div>
            <h1>Verify Mobile Number</h1>
            <p>We'll send a 6-digit OTP to confirm your number</p>
        </div>

        <div class="vp-body">
            <div class="info-note">
                📲 An OTP will be sent to your mobile number. Please ensure it's correct before proceeding.
            </div>

            <!-- Phone field -->
            <div style="margin-bottom:6px;">
                <label class="form-label">Mobile Number</label>
                <div class="input-row">
                    <select id="countrySelect" class="country-select" disabled>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}"
                                data-code="{{ $c->phone_code }}"
                                {{ $pending->data['country_id'] == $c->id ? 'selected' : '' }}>
                                {{ $c->phone_code }}
                            </option>
                        @endforeach
                    </select>
                    <input type="tel" id="phoneInput" class="phone-input"
                           value="{{ $pending->phone }}" disabled>
                </div>
                <div class="field-err" id="e_phone"></div>
                <button class="edit-btn" id="editBtn" onclick="enableEdit()">✏️ Edit Mobile Number</button>
            </div>

            <!-- OTP field (shown after send) -->
            <div class="otp-group" id="otpGroup">
                <label class="form-label">Enter Verification Code</label>
                <input type="text" id="otpInput" class="otp-input"
                       placeholder="000000" maxlength="6" inputmode="numeric">
                <div class="field-err" id="e_otp"></div>

                <div class="resend-row">
                    <button class="resend-btn" id="resendBtn" onclick="sendOtp(true)" disabled>Resend OTP</button>
                    <span class="resend-timer" id="resendTimer"></span>
                </div>
            </div>

            <!-- Send OTP button -->
            <button class="btn-send" id="sendBtn" onclick="sendOtp(false)">Send OTP</button>

            <!-- Verify button (shown after OTP sent) -->
            <button class="btn-send" id="verifyBtn" onclick="verifyOtp()"
                    style="display:none;background:linear-gradient(135deg,#15803d,#16a34a);">
                Verify & Complete Registration
            </button>
        </div>
    </div>
</div>

@include('partials.footer')

<script>
    const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
    const TOKEN  = '{{ $pending->token }}';
    const SEND_URL   = '{{ route("phone.send.otp", $pending->token) }}';
    const VERIFY_URL = '{{ route("phone.verify.otp", $pending->token) }}';

    let resendInterval = null;
    let otpSent = false;

    function showToast(msg, type = 'success') {
        const c = document.getElementById('toastContainer');
        const t = document.createElement('div');
        t.className = 'toast ' + type;
        t.innerHTML = `<span>${type==='success'?'✅':'❌'}</span><span style="flex:1">${msg}</span><button class="toast-close" onclick="this.closest('.toast').remove()">✕</button><div class="toast-progress"></div>`;
        c.appendChild(t);
        setTimeout(() => { t.style.animation='toastOut 0.4s ease forwards'; setTimeout(()=>t.remove(),400); }, 5000);
    }

    function startResendTimer(seconds = 30) {
        const btn   = document.getElementById('resendBtn');
        const timer = document.getElementById('resendTimer');
        btn.disabled = true;
        let remaining = seconds;

        clearInterval(resendInterval);
        resendInterval = setInterval(() => {
            remaining--;
            timer.textContent = `Resend in ${remaining}s`;
            if (remaining <= 0) {
                clearInterval(resendInterval);
                btn.disabled = false;
                timer.textContent = '';
            }
        }, 1000);
        timer.textContent = `Resend in ${remaining}s`;
    }

    function enableEdit() {
        document.getElementById('countrySelect').disabled = false;
        document.getElementById('phoneInput').disabled    = false;
        document.getElementById('phoneInput').focus();
        document.getElementById('editBtn').style.display = 'none';
    }

    // Auto-show edit button after 30s
    setTimeout(() => {
        document.getElementById('editBtn').style.display = 'inline-block';
    }, 30000);

    function sendOtp(isResend = false) {
        const phone      = document.getElementById('phoneInput').value.trim();
        const countryId  = document.getElementById('countrySelect').value;
        const phoneReg   = /^\+?[0-9\s\-\(\)]{7,20}$/;
        const errEl      = document.getElementById('e_phone');

        if (!phone || !phoneReg.test(phone)) {
            errEl.textContent = 'Enter a valid phone number.';
            errEl.style.display = 'block';
            document.getElementById('phoneInput').classList.add('err');
            return;
        }
        errEl.style.display = 'none';
        document.getElementById('phoneInput').classList.remove('err');

        const btn = document.getElementById('sendBtn');
        const resendBtn = document.getElementById('resendBtn');
        if (isResend) {
            resendBtn.textContent = 'Sending...'; resendBtn.disabled = true;
        } else {
            btn.textContent = 'Sending...'; btn.disabled = true;
        }

        fetch(SEND_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ phone, country_id: countryId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'OTP sent!', 'success');

                document.getElementById('otpGroup').style.display = 'block';
                document.getElementById('verifyBtn').style.display = 'block';
                btn.style.display = 'none';

                document.getElementById('phoneInput').disabled    = true;
                document.getElementById('countrySelect').disabled = true;
                document.getElementById('editBtn').style.display  = 'none';

                otpSent = true;
                startResendTimer(data.resend_after || 30);

                if (data.dev_otp) {
                    document.getElementById('otpInput').value = data.dev_otp;
                    showToast('DEV: OTP auto-filled: ' + data.dev_otp, 'success');
                }
            } else {
                showToast(data.message || 'Failed to send OTP.', 'error');
                if (isResend) {
                    resendBtn.textContent = 'Resend OTP'; resendBtn.disabled = false;
                } else {
                    btn.textContent = 'Send OTP'; btn.disabled = false;
                }
            }
        })
        .catch(() => {
            showToast('Something went wrong.', 'error');
            if (isResend) {
                resendBtn.textContent = 'Resend OTP'; resendBtn.disabled = false;
            } else {
                btn.textContent = 'Send OTP'; btn.disabled = false;
            }
        });
    }

    function verifyOtp() {
        const otp    = document.getElementById('otpInput').value.trim();
        const errEl  = document.getElementById('e_otp');

        if (!otp || otp.length !== 6 || !/^\d{6}$/.test(otp)) {
            errEl.textContent = 'Enter the 6-digit OTP.';
            errEl.style.display = 'block';
            document.getElementById('otpInput').classList.add('err');
            return;
        }
        errEl.style.display = 'none';
        document.getElementById('otpInput').classList.remove('err');

        const btn = document.getElementById('verifyBtn');
        btn.textContent = 'Verifying...'; btn.disabled = true;

        fetch(VERIFY_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ otp })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Phone verified! Redirecting...', 'success');
                setTimeout(() => { window.location.href = data.redirect || '/'; }, 1500);
            } else {
                showToast(data.message || 'Invalid OTP.', 'error');
                btn.textContent = 'Verify & Complete Registration'; btn.disabled = false;
            }
        })
        .catch(() => { showToast('Something went wrong.', 'error'); btn.textContent = 'Verify & Complete Registration'; btn.disabled = false; });
    }
</script>
</body>
</html>
