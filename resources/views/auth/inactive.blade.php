<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Inactive</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: #fff;
            border-radius: 24px;
            padding: 56px 48px;
            max-width: 520px;
            width: 100%;
            text-align: center;
            box-shadow: 0 32px 80px rgba(0,0,0,0.25);
            animation: popIn 0.6s cubic-bezier(0.34,1.56,0.64,1) forwards;
        }

        @keyframes popIn {
            from { opacity: 0; transform: scale(0.85) translateY(30px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .icon-wrap {
            width: 90px; height: 90px;
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.3); }
            50%       { box-shadow: 0 0 0 16px rgba(239,68,68,0); }
        }

        .icon-wrap svg { color: #ef4444; }

        .oops {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #ef4444;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 28px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .username {
            font-size: 16px;
            color: #667eea;
            font-weight: 700;
            margin-bottom: 20px;
        }

        p {
            font-size: 15px;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .btn-support {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 8px 24px rgba(102,126,234,0.4);
        }

        .btn-support:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(102,126,234,0.5);
        }

        .back-link {
            display: block;
            margin-top: 20px;
            font-size: 13px;
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover { color: #667eea; }

        @media (max-width: 480px) {
            .card { padding: 40px 24px; }
            h1 { font-size: 22px; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>

        <div class="oops">Oops!</div>
        <h1>Account Inactive</h1>
        <div class="username">Hi, {{ $name }}</div>

        <p>
            Sorry, your account has been deactivated and you are currently unable to access the platform.
            Please reach out to our support team and we'll be happy to help you resolve this.
        </p>

        <a href="mailto:support@hotelhost.com" class="btn-support">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
            </svg>
            Help &amp; Support
        </a>

        <a href="{{ route('auth') }}" class="back-link">← Back to Login</a>
    </div>
</body>
</html>
