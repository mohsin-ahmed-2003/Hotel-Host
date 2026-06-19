<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    @if(isset($siteSettings) && $siteSettings->get('site_favicon'))
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($siteSettings->get('site_favicon')) }}" type="image/x-icon">
    @endif
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated background orbs */
        body::before, body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 8s ease-in-out infinite;
        }

        body::before {
            width: 500px; height: 500px;
            background: #6366f1;
            top: -100px; left: -100px;
        }

        body::after {
            width: 400px; height: 400px;
            background: #764ba2;
            bottom: -100px; right: -100px;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(30px) scale(1.05); }
        }

        .login-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 24px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5);
        }

        .login-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 46px; height: 46px;
            background: linear-gradient(135deg, #6366f1, #764ba2);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }

        .logo-text { font-size: 20px; font-weight: 800; color: #fff; }
        .logo-sub  { font-size: 12px; color: #64748b; font-weight: 500; }

        .login-title {
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 32px;
        }

        .alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.3);
            color: #6ee7b7;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .form-group { margin-bottom: 20px; }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: #0f172a;
            border: 1.5px solid #334155;
            border-radius: 10px;
            font-size: 14px;
            color: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }

        .form-control::placeholder { color: #475569; }

        .form-control:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }

        .form-control.err { border-color: #ef4444; }

        .field-err {
            font-size: 12px;
            color: #f87171;
            margin-top: 5px;
            display: none;
        }

        .password-wrap { position: relative; }

        .password-wrap .form-control { padding-right: 44px; }

        .pw-toggle {
            position: absolute;
            right: 14px; top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #475569;
            font-size: 16px;
            user-select: none;
            transition: color 0.2s;
        }

        .pw-toggle:hover { color: #94a3b8; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #6366f1, #764ba2);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.2s;
            margin-top: 8px;
            font-family: inherit;
            letter-spacing: 0.3px;
        }

        .btn-login:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #475569;
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover { color: #94a3b8; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-logo">
        <div class="logo-icon">⚡</div>
        <div>
            <div class="logo-text">AdminPanel</div>
            <div class="logo-sub">Secure Access</div>
        </div>
    </div>

    <div class="login-title">Welcome back</div>
    <div class="login-subtitle">Sign in to your admin account</div>

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <form id="adminLoginForm" action="{{ route('admin.login.post') }}" method="POST" novalidate>
        @csrf

        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" id="f_email" name="email"
                   class="form-control"
                   value="{{ old('email') }}"
                   placeholder="admin@example.com"
                   autocomplete="email">
            <div class="field-err" id="e_email"></div>
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="password-wrap">
                <input type="password" id="f_password" name="password"
                       class="form-control"
                       placeholder="Enter your password"
                       autocomplete="current-password">
                <span class="pw-toggle" onclick="togglePw()">👁️</span>
            </div>
            <div class="field-err" id="e_password"></div>
        </div>

        <button type="submit" class="btn-login">Sign In to Admin Panel</button>
    </form>

    <a href="{{ route('homepage') }}" class="back-link">← Back to main site</a>
</div>

<script>
    // Show backend errors inline
    @if($errors->has('email'))
        document.getElementById('e_email').textContent = '{{ $errors->first('email') }}';
        document.getElementById('e_email').style.display = 'block';
        document.getElementById('f_email').classList.add('err');
    @endif

    function togglePw() {
        const f = document.getElementById('f_password');
        f.type = f.type === 'password' ? 'text' : 'password';
    }

    document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let valid = true;
        const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        const email = document.getElementById('f_email').value.trim();
        const eErr  = document.getElementById('e_email');
        if (!email) {
            eErr.textContent = 'Email address is required.';
            eErr.style.display = 'block';
            document.getElementById('f_email').classList.add('err');
            valid = false;
        } else if (!emailReg.test(email)) {
            eErr.textContent = 'Enter a valid email address.';
            eErr.style.display = 'block';
            document.getElementById('f_email').classList.add('err');
            valid = false;
        } else {
            eErr.style.display = 'none';
            document.getElementById('f_email').classList.remove('err');
        }

        const pw   = document.getElementById('f_password').value;
        const pErr = document.getElementById('e_password');
        if (!pw) {
            pErr.textContent = 'Password is required.';
            pErr.style.display = 'block';
            document.getElementById('f_password').classList.add('err');
            valid = false;
        } else {
            pErr.style.display = 'none';
            document.getElementById('f_password').classList.remove('err');
        }

        if (valid) this.submit();
    });
</script>
</body>
</html>
