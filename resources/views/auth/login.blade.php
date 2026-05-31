@extends('layouts.app')

@section('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .auth-page-wrapper {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: calc(100vh - 80px); /* Subtract approximate header height */
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
        transition: background 0.3s;
        position: relative;
    }

    body.dark-mode .auth-page-wrapper {
        background: #0f172a;
    }

    body.dark-mode .auth-container {
        background: #1e293b;
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    }

    body.dark-mode .auth-content {
        background: #1e293b;
    }

    body.dark-mode label { color: #94a3b8; }

    body.dark-mode input,
    body.dark-mode select,
    body.dark-mode textarea {
        background: #0f172a;
        color: #f1f5f9;
        border-color: #334155;
    }

    body.dark-mode .input-wrapper::after { background: #0f172a; }
    body.dark-mode .input-wrapper-clip::before { background: conic-gradient(#6366f1, #764ba2, #a78bfa, #6366f1); }

    body.dark-mode .password-validation { background: #263348; color: #94a3b8; }
    body.dark-mode .validation-item { color: #64748b; }
    body.dark-mode .back-link { color: #a5b4fc; }

    .theme-toggle-auth {
        position: fixed;
        top: 16px;
        right: 16px;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 50px;
        padding: 8px 14px;
        cursor: pointer;
        color: white;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        backdrop-filter: blur(8px);
        transition: background 0.2s;
        z-index: 100;
    }

    .theme-toggle-auth:hover { background: rgba(255,255,255,0.25); }

    .auth-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        width: 100%;
        max-width: 450px;
    }

    .auth-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 40px 30px;
        text-align: center;
        position: relative;
        animation: headerSlideDown 0.5s ease forwards;
    }

    .auth-header h1 {
        color: white;
        font-size: 28px;
        margin-bottom: 10px;
        font-weight: 600;
        animation: welcomeAppearThenSlide 1.5s ease forwards;
    }

    @keyframes headerSlideDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes welcomeAppearThenSlide {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        40% {
            opacity: 1;
            transform: translateY(0);
        }
        100% {
            opacity: 1;
            transform: translateY(-15px);
        }
    }

    .auth-tabs {
        display: flex;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50px;
        padding: 4px;
        width: 100%;
        max-width: 300px;
        margin: 20px auto 0;
    }

    .social-header-btns {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin: 16px auto 0;
    }

    .btn-social-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 14px;
        border-radius: 50px;
        background: rgba(255,255,255,0.15);
        border: 1.5px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(8px);
        text-decoration: none;
        color: #fff;
        font-family: inherit;
        cursor: pointer;
        overflow: hidden;
        will-change: min-width;
        transition: background 0.25s, border-color 0.25s, box-shadow 0.25s,
                    min-width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        min-width: 40px;
        white-space: nowrap;
    }

    .btn-social-pill:hover {
        background: rgba(255,255,255,0.28);
        box-shadow: 0 4px 18px rgba(0,0,0,0.18);
    }

    .btn-social-pill .sp-logo {
        width: 20px; height: 20px;
        flex-shrink: 0;
        display: block;
    }

    .btn-social-pill .sp-label {
        white-space: nowrap;
        overflow: hidden;
        width: 0;
        opacity: 0;
        margin-left: 0;
        font-size: 13px;
        font-weight: 600;
        will-change: width, opacity;
        transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1),
                    opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                    margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-social-pill.revealed .sp-label {
        width: auto;
        max-width: 120px;
        opacity: 1;
        margin-left: 7px;
    }

    .auth-tabs button {
        flex: 1;
        padding: 10px 20px;
        border: none;
        background: transparent;
        color: white;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        border-radius: 50px;
    }

    .auth-tabs button.active {
        background: white;
        color: #667eea;
    }

    .auth-content {
        padding: 40px 30px;
        position: relative;
        overflow: visible;
        min-height: 300px;
    }

    .form-section {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        opacity: 0;
        pointer-events: none;
        transform: translateY(40px);
        transition: none;
        padding: 0; /* Changed from 40px 30px to 0 to avoid jump */
    }

    .form-section.active {
        position: relative;
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0);
        animation: formSlideUp 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        padding: 0;
    }

    .form-section.exit-animation {
        animation: formSlideDown 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }

    @keyframes formSlideUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes formSlideDown {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }

    .form-group {
        margin-bottom: 20px;
        display: block;
        opacity: 1;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .input-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #333;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Input base — consistent in both modes */
    input, select, textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s ease;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #ffffff;
        color: #1e293b;
    }

    body.dark-mode input,
    body.dark-mode select,
    body.dark-mode textarea {
        background: #0f172a;
        color: #f1f5f9;
        border-color: #334155;
    }

    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear,
    input[type="password"]::-webkit-textfield-decoration-container,
    input[type="password"]::-webkit-password-decoration-toggle {
        display: none !important;
    }

    .password-container input {
        padding-right: 45px;
    }

    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: transparent;
        box-shadow: none;
    }

    /* Wrapper: the clip holds the spinning conic gradient */
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

    /* Spinning border shown only when focused */
    .input-wrapper.focused .input-wrapper-clip::before {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        width: 200%; height: 200%;
        background: conic-gradient(#667eea, #764ba2, #a78bfa, #667eea);
        transform: translate(-50%, -50%) rotate(0deg);
        animation: rotateBorder 2s linear infinite;
    }

    /* Inner fill covers the spinning gradient leaving only a 2px border ring */
    .input-wrapper::after {
        content: '';
        position: absolute;
        inset: 2px;
        border-radius: 7px;
        background: #ffffff;
        z-index: 1;
        transition: background 0.2s;
    }

    body.dark-mode .input-wrapper::after { background: #0f172a; }

    /* Input sits above the ::after fill */
    .input-wrapper input,
    .input-wrapper select,
    .input-wrapper textarea {
        position: relative;
        z-index: 2;
        background: transparent;
        /* Keep the 2px border visible at all times — blue in light, slate in dark */
        border: 2px solid #667eea;
    }

    body.dark-mode .input-wrapper input,
    body.dark-mode .input-wrapper select,
    body.dark-mode .input-wrapper textarea {
        border-color: #334155;
        color: #f1f5f9;
    }

    /* While focused: hide the static border so only the spinning ring shows */
    .input-wrapper.focused input,
    .input-wrapper.focused select,
    .input-wrapper.focused textarea {
        border-color: transparent;
        background: transparent;
    }

    @keyframes rotateBorder {
        from { transform: translate(-50%, -50%) rotate(0deg); }
        to   { transform: translate(-50%, -50%) rotate(360deg); }
    }

    .password-container {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #667eea;
        font-size: 18px;
        user-select: none;
        z-index: 10;
    }

    .password-validation {
        background: #f5f5f5;
        border-radius: 8px;
        padding: 10px 15px;
        margin-top: 8px;
        font-size: 12px;
    }

    .validation-item {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
        color: #999;
    }

    .validation-item.valid {
        color: #4caf50;
    }

    .validation-icon {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
        font-size: 12px;
    }

    .validation-item.valid .validation-icon {
        border-color: #4caf50;
        background: #e8f5e9;
        color: #4caf50;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .checkbox-item input[type="checkbox"] {
        width: auto;
        margin-right: 8px;
        cursor: pointer;
        accent-color: #667eea;
    }

    .checkbox-item label {
        margin-bottom: 0;
        cursor: pointer;
        text-transform: none;
        font-weight: 400;
    }

    .field-error {
        color: #c62828;
        font-size: 12px;
        margin-top: 5px;
    }

    input.error, select.error {
        border-color: #c62828;
    }

    .two-column {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .country-phone-row {
        display: grid;
        grid-template-columns: 1fr 1.6fr;
        gap: 12px;
        align-items: flex-start;
    }

    .country-phone-row .input-group {
        margin-bottom: 0;
    }

    /* Ensure custom-select-wrap inside country-phone-row doesn't overflow */
    .country-phone-row .custom-select-wrap { width: 100%; }

    /* Fix for custom country select inside input-wrapper */
    .input-wrapper .custom-select-wrap {
        position: relative;
        z-index: 2; /* Sit above the ::after background fill */
        width: 100%;
    }

    .input-wrapper .cs-trigger {
        background: transparent !important;
        border: 2px solid #667eea !important;
        border-radius: 8px !important;
        padding: 12px 15px !important;
        color: #1e293b !important;
        min-height: 48px;
        box-shadow: none !important;
        box-sizing: border-box;
    }

    body.dark-mode .input-wrapper .cs-trigger {
        border-color: #334155 !important;
        color: #f1f5f9 !important;
    }

    /* While focused: hide the static border so only the spinning ring shows */
    .input-wrapper.focused .cs-trigger {
        border-color: transparent !important;
        background: transparent !important;
    }

    .input-wrapper .cs-panel {
        z-index: 99999 !important;
    }

    .country-meta {
        margin-top: 10px;
        font-size: 13px;
        color: #555;
        line-height: 1.4;
        white-space: nowrap;
        overflow-x: auto;
    }

    .terms-text {
        font-size: 12px;
        color: #999;
        margin-top: 8px;
    }

    .terms-text a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .btn {
        width: 100%;
        padding: 13px 20px;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .social-divider {
        display: none;
    }
    .social-btns { display: none; }
    .btn-social { display: none; }

    .error-message {
        background: #ffebee;
        color: #c62828;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
        border-left: 4px solid #c62828;
    }

    .success-message {
        background: #e8f5e9;
        color: #2e7d32;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
        border-left: 4px solid #2e7d32;
    }

    .link {
        text-align: center;
        color: #999;
        font-size: 13px;
        margin-top: 20px;
    }

    .link a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        cursor: pointer;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: #667eea;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    @media (max-width: 600px) {
        .auth-page-wrapper {
            padding: 20px 10px;
        }

        .auth-container {
            max-width: 100%;
            border-radius: 15px;
        }

        .auth-header {
            padding: 30px 20px;
        }

        .auth-header h1 {
            font-size: 24px;
        }

        .auth-content {
            padding: 30px 20px;
        }

        .two-column,
        .country-phone-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="auth-page-wrapper">
    <!-- Theme Toggle -->
    <button class="theme-toggle-auth" id="themeToggle" onclick="toggleTheme()">
    <svg id="themeIconDark" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
    <svg id="themeIconLight" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
    <span id="themeLabel">Dark</span>
</button>
<div class="auth-container">
    <div class="auth-header">
        <h1>Welcome</h1>
        <div class="auth-tabs">
            <button type="button" class="auth-tab active" data-tab="login">Login</button>
            <button type="button" class="auth-tab" data-tab="signup">Sign Up</button>
        </div>

        @php
            $googleOn   = \App\Models\SiteSetting::get('google_login_enabled')   === '1';
            $facebookOn = \App\Models\SiteSetting::get('facebook_login_enabled') === '1';
            $appleOn    = \App\Models\SiteSetting::get('apple_login_enabled')    === '1';
        @endphp
        @if($googleOn || $facebookOn || $appleOn)
        <div class="social-header-btns" id="socialHeaderBtns">
            @if($googleOn)
            <a href="{{ route('social.redirect','google') }}" class="btn-social-pill" id="spGoogle">
                <svg class="sp-logo" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                <span class="sp-label" id="spGoogleLabel">Google</span>
            </a>
            @endif
            @if($facebookOn)
            <a href="{{ route('social.redirect','facebook') }}" class="btn-social-pill" id="spFacebook">
                <svg class="sp-logo" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"/></svg>
                <span class="sp-label" id="spFacebookLabel">Facebook</span>
            </a>
            @endif
            @if($appleOn)
            <a href="{{ route('social.redirect','apple') }}" class="btn-social-pill" id="spApple">
                <svg class="sp-logo" viewBox="0 0 24 24"><path d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701" fill="#fff"/></svg>
                <span class="sp-label" id="spAppleLabel">Apple</span>
            </a>
            @endif
        </div>
        @endif
    </div>

    <div class="auth-content">
        @if ($errors->any())
            <div class="error-message">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <!-- LOGIN FORM -->
        <form action="{{ route('login') }}" method="POST" id="loginForm" class="form-section {{ $errors->any() && !old('name') ? 'active' : ($errors->any() ? '' : 'active') }}" onsubmit="return validateLoginForm()">
            @csrf

            <div class="form-group">
                <div class="input-group">
                    <label for="email_phone">Email or Phone</label>
                    <div class="input-wrapper">
                        <div class="input-wrapper-clip"></div>
                        <input type="text" id="email_phone" name="email_phone" placeholder="Enter your email or phone"
                               value="{{ old('email_phone') }}" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <label for="login_password">Password</label>
                    <div class="password-container input-wrapper">
                        <div class="input-wrapper-clip"></div>
                        <input type="password" id="login_password" name="password" placeholder="Enter your password" required>
                        <span class="password-toggle" onclick="togglePassword('login_password')">👁️</span>
                    </div>
                    <div style="text-align:right;margin-top:6px;position:relative;z-index:10;">
                        <a href="{{ route('forgot.password') }}" style="font-size:12px;color:#667eea;font-weight:600;text-decoration:none;">Forgot Password?</a>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>

            @php
                $googleOn   = \App\Models\SiteSetting::get('google_login_enabled')   === '1';
                $facebookOn = \App\Models\SiteSetting::get('facebook_login_enabled') === '1';
                $appleOn    = \App\Models\SiteSetting::get('apple_login_enabled')    === '1';
            @endphp
            @if($googleOn || $facebookOn || $appleOn)
            <div class="social-divider">or continue with</div>
            <div class="social-btns">
                @if($googleOn)
                <a href="{{ route('social.redirect','google') }}" class="btn-social btn-social-google">
                    <div class="social-logo-wrap">
                        <svg class="social-logo" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                        <span class="social-label">Login with Google</span>
                    </div>
                </a>
                @endif
                @if($facebookOn)
                <a href="{{ route('social.redirect','facebook') }}" class="btn-social btn-social-facebook">
                    <div class="social-logo-wrap">
                        <svg class="social-logo" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"/></svg>
                        <span class="social-label">Login with Facebook</span>
                    </div>
                </a>
                @endif
                @if($appleOn)
                <a href="{{ route('social.redirect','apple') }}" class="btn-social btn-social-apple">
                    <div class="social-logo-wrap">
                        <svg class="social-logo" viewBox="0 0 24 24"><path d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701" fill="#000"/></svg>
                        <span class="social-label">Login with Apple</span>
                    </div>
                </a>
                @endif
            </div>
            @endif

            <div style="text-align: center; margin-top: 15px;">
                <a href="/" class="back-link">← Back to Home</a>
            </div>
        </form>

        <!-- SIGNUP FORM -->
        <form action="{{ route('register') }}" method="POST" id="signupForm" class="form-section {{ $errors->any() && old('name') ? 'active' : '' }}" onsubmit="return validateSignupForm()">
            @csrf

            <div class="form-group">
                <div class="input-group">
                    <label for="name">Full Name</label>
                    <div class="input-wrapper">
                        <div class="input-wrapper-clip"></div>
                        <input type="text" id="name" name="name" placeholder="John Doe" value="{{ old('name') }}" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="country-phone-row">
                    <div class="input-group">
                        <label for="country">Country</label>
                        <div class="input-wrapper">
                            <div class="input-wrapper-clip"></div>
                            <select id="country" name="country_id" data-type="country-code" required>
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country['id'] }}"
                                            data-phone-code="{{ $country['phone_code'] ?? '' }}"
                                            data-name="{{ $country['country_name'] }}"
                                            {{ old('country_id') == $country['id'] ? 'selected' : '' }}>
                                        {{ $country['country_name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="phone">Phone Number</label>
                        <div class="input-wrapper">
                            <div class="input-wrapper-clip"></div>
                            <input type="tel" id="phone" name="phone" placeholder="555 123 4567" value="{{ old('phone') }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <div class="input-wrapper">
                        <div class="input-wrapper-clip"></div>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <label for="gender">Gender</label>
                    <div class="input-wrapper">
                        <div class="input-wrapper-clip"></div>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <div class="input-wrapper-clip"></div>
                        <input type="email" id="email" name="email" placeholder="john@example.com" value="{{ old('email') }}" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="password-container input-wrapper">
                        <div class="input-wrapper-clip"></div>
                        <input type="password" id="password" name="password" placeholder="Create a strong password"
                               oninput="validatePassword(this.value)" required>
                        <span class="password-toggle" onclick="togglePassword('password')">👁️</span>
                    </div>
                    <div class="password-validation" id="passwordValidation">
                        <div class="validation-item" id="length-check"><span class="validation-icon">✓</span> At least 8 characters</div>
                        <div class="validation-item" id="uppercase-check"><span class="validation-icon">✓</span> At least 1 uppercase letter</div>
                        <div class="validation-item" id="lowercase-check"><span class="validation-icon">✓</span> At least 1 lowercase letter</div>
                        <div class="validation-item" id="number-check"><span class="validation-icon">✓</span> At least 1 number</div>
                        <div class="validation-item" id="symbol-check"><span class="validation-icon">✓</span> At least 1 special character</div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <label for="password_confirm">Confirm Password</label>
                    <div class="password-container input-wrapper">
                        <div class="input-wrapper-clip"></div>
                        <input type="password" id="password_confirm" name="password_confirm" placeholder="Confirm your password" required>
                        <span class="password-toggle" onclick="togglePassword('password_confirm')">👁️</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-item">
                    <input type="checkbox" id="terms" name="terms" value="on" {{ old('terms') ? 'checked' : '' }}>
                    <label for="terms" style="margin-bottom:0;">
                        I agree to the <a href="#" style="color:#667eea;">Terms &amp; Conditions</a> and <a href="#" style="color:#667eea;">Privacy Policy</a>
                    </label>
                </div>
                <div id="terms-error" class="field-error"></div>
            </div>

            <button type="submit" class="btn btn-primary">Create Account</button>

            @if($googleOn || $facebookOn || $appleOn)
            <div class="social-divider">or sign up with</div>
            <div class="social-btns">
                @if($googleOn)
                <a href="{{ route('social.redirect','google') }}" class="btn-social btn-social-google">
                    <div class="social-logo-wrap"><svg class="social-logo" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg><span class="social-label">Signup with Google</span></div>
                </a>
                @endif
                @if($facebookOn)
                <a href="{{ route('social.redirect','facebook') }}" class="btn-social btn-social-facebook">
                    <div class="social-logo-wrap"><svg class="social-logo" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"/></svg><span class="social-label">Signup with Facebook</span></div>
                </a>
                @endif
                @if($appleOn)
                <a href="{{ route('social.redirect','apple') }}" class="btn-social btn-social-apple">
                    <div class="social-logo-wrap"><svg class="social-logo" viewBox="0 0 24 24"><path d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701" fill="#000"/></svg><span class="social-label">Signup with Apple</span></div>
                </a>
                @endif
            </div>
            @endif

            <div style="text-align:center;margin-top:15px;">
                <a href="/" class="back-link">← Back to Home</a>
            </div>
        </form>
    </div>
</div>
</div>

@endsection

@section('scripts')
<script>
    // Theme logic is already handled in header.blade.php
    // We just need to sync the login page specific label if needed, but the button has the same ID.
    // Let's just override the applyTheme function if we want to change the label, or rename the constant.
    const AUTH_THEME_KEY = 'site_theme';

    function applyAuthTheme(theme) {
        document.body.classList.toggle('dark-mode', theme === 'dark');
        const dIcon = document.getElementById('themeIconDark');
        const lIcon = document.getElementById('themeIconLight');
        const label = document.getElementById('themeLabel');
        
        if(dIcon) dIcon.style.display = theme === 'dark' ? 'block' : 'none';
        if(lIcon) lIcon.style.display = theme === 'dark' ? 'none'  : 'block';
        if(label) label.textContent   = theme === 'dark' ? 'Light' : 'Dark';
    }

    // Override the global toggleTheme if called from auth page
    window.toggleTheme = function() {
        const next = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
        localStorage.setItem(AUTH_THEME_KEY, next);
        applyAuthTheme(next);
    };

    applyAuthTheme(localStorage.getItem(AUTH_THEME_KEY) || 'light');

    // ── Active tab sync on page load (handles server-side errors) ────────────
    (function() {
        const signupActive = document.getElementById('signupForm').classList.contains('active');
        if (signupActive) {
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            document.querySelector('[data-tab="signup"]').classList.add('active');
        }
    })();

    // ── Social pill reveal animation ──────────────────────────────────────────
    function revealSocialPills(delay) {
        const pills = document.querySelectorAll('.btn-social-pill');
        pills.forEach(btn => {
            btn.classList.remove('revealed');
            // Force browser to register the removal before re-adding
            void btn.offsetWidth;
        });
        pills.forEach((btn, i) => {
            setTimeout(() => btn.classList.add('revealed'), (delay || 0) + i * 160);
        });
    }

    // Initial reveal on page load — after header animation settles
    setTimeout(() => revealSocialPills(0), 600);

    // Show toast from query param
    (function() {
        const p = new URLSearchParams(window.location.search).get('toast');
        if (!p) return;
        const box = document.createElement('div');
        box.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;background:#dcfce7;color:#15803d;border-left:4px solid #16a34a;padding:14px 18px;border-radius:12px;font-size:14px;font-weight:500;box-shadow:0 8px 30px rgba(0,0,0,0.15);max-width:360px;display:flex;align-items:center;gap:10px;';
        box.innerHTML = '<span>✅</span><span style="flex:1">' + decodeURIComponent(p) + '</span><button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;font-size:16px;opacity:0.6;">✕</button>';
        document.body.appendChild(box);
        setTimeout(() => box.remove(), 6000);
        window.history.replaceState({}, '', window.location.pathname);
    })();

    // ── Single unified tab switch handler ────────────────────────────────────
    document.querySelectorAll('.auth-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            const loginForm  = document.getElementById('loginForm');
            const signupForm = document.getElementById('signupForm');
            const isAlreadyActive =
                (tabName === 'login'  && loginForm.classList.contains('active')) ||
                (tabName === 'signup' && signupForm.classList.contains('active'));
            if (isAlreadyActive) return;

            // 1. Update tab highlight
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // 2. Collapse social pills immediately
            document.querySelectorAll('.btn-social-pill').forEach(btn => {
                btn.classList.remove('revealed');
                void btn.offsetWidth; // force reflow so transition resets
            });

            // 3. Animate form out
            const outForm = tabName === 'signup' ? loginForm  : signupForm;
            const inForm  = tabName === 'signup' ? signupForm : loginForm;

            outForm.classList.add('exit-animation');
            setTimeout(() => {
                outForm.classList.remove('active', 'exit-animation');
                inForm.classList.add('active');

                // 4. Re-reveal pills after new form slides in (700ms slide-up)
                setTimeout(() => revealSocialPills(0), 400);
            }, 500);
        });
    });

    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        if (field.type === 'password') {
            field.type = 'text';
        } else {
            field.type = 'password';
        }
    }

    // Validate password
    function validatePassword(password) {
        const checks = {
            '#length-check': password.length >= 8,
            '#uppercase-check': /[A-Z]/.test(password),
            '#lowercase-check': /[a-z]/.test(password),
            '#number-check': /\d/.test(password),
            '#symbol-check': /[@$!%*?&]/.test(password)
        };

        Object.keys(checks).forEach(selector => {
            const element = document.querySelector(selector);
            if (checks[selector]) {
                element.classList.add('valid');
            } else {
                element.classList.remove('valid');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.input-wrapper input, .input-wrapper select, .input-wrapper textarea, .input-wrapper .cs-trigger').forEach(el => {
            el.addEventListener('focus', () => el.closest('.input-wrapper').classList.add('focused'));
            el.addEventListener('blur',  () => el.closest('.input-wrapper').classList.remove('focused'));
        });
    });

    // Age validation for date of birth
    document.getElementById('date_of_birth')?.addEventListener('change', function() {
        const birthDate = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        if (age < 18) {
            alert('You must be at least 18 years old.');
            this.value = '';
        }
    });

    function showError(id, msg) {
        const el = document.getElementById(id);
        const container = el.closest('.input-group') || el.parentElement;
        let err = container.querySelector('.field-error');
        if (!err) {
            err = document.createElement('div');
            err.className = 'field-error';
            container.appendChild(err);
        }
        err.textContent = msg;
        el.classList.add('error');
    }

    function clearError(id) {
        const el = document.getElementById(id);
        const container = el.closest('.input-group') || el.parentElement;
        const err = container.querySelector('.field-error');
        if (err) err.textContent = '';
        el.classList.remove('error');
    }

    function validateLoginForm() {
        let valid = true;
        const emailPhone = document.getElementById('email_phone').value.trim();
        const password = document.getElementById('login_password').value;

        if (!emailPhone) { showError('email_phone', 'Please enter your email or phone number.'); valid = false; }
        else clearError('email_phone');

        if (!password) { showError('login_password', 'Please enter your password.'); valid = false; }
        else clearError('login_password');

        return valid;
    }

function validateSignupForm() {
        let valid = true;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        const name = document.getElementById('name').value.trim();
        if (!name) { showError('name', 'Please enter your full name.'); valid = false; }
        else clearError('name');

        const country = document.getElementById('country').value;
        if (!country) { showError('country', 'Please select your country.'); valid = false; }
        else clearError('country');

        const phone = document.getElementById('phone').value.trim();
        if (!phone) { showError('phone', 'Please enter your phone number.'); valid = false; }
        else clearError('phone');

        const dob = document.getElementById('date_of_birth').value;
        if (!dob) { showError('date_of_birth', 'Please select your date of birth.'); valid = false; }
        else clearError('date_of_birth');

        const gender = document.getElementById('gender').value;
        if (!gender) { showError('gender', 'Please select your gender.'); valid = false; }
        else clearError('gender');

        const email = document.getElementById('email').value.trim();
        if (!email) { showError('email', 'Please enter your email address.'); valid = false; }
        else if (!emailRegex.test(email)) { showError('email', 'Please enter a valid email address.'); valid = false; }
        else clearError('email');

        const password = document.getElementById('password').value;
        if (!password) { showError('password', 'Please enter a password.'); valid = false; }
        else if (password.length < 8) { showError('password', 'Password must be at least 8 characters.'); valid = false; }
        else if (!/[A-Z]/.test(password)) { showError('password', 'Password must contain at least 1 uppercase letter.'); valid = false; }
        else if (!/[a-z]/.test(password)) { showError('password', 'Password must contain at least 1 lowercase letter.'); valid = false; }
        else if (!/\d/.test(password)) { showError('password', 'Password must contain at least 1 number.'); valid = false; }
        else if (!/[@$!%*?&]/.test(password)) { showError('password', 'Password must contain at least 1 special character (@$!%*?&).'); valid = false; }
        else clearError('password');

        const passwordConfirm = document.getElementById('password_confirm').value;
        if (!passwordConfirm) { showError('password_confirm', 'Please confirm your password.'); valid = false; }
        else if (password !== passwordConfirm) { showError('password_confirm', 'Passwords do not match.'); valid = false; }
        else clearError('password_confirm');

        const terms = document.getElementById('terms');
        const termsErr = document.getElementById('terms-error');
        if (!terms.checked) {
            termsErr.textContent = 'You must agree to the Terms & Conditions and Privacy Policy.';
            valid = false;
        } else {
            termsErr.textContent = '';
        }

        return valid;
    }
</script>
@endsection
