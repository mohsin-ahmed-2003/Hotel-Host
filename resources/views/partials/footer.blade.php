<style>
    .site-footer {
        background: var(--footer-bg, #111827);
        border-top: 1px solid var(--footer-border, #1f2937);
        color: var(--footer-text, #6b7280);
        padding: 48px 24px 20px;
        margin-top: auto;
        transition: background 0.3s, border-color 0.3s;
    }

    .footer-inner {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 40px;
        padding-bottom: 32px;
        border-bottom: 1px solid var(--footer-border, #1a2744);
    }

    .footer-brand {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }

    .footer-brand-icon {
        width: 30px;
        height: 30px;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .footer-brand-name {
        font-size: 18px;
        font-weight: 800;
        color: var(--footer-heading, #c8d8f0);
        letter-spacing: -0.3px;
    }

    .footer-brand-name span {
        color: #f59e0b;
    }

    .footer-desc {
        font-size: 13px;
        line-height: 1.75;
        color: var(--footer-text, #8fa3c8);
        max-width: 240px;
        margin-bottom: 20px;
    }

    .footer-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .footer-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        border: 1px solid rgba(182, 197, 227, 0.45);
        /* color: #6b8ab8; */
        background: rgba(37, 99, 235, 0.06);
    }

    .footer-col-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--footer-heading, #f1f5f9);
        margin-bottom: 10px;
        position: relative;
        padding-bottom: 10px;
    }

    .footer-col-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 24px;
        height: 3px;
        background: #3b82f6;
        border-radius: 2px;
    }

    .footer-links {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 6px;
        padding: 0;
        margin: 0;
    }

    .footer-links a {
        font-size: 13.5px;
        color: var(--footer-link, #94a3b8);
        text-decoration: none;
        transition: color 0.3s ease;
        display: inline-block;
        padding: 2px 0;
        position: relative;
        width: fit-content;
    }

    .footer-links a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 1.5px;
        background: #60a5fa;
        transition: width 0.3s ease;
    }

    .footer-links a:hover {
        color: #f1f5f9;
    }

    .footer-links a:hover::after {
        width: 100%;
    }

    .footer-bottom-wrap {
        background: var(--footer-bottom, #030712);
        border-top: 1px solid var(--footer-border, #1f2937);
        padding: 16px 24px;
        transition: background 0.3s;
    }

    .footer-bottom {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
        color: #93afd4;
        flex-wrap: wrap;
        gap: 8px;
    }

    .footer-bottom-links {
        display: flex;
        gap: 16px;
    }

    .footer-bottom-links a {
        color: #93afd4;
        text-decoration: none;
        font-size: 12px;
        transition: color 0.2s;
    }

    .footer-bottom-links a:hover {
        color: #c8d8f0;
    }

    @media (max-width: 900px) {
        .footer-inner {
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
        }

        .footer-inner>div:first-child {
            grid-column: 1 / -1;
        }

        .footer-desc {
            max-width: 100%;
        }
    }

    @media (max-width: 500px) {
        .footer-inner {
            grid-template-columns: repeat(2, 1fr);
            gap: 24px 16px;
        }

        .footer-bottom {
            justify-content: center;
            text-align: center;
            flex-direction: column;
            gap: 12px;
        }

        .footer-bottom-links {
            justify-content: center;
            flex-wrap: wrap;
        }
    }
</style>

<footer class="site-footer">
    <div class="footer-inner">
        <div>
            <div class="footer-brand">
                @if($siteSettings->get('site_logo'))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($siteSettings->get('site_logo')) }}"
                        alt="{{ $siteSettings->get('site_name', 'Hotel Host') }}"
                        style="height:48px;width:auto;max-width:160px;object-fit:contain;">
                @else
                    <div class="footer-brand-icon">🏨</div>
                @endif
                <div class="footer-brand-name">{{ $siteSettings->get('site_name', 'Hotel Host') }}</div>
            </div>
            <p class="footer-desc">Discover and book premium accommodations worldwide. Trusted by millions of travelers
                for seamless, secure hotel reservations.</p>
            <div class="footer-badges">
                <span class="footer-badge">🔒 Secure Booking</span>
                <span class="footer-badge">✈️ Best Price</span>
                <span class="footer-badge">⭐ Top Rated</span>
            </div>
        </div>
        <div>
            <div class="footer-col-title">Explore</div>
            <ul class="footer-links">
                <li><a href="/">Home</a></li>
                <li><a href="/dashboard">Dashboard</a></li>
                <li><a href="/trips">My Trips</a></li>
                <li><a href="/reservations">Reservations</a></li>
            </ul>
        </div>
        <div>
            <div class="footer-col-title">Account</div>
            <ul class="footer-links">
                <li><a href="/profile">My Profile</a></li>
                <li><a href="{{ route('auth') }}">Login</a></li>
                <li><a href="{{ route('auth') }}">Register</a></li>
                <li><a href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </div>
        <div>
            <div class="footer-col-title">Support</div>
            <ul class="footer-links">
                <li><a href="#">Help Center</a></li>
                <li><a href="#">Contact Us</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
            </ul>
        </div>
    </div>
</footer>
<div class="footer-bottom-wrap">
    <div class="footer-bottom">
        <span>© {{ date('Y') }} {{ $siteSettings->get('site_name', 'Hotel Host') }}. All rights reserved.</span>
        <div class="footer-bottom-links">
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <a href="#">Cookies</a>
        </div>
    </div>
</div>