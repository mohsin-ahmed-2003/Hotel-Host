<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password | {{ $siteSettings->get('site_name', 'Hotel Host') }}</title>
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

        .fp-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(30,58,138,0.10);
            border: 1px solid #e2e8f0;
            width: 100%;
            max-width: 480px;
            overflow: hidden;
        }

        body.dark-mode .fp-card { background: #1f2937; border-color: #374151; }

        .fp-header {
            background: linear-gradient(135deg, #1E3A8A, #2563eb);
            padding: 32px 36px;
            text-align: center;
        }

        .fp-header h1 { color: #fff; font-size: 24px; font-weight: 800; margin-bottom: 6px; }
        .fp-header p  { color: #93afd4; font-size: 13px; }

        .fp-body { padding: 32px 36px; }

        /* Steps indicator */
        .steps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-bottom: 28px;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .step-circle {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.3s;
        }

        .step.active .step-circle  { background: #1E3A8A; color: #fff; }
        .step.done .step-circle    { background: #16a34a; color: #fff; }

        .step-label { font-size: 11px; color: #94a3b8; font-weight: 600; white-space: nowrap; }
        .step.active .step-label { color: #1E3A8A; }
        body.dark-mode .step.active .step-label { color: #93c5fd; }

        .step-line { width: 48px; height: 2px; background: #e2e8f0; margin-bottom: 16px; transition: background 0.3s; }
        .step-line.done { background: #16a34a; }

        /* Form */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 7px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            color: #0f172a;
            background: #f8fafc;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); background: #fff; }
        .form-control:disabled { opacity: 0.5; cursor: not-allowed; }
        .form-control.err { border-color: #ef4444; }

        body.dark-mode .form-control { background: #111827; border-color: #374151; color: #e2e8f0; }
        body.dark-mode .form-control:focus { background: #111827; }

        .field-err { font-size: 12px; color: #ef4444; margin-top: 4px; display: none; }

        /* Code input */
        .code-input {
            text-align: center;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 10px;
            font-family: 'Courier New', monospace;
            color: #1E3A8A;
        }

        body.dark-mode .code-input { color: #93c5fd; }

        .btn-primary {
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
            margin-top: 8px;
        }

        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .info-text {
            font-size: 13px;
            color: #64748b;
            text-align: center;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        body.dark-mode .info-text { color: #6b7280; }

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
        body.dark-mode .toast.error   { background: rgba(239,68,68,0.15);  color: #fca5a5; }

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

        .hidden { display: none !important; }
    </style>
</head>
<body>

@include('partials.header')

<div class="toast-container" id="toastContainer"></div>

<div class="page-body">
    <div class="fp-card">
        <div class="fp-header">
            <h1>Forgot Password</h1>
            <p>Reset your account password in 3 simple steps</p>
        </div>

        <div class="fp-body">
            <!-- Steps -->
            <div class="steps">
                <div class="step active" id="step1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Email</div>
                </div>
                <div class="step-line" id="line1"></div>
                <div class="step" id="step2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Verify</div>
                </div>
                <div class="step-line" id="line2"></div>
                <div class="step" id="step3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Reset</div>
                </div>
            </div>

            <!-- Step 1: Email -->
            <div id="panel1">
                <p class="info-text">Enter your registered email address and we'll send you a 6-digit reset code.</p>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" id="f_email" class="form-control" placeholder="your@email.com">
                    <div class="field-err" id="e_email"></div>
                </div>
                <button class="btn-primary" id="btn1" onclick="sendCode()">Send Reset Code</button>
            </div>

            <!-- Step 2: Verify code -->
            <div id="panel2" class="hidden">
                <p class="info-text">Enter the 6-digit code sent to <strong id="emailDisplay"></strong></p>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" id="f_email2" class="form-control" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Reset Code</label>
                    <input type="text" id="f_code" class="form-control code-input" placeholder="000000" maxlength="6">
                    <div class="field-err" id="e_code"></div>
                </div>
                <button class="btn-primary" id="btn2" onclick="verifyCode()">Verify Code</button>
            </div>

            <!-- Step 3: New password -->
            <div id="panel3" class="hidden">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" id="f_email3" class="form-control" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Reset Code</label>
                    <input type="text" id="f_code3" class="form-control code-input" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" id="f_newpw" class="form-control" placeholder="Min 8 chars, upper, lower, number, symbol">
                    <div class="field-err" id="e_newpw"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" id="f_confirmpw" class="form-control" placeholder="Repeat new password">
                    <div class="field-err" id="e_confirmpw"></div>
                </div>
                <button class="btn-primary" id="btn3" onclick="resetPassword()">Reset Password</button>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let verifiedEmail = '';
    let verifiedCode  = '';

    function showToast(msg, type = 'success') {
        const c = document.getElementById('toastContainer');
        const t = document.createElement('div');
        t.className = 'toast ' + type;
        t.innerHTML = `<span>${type==='success'?'✅':'❌'}</span><span style="flex:1">${msg}</span><button class="toast-close" onclick="this.closest('.toast').remove()">✕</button><div class="toast-progress"></div>`;
        c.appendChild(t);
        setTimeout(() => { t.style.animation='toastOut 0.4s ease forwards'; setTimeout(()=>t.remove(),400); }, 5000);
    }

    function setStep(n) {
        [1,2,3].forEach(i => {
            document.getElementById('step'+i).classList.remove('active','done');
            document.getElementById('panel'+i).classList.add('hidden');
        });
        for (let i = 1; i < n; i++) {
            document.getElementById('step'+i).classList.add('done');
            document.querySelector('#step'+i+' .step-circle').textContent = '✓';
            if (document.getElementById('line'+i)) document.getElementById('line'+i).classList.add('done');
        }
        document.getElementById('step'+n).classList.add('active');
        document.getElementById('panel'+n).classList.remove('hidden');
    }

    function sendCode() {
        const email = document.getElementById('f_email').value.trim();
        const errEl = document.getElementById('e_email');
        const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!email) { errEl.textContent='Email is required.'; errEl.style.display='block'; document.getElementById('f_email').classList.add('err'); return; }
        if (!emailReg.test(email)) { errEl.textContent='Enter a valid email.'; errEl.style.display='block'; document.getElementById('f_email').classList.add('err'); return; }
        errEl.style.display='none'; document.getElementById('f_email').classList.remove('err');

        const btn = document.getElementById('btn1');
        btn.textContent = 'Sending...'; btn.disabled = true;

        fetch('{{ route("forgot.send") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ email })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success === false) {
                showToast(data.message, 'error');
            } else {
                showToast(data.message, 'success');
                verifiedEmail = email;
                document.getElementById('f_email2').value = email;
                document.getElementById('emailDisplay').textContent = email;
                setStep(2);
            }
        })
        .catch(() => showToast('Something went wrong.', 'error'))
        .finally(() => { btn.textContent = 'Send Reset Code'; btn.disabled = false; });
    }

    function verifyCode() {
        const code  = document.getElementById('f_code').value.trim();
        const errEl = document.getElementById('e_code');

        if (!code || code.length !== 6 || !/^\d{6}$/.test(code)) {
            errEl.textContent = 'Enter the 6-digit code.'; errEl.style.display = 'block';
            document.getElementById('f_code').classList.add('err'); return;
        }
        errEl.style.display = 'none'; document.getElementById('f_code').classList.remove('err');

        const btn = document.getElementById('btn2');
        btn.textContent = 'Verifying...'; btn.disabled = true;

        fetch('{{ route("forgot.verify") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ email: verifiedEmail, code })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                verifiedCode = code;
                document.getElementById('f_email3').value = verifiedEmail;
                document.getElementById('f_code3').value  = code;
                setStep(3);
            } else {
                errEl.textContent = data.message; errEl.style.display = 'block';
                document.getElementById('f_code').classList.add('err');
            }
        })
        .catch(() => showToast('Something went wrong.', 'error'))
        .finally(() => { btn.textContent = 'Verify Code'; btn.disabled = false; });
    }

    function resetPassword() {
        const pwReg = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;
        let valid = true;

        const pw = document.getElementById('f_newpw').value;
        const pwErr = document.getElementById('e_newpw');
        if (!pw) { pwErr.textContent='Password is required.'; pwErr.style.display='block'; document.getElementById('f_newpw').classList.add('err'); valid=false; }
        else if (!pwReg.test(pw)) { pwErr.textContent='Min 8 chars with upper, lower, number & symbol.'; pwErr.style.display='block'; document.getElementById('f_newpw').classList.add('err'); valid=false; }
        else { pwErr.style.display='none'; document.getElementById('f_newpw').classList.remove('err'); }

        const cpw = document.getElementById('f_confirmpw').value;
        const cpwErr = document.getElementById('e_confirmpw');
        if (!cpw) { cpwErr.textContent='Please confirm your password.'; cpwErr.style.display='block'; document.getElementById('f_confirmpw').classList.add('err'); valid=false; }
        else if (pw !== cpw) { cpwErr.textContent='Passwords do not match.'; cpwErr.style.display='block'; document.getElementById('f_confirmpw').classList.add('err'); valid=false; }
        else { cpwErr.style.display='none'; document.getElementById('f_confirmpw').classList.remove('err'); }

        if (!valid) return;

        const btn = document.getElementById('btn3');
        btn.textContent = 'Resetting...'; btn.disabled = true;

        fetch('{{ route("forgot.reset") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ email: verifiedEmail, code: verifiedCode, password: pw, password_confirmation: cpw })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Password reset successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("auth") }}?toast=Password+reset+successfully.+Please+login+with+your+new+password';
                }, 2000);
            } else {
                showToast(data.message || 'Reset failed.', 'error');
                btn.textContent = 'Reset Password'; btn.disabled = false;
            }
        })
        .catch(() => { showToast('Something went wrong.', 'error'); btn.textContent = 'Reset Password'; btn.disabled = false; });
    }
</script>
</body>
</html>
