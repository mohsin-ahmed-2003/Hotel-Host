<style>
    /* ── Theme Variables ── */
    :root {
        /* Light Mode */
        --header-bg: #1E3A8A;
        --header-border: #1e40af;
        --header-text: #e8edf8;
        --header-muted: #93afd4;
        --header-hover: rgba(255, 255, 255, 0.09);
        --header-shadow: 0 2px 20px rgba(10, 20, 60, 0.22);

        --body-bg: #F8FAFC;
        --body-text: #0f172a;
        --body-muted: #64748b;

        --footer-bg: #1E3A8A;
        --footer-border: #1f2937;
        --footer-text: #e8edf8;
        --footer-heading: #d1d5db;
        --footer-link: #e8edf8;
        --footer-bottom: #030712;

        --accent: #2563eb;
        --accent-hover: #1d4ed8;
        --accent-light: #dbeafe;
        --accent-gold: #f59e0b;

        --card-bg: #ffffff;
        --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        --border: #e2e8f0;
        --input-bg: #ffffff;
        --input-border: #cbd5e1;
        --shadow-sm: 0 1px 4px rgba(15, 23, 42, 0.07);
        --shadow-md: 0 4px 20px rgba(15, 23, 42, 0.10);

        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
    }

    body.dark-mode {
        /* Dark Mode */
        --header-bg: #1e3b8a;
        --header-border: #111827;
        --header-text: #f1f5f9;
        --header-muted: #93afd4;
        --header-hover: rgba(255, 255, 255, 0.06);
        --header-shadow: 0 2px 24px rgba(0, 0, 0, 0.5);

        --body-bg: #0f172a;
        --body-text: #f1f5f9;
        --body-muted: #94a3b8;

        --footer-bg: #1E3A8A;
        --footer-border: #1e293b;
        --footer-text: #f1f5f9;
        --footer-heading: #94a3b8;
        --footer-link: #f1f5f9;
        --footer-bottom: #000000;

        --accent: #3b82f6;
        --accent-hover: #2563eb;
        --accent-light: rgba(59, 130, 246, 0.12);
        --accent-gold: #d97706;

        --card-bg: #1e293b;
        --card-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
        --border: #334155;
        --input-bg: #0f172a;
        --input-border: #334155;
        --shadow-sm: 0 1px 4px rgba(0, 0, 0, 0.4);
        --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.6);
    }

    body {
        background-color: var(--body-bg);
        color: var(--body-text);
        transition: background-color 0.3s, color 0.3s;
    }

    /* ── Header ── */
    .header-container {
        background: var(--header-bg);
        border-bottom: 1px solid var(--header-border);
        color: var(--header-text);
        padding: 14px 0;
        box-shadow: var(--header-shadow);
        position: sticky;
        top: 0;
        z-index: 500;
        /* position:relative needed so absolute mobile-nav positions under header */
        /* sticky already implies a stacking context */
        transition: background 0.3s, border-color 0.3s, box-shadow 0.3s;
    }

    .header-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .site-logo {
        font-size: 22px;
        font-weight: 800;
        text-decoration: none;
        color: #ffffff;
        flex-shrink: 0;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .site-logo-icon {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .site-logo span {
        color: var(--accent-gold);
    }

    .site-name-text {
        color: #ffffff;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -0.3px;
        white-space: nowrap;
    }

    .header-search {
        flex: 1;
        max-width: 340px;
    }

    .header-search input {
        width: 90%;
        padding: 9px 16px 9px 38px;
        border: 1px solid var(--header-border);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.07);
        color: var(--header-text);
        font-size: 13px;
        outline: none;
        font-family: inherit;
        transition: all 0.2s;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%238fa3c8' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 12px center;
    }

    .header-search input::placeholder {
        color: var(--header-muted);
    }

    .header-search input:focus {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
    }

    .homenavbar {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    /* Theme btn */
    .theme-toggle-btn {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--header-border);
        cursor: pointer;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        color: var(--header-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .theme-toggle-btn:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    /* Pill Menu */
    .user-menu-pill {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 5px 5px 5px 14px;
        border: 1px solid var(--header-border);
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.03);
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .user-menu-pill:hover {
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .pill-hamburger {
        color: var(--header-muted);
        display: flex;
        align-items: center;
    }

    .pill-profile-img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        background: var(--header-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
    }

    .header-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        min-width: 240px;
        z-index: 1000;
        animation: slideDown 0.2s ease;
    }

    .header-dropdown.open {
        display: block;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .header-dropdown-info {
        padding: 16px;
        border-bottom: 1px solid var(--border);
        background: #1E3A8A;
    }

    .header-dropdown-name {
        font-size: 15px;
        font-weight: 700;
        color: #ffffff;
    }

    .header-dropdown-email {
        font-size: 13px;
        color: #93afd4;
        margin-top: 2px;
    }

    .header-dropdown a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        font-size: 14px;
        font-weight: 500;
        color: var(--body-text);
        text-decoration: none;
        transition: background 0.15s;
    }

    .header-dropdown a:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    body.dark-mode .header-dropdown a:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .header-dropdown a.danger {
        color: #dc2626;
    }

    body.dark-mode .header-dropdown a.danger {
        color: #f87171;
    }

    .header-dropdown a.danger:hover {
        background: rgba(220, 38, 38, 0.06);
    }

    .header-dropdown-divider {
        height: 1px;
        background: var(--border);
        margin: 4px 0;
    }

    @media (max-width: 1150px) {
        .header-content {
            padding: 0 12px;
            gap: 8px;
        }

        .site-name-text {
            font-size: 16px;
        }

        .header-search input {
            width: 100%;
            padding: 8px 12px 8px 28px;
            font-size: 12px;
            background-position: 8px center;
        }
    }

    @media (max-width: 768px) {
        .header-container {
            position: sticky;
        }

        .header-search {
            display: none;
        }

        .header-site-logo-img {
            height: 36px !important;
            max-width: 120px !important;
        }

        .site-name-text {
            font-size: 16px;
        }

        .desktop-only-btn {
            display: none !important;
        }
    }
</style>

<header class="header-container">
    <div class="header-content">
        <a href="/" class="site-logo">
            @if($siteSettings->get('site_logo'))
                <img src="{{ \Illuminate\Support\Facades\Storage::url($siteSettings->get('site_logo')) }}"
                    alt="{{ $siteSettings->get('site_name', 'Hotel Host') }}" class="header-site-logo-img"
                    style="height:48px;width:auto;max-width:160px;object-fit:contain;">
            @else
                <div class="site-logo-icon">🏨</div>
            @endif
            <span class="site-name-text">{{ $siteSettings->get('site_name', 'Hotel Host') }}</span>
        </a>

        <div class="header-search">
            <input type="text" placeholder="Search destinations, hotels...">
        </div>

        <nav class="homenavbar">
            @if(session('user_id'))
                <a href="{{ route('host.start') }}" class="desktop-only-btn"
                    style="background:var(--accent);color:#fff;padding:10px 18px;border-radius:24px;font-size:13px;font-weight:700;text-decoration:none;transition:background 0.2s;margin-right:8px;">Host
                    Property</a>
                <a href="/wishlist" class="desktop-only-btn"
                    style="background:transparent;border:1px solid rgba(255,255,255,0.15);color:var(--header-text);padding:10px 18px;border-radius:24px;font-size:13px;font-weight:700;text-decoration:none;transition:all 0.2s;margin-right:8px;display:inline-flex;align-items:center;gap:6px;"><i
                        class="fa-regular fa-heart" style="color:#f87171;"></i> Wishlist</a>
            @endif

            <button class="theme-toggle-btn" id="themeToggle" onclick="toggleTheme()" title="Toggle theme">
                <svg id="themeIconDark" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                </svg>
                <svg id="themeIconLight" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" style="display:none">
                    <circle cx="12" cy="12" r="5" />
                    <line x1="12" y1="1" x2="12" y2="3" />
                    <line x1="12" y1="21" x2="12" y2="23" />
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                    <line x1="1" y1="12" x2="3" y2="12" />
                    <line x1="21" y1="12" x2="23" y2="12" />
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                </svg>
            </button>

            @if(session('user_id'))
                <div class="user-menu-pill" id="headerProfileWrap" onclick="toggleHeaderDropdown()">
                    <div class="pill-hamburger">
                        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="presentation"
                            focusable="false"
                            style="display: block; fill: none; height: 16px; width: 16px; stroke: currentcolor; stroke-width: 3; overflow: visible;">
                            <g fill="none" fill-rule="nonzero">
                                <path d="m2 16h28"></path>
                                <path d="m2 24h28"></path>
                                <path d="m2 8h28"></path>
                            </g>
                        </svg>
                    </div>
                    <img src="{{ asset(session('user')->profile_image ?? 'images/image.png') }}" alt="Profile"
                        class="pill-profile-img">

                    <div class="header-dropdown" id="headerDropdown">
                        <div class="header-dropdown-info">
                            <div class="header-dropdown-name">{{ session('user')->name ?? 'User' }}</div>
                            <div class="header-dropdown-email">{{ session('user')->email ?? '' }}</div>
                        </div>
                        <!-- <a href="/">Home</a> -->
                        <a href="/profile">My Profile</a>
                        <a href="/dashboard">Dashboard</a>
                        <a href="/trips">Trips</a>
                        <a href="/reservations">Reservations</a>
                        <a href="/wishlist">Wishlist</a>
                        <a href="/subscriptions">Subscription plan</a>
                        <a href="{{ route('user.properties') }}">Your Property</a>
                        <div class="header-dropdown-divider"></div>
                        <div class="header-dropdown-divider"></div>
                        <a href="{{ route('logout') }}" class="danger">Logout</a>
                    </div>
                </div>
            @else
                <div class="user-menu-pill" id="headerProfileWrap" onclick="toggleHeaderDropdown()">
                    <div class="pill-hamburger">
                        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="presentation"
                            focusable="false"
                            style="display: block; fill: none; height: 16px; width: 16px; stroke: currentcolor; stroke-width: 3; overflow: visible;">
                            <g fill="none" fill-rule="nonzero">
                                <path d="m2 16h28"></path>
                                <path d="m2 24h28"></path>
                                <path d="m2 8h28"></path>
                            </g>
                        </svg>
                    </div>
                    <div class="pill-profile-img">
                        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"
                            style="display: block; height: 16px; width: 16px; fill: currentcolor;">
                            <path
                                d="m16 .7c-8.437 0-15.3 6.863-15.3 15.3s6.863 15.3 15.3 15.3 15.3-6.863 15.3-15.3-6.863-15.3-15.3-15.3zm0 28c-4.021 0-7.605-1.884-9.933-4.81a12.425 12.425 0 0 1 6.451-4.4 6.507 6.507 0 0 1 -3.018-5.49c0-3.584 2.916-6.5 6.5-6.5s6.5 2.916 6.5 6.5a6.513 6.513 0 0 1 -3.019 5.491 12.42 12.42 0 0 1 6.452 4.4c-2.328 2.925-5.912 4.809-9.933 4.809z">
                            </path>
                        </svg>
                    </div>

                    <div class="header-dropdown" id="headerDropdown">
                        <a href="{{ route('auth') }}" style="font-weight:700;">Login / Register</a>
                        <div class="header-dropdown-divider"></div>
                        <a href="/">Help</a>
                    </div>
                </div>
            @endif
        </nav>
    </div>
</header>

<script>
    const THEME_KEY = 'site_theme';

    function applyTheme(theme) {
        document.body.classList.toggle('dark-mode', theme === 'dark');
        const d = document.getElementById('themeIconDark');
        const l = document.getElementById('themeIconLight');
        if (d) d.style.display = theme === 'dark' ? 'block' : 'none';
        if (l) l.style.display = theme === 'dark' ? 'none' : 'block';
    }

    function toggleTheme() {
        const next = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
        localStorage.setItem(THEME_KEY, next);
        applyTheme(next);
    }

    applyTheme(localStorage.getItem(THEME_KEY) || 'light');

    function toggleHeaderDropdown() {
        document.getElementById('headerDropdown')?.classList.toggle('open');
    }

    document.addEventListener('click', function (e) {
        const wrap = document.getElementById('headerProfileWrap');
        const dd = document.getElementById('headerDropdown');
        if (wrap && dd && !wrap.contains(e.target)) dd.classList.remove('open');
    });
</script>