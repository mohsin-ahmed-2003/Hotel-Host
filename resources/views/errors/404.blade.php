<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
        }

        /* Animated background blobs */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 8s ease-in-out infinite;
        }

        .blob-1 { width: 500px; height: 500px; background: #667eea; top: -100px; left: -100px; animation-delay: 0s; }
        .blob-2 { width: 400px; height: 400px; background: #764ba2; bottom: -80px; right: -80px; animation-delay: 3s; }
        .blob-3 { width: 300px; height: 300px; background: #a78bfa; top: 50%; left: 50%; animation-delay: 1.5s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(30px, -30px) scale(1.05); }
        }

        .container {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .number-wrap {
            position: relative;
            display: inline-block;
            margin-bottom: 8px;
        }

        .number {
            font-size: clamp(120px, 20vw, 200px);
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, #667eea, #a78bfa, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 3s ease-in-out infinite;
            background-size: 200% 200%;
        }

        @keyframes shimmer {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glitch {
            position: relative;
            display: inline-block;
        }

        .glitch::before,
        .glitch::after {
            content: '404';
            position: absolute;
            top: 0; left: 0;
            background: linear-gradient(135deg, #667eea, #a78bfa, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glitch::before {
            animation: glitch1 4s infinite;
            clip-path: polygon(0 0, 100% 0, 100% 35%, 0 35%);
        }

        .glitch::after {
            animation: glitch2 4s infinite;
            clip-path: polygon(0 65%, 100% 65%, 100% 100%, 0 100%);
        }

        @keyframes glitch1 {
            0%, 90%, 100% { transform: translate(0); }
            92%            { transform: translate(-4px, 2px); }
            94%            { transform: translate(4px, -2px); }
            96%            { transform: translate(-2px, 0); }
        }

        @keyframes glitch2 {
            0%, 90%, 100% { transform: translate(0); }
            92%            { transform: translate(4px, -2px); }
            94%            { transform: translate(-4px, 2px); }
            96%            { transform: translate(2px, 0); }
        }

        .icon-wrap {
            width: 72px; height: 72px;
            background: rgba(102,126,234,0.15);
            border: 1px solid rgba(102,126,234,0.3);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-8px); }
        }

        h1 {
            font-size: 28px;
            font-weight: 800;
            color: #f1f5f9;
            margin-bottom: 12px;
        }

        p {
            font-size: 16px;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 40px;
        }

        .btn-group {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 28px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            box-shadow: 0 8px 24px rgba(102,126,234,0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(102,126,234,0.5);
        }

        .btn-ghost {
            background: rgba(255,255,255,0.06);
            color: #94a3b8;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .btn-ghost:hover {
            background: rgba(255,255,255,0.1);
            color: #f1f5f9;
            transform: translateY(-2px);
        }

        .divider {
            width: 60px; height: 3px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 2px;
            margin: 0 auto 24px;
        }

        @media (max-width: 480px) {
            h1 { font-size: 22px; }
            .btn-group { flex-direction: column; align-items: center; }
        }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="container">
        <div class="number-wrap">
            <div class="number glitch">404</div>
        </div>

        <div class="icon-wrap">
            <svg width="32" height="32" fill="none" stroke="#a78bfa" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
                <line x1="11" y1="8" x2="11" y2="11"/>
                <line x1="11" y1="14" x2="11.01" y2="14"/>
            </svg>
        </div>

        <div class="divider"></div>
        <h1>Page Not Found</h1>
        <p>The page you're looking for doesn't exist or has been moved.<br>Let's get you back on track.</p>

        <div class="btn-group">
            <a href="/" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Go Home
            </a>
            <button onclick="history.back()" class="btn btn-ghost">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Go Back
            </button>
        </div>
    </div>
</body>
</html>
