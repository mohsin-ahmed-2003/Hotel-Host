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

    /* Header version of the hero search bar */
    .header-search {
        flex: 1;
        max-width: 700px;
        opacity: 0;
        transform: translateY(15px) scale(0.95);
        pointer-events: none;
        transition: opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1), transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        margin: 0 16px;
    }

    .header-search.active {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    .header-search .hero-search-bar-wrap {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 40px;
        padding: 4px 6px 4px 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        max-width: 100%;
        margin: 0 auto;
        position: relative;
    }

    body.dark-mode .header-search .hero-search-bar-wrap {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .header-search .hero-search-form {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .header-search .search-field {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        flex: 1;
        min-width: 80px;
    }

    .header-search .field-label {
        font-size: 9px;
        font-weight: 800;
        color: #ffffff;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .header-search .field-label i,
    .header-search .field-label svg {
        font-size: 11px;
        color: var(--accent);
    }

    .header-search .field-input { background: transparent; border: none; color: var(--header-text) !important; font-size: 13px; font-weight: 700; outline: none; width: 100%; padding: 0; }
    
    .header-search select.field-input {
        background: transparent !important;
        color: inherit !important;
        border: none !important;
        outline: none !important;
    }

    .header-search .field-input::placeholder {
        color: var(--header-muted) !important;
        font-weight: 400;
    }

    .header-search .field-divider {
        width: 1px;
        height: 24px;
        background: rgba(255, 255, 255, 0.15);
    }

    body.dark-mode .header-search .field-divider {
        background: rgba(255, 255, 255, 0.08);
    }

    .header-search .search-submit-btn {
        background: var(--accent);
        border: none;
        border-radius: 50% !important;
        width: 36px !important;
        height: 36px !important;
        min-width: 36px !important;
        min-height: 36px !important;
        padding: 0 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
        box-sizing: border-box !important;
    }

    .header-search .search-submit-btn:hover {
        background: var(--accent-hover);
        transform: scale(1.05);
    }

    /* ── Styled Custom Select Dropdown Overrides inside Search Bar ── */
    .hero-search-bar-wrap .custom-select-wrap {
        width: 100%;
        background: transparent !important;
        border: none !important;
        position: relative;
    }

    .hero-search-bar-wrap .cs-trigger {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        color: var(--header-text) !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        box-shadow: none !important;
        height: auto !important;
        min-height: unset !important;
        line-height: normal !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        cursor: pointer;
        outline: none !important;
    }

    .hero-search-bar-wrap .cs-text {
        font-size: 13px !important;
        font-weight: 700 !important;
        color: inherit !important;
        padding: 0 !important;
    }

    .hero-search-bar-wrap .cs-arrow {
        color: var(--accent) !important;
        width: 12px !important;
        height: 12px !important;
        margin-left: 6px;
        flex-shrink: 0;
    }

    /* Custom options list dropdown panel */
    .hero-search-bar-wrap .cs-panel {
        background: #1e293b !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45) !important;
        margin-top: 8px !important;
        padding: 4px 0 !important;
        z-index: 99999 !important;
        width: 140px !important;
        position: absolute;
        top: 100%;
        left: 0;
    }

    .hero-search-bar-wrap .cs-options {
        max-height: none !important;
        overflow-y: visible !important;
    }

    .hero-search-bar-wrap .cs-option {
        background: transparent !important;
        color: #f1f5f9 !important;
        padding: 8px 12px !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
        transition: all 0.2s ease !important;
        text-align: left;
    }

    .hero-search-bar-wrap .cs-option:last-child {
        border-bottom: none !important;
    }

    .hero-search-bar-wrap .cs-option:hover,
    .hero-search-bar-wrap .cs-option.selected {
        background: #6366f1 !important;
        color: #ffffff !important;
    }

    body.dark-mode .header-search .cs-panel {
        background: #1e293b !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45) !important;
    }
    body.dark-mode .header-search .cs-option {
        color: #f1f5f9 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
    }

    .header-search .search-submit-btn span {
        display: none;
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

    /* ── Flatpickr Custom Premium Styling ── */
    .flatpickr-calendar,
    .flatpickr-calendar.open,
    .flatpickr-calendar.animate.open {
        transform: none !important;
        border-radius: 12px !important;
        border: 1px solid rgba(99, 102, 241, 0.3) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12) !important;
        padding: 8px !important;
        width: 275px !important;
        background: #ffffff !important;
        box-sizing: border-box !important;
    }
    
    .flatpickr-days {
        width: 100% !important;
    }
    
    .dayContainer {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
        justify-content: space-around !important;
    }

    .flatpickr-day {
        max-width: 34px !important;
        height: 34px !important;
        line-height: 34px !important;
        font-size: 13px !important;
    }
    
    span.flatpickr-weekday {
        font-size: 12px !important;
    }

    /* Force Clear Day Number Visibility in Light Mode! */
    .flatpickr-calendar,
    .flatpickr-month,
    .flatpickr-weekday,
    .flatpickr-current-month,
    .flatpickr-monthDropdown-months,
    .flatpickr-day {
        color: #1e293b !important;
        fill: #1e293b !important;
    }

    body.dark-mode .flatpickr-calendar {
        background: #1e293b !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5) !important;
    }

    /* Bold Highlight today's date with light tint and border outline */
    .flatpickr-day.today {
        background: rgba(99, 102, 241, 0.12) !important;
        border-color: #6366f1 !important;
        color: #6366f1 !important;
        font-weight: 800 !important;
    }

    .flatpickr-day.selected,
    .flatpickr-day.selected:hover {
        background: #10b981 !important;
        border-color: #10b981 !important;
        color: white !important;
    }

    body.dark-mode .flatpickr-calendar,
    body.dark-mode .flatpickr-month,
    body.dark-mode .flatpickr-weekday,
    body.dark-mode .flatpickr-current-month,
    body.dark-mode .flatpickr-monthDropdown-months,
    body.dark-mode .numInputWrapper span,
    body.dark-mode .flatpickr-day {
        color: #f1f5f9 !important;
        fill: #f1f5f9 !important;
    }

    body.dark-mode .flatpickr-day.flatpickr-disabled,
    body.dark-mode .flatpickr-day.disabled {
        color: #475569 !important;
        background: transparent !important;
        opacity: 0.45 !important;
    }

    .flatpickr-day.is-sunday:not(.flatpickr-disabled):not(.disabled) {
        color: #ef4444 !important;
        font-weight: bold;
    }

    .flatpickr-day.is-checkin-date,
    .flatpickr-day.is-checkin-date:hover,
    .flatpickr-day.is-checkout-date,
    .flatpickr-day.is-checkout-date:hover {
        background: #10b981 !important;
        border-color: #10b981 !important;
        color: white !important;
        font-weight: bold !important;
        opacity: 0.95 !important;
    }

    /* Range selection highlighting styles */
    .flatpickr-day.is-in-range,
    .flatpickr-day.is-hover-range,
    .flatpickr-day:hover {
        background: #f1f5f9 !important;
        border-color: #e2e8f0 !important;
        color: #0f172a !important;
    }

    body.dark-mode .flatpickr-day.is-in-range,
    body.dark-mode .flatpickr-day.is-hover-range,
    body.dark-mode .flatpickr-day:hover {
        background: #334155 !important;
        border-color: #1e293b !important;
        color: #f8fafc !important;
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
            <div class="hero-search-bar-wrap">
                <form action="{{ route('search') }}" method="GET" class="hero-search-form" id="global-header-search-form">
                    <div class="search-field">
                        <span class="field-label"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> Location</span>
                        <input type="text" name="city" id="header-location" placeholder="Where are you going?"
                            class="field-input" value="{{ request('city') }}">
                    </div>
                    <div class="field-divider"></div>

                    <div class="search-field">
                        <span class="field-label"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> Check In</span>
                        <input type="text" name="checkin" id="header-checkin" placeholder="Add date" class="field-input"
                            value="{{ request('checkin') }}">
                    </div>
                    <div class="field-divider"></div>

                    <div class="search-field">
                        <span class="field-label"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> Check Out</span>
                        <input type="text" name="checkout" id="header-checkout" placeholder="Add date"
                            class="field-input" value="{{ request('checkout') }}">
                    </div>
                    <div class="field-divider"></div>

                    <div class="search-field" style="max-width: 100px;">
                        <span class="field-label"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg> Guests</span>
                        <select name="guests" id="header-guests-select" data-cs-built="1" class="field-input select-styled">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ request('guests', 2) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                            <option value="10+" {{ request('guests') === '10+' ? 'selected' : '' }}>10+</option>
                        </select>
                    </div>

                    <button type="submit" class="search-submit-btn" aria-label="Search">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </button>
                </form>
            </div>
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
                        <a href="{{ route('account.index') }}">Accounts</a>
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

    document.addEventListener('DOMContentLoaded', () => {
        const headerSearch = document.querySelector('.header-search');
        const heroSection = document.querySelector('.hero-search-bar-wrap:not(.header-search .hero-search-bar-wrap)');

        if (heroSection && headerSearch) {
            const handleScroll = () => {
                const rect = heroSection.getBoundingClientRect();
                if (rect.bottom < 60) {
                    headerSearch.classList.add('active');
                } else {
                    headerSearch.classList.remove('active');
                }
            };
            window.addEventListener('scroll', handleScroll, { passive: true });
            handleScroll();
        } else if (headerSearch) {
            headerSearch.classList.add('active');
        }

        // Initialize header search autocomplete and calendars if libraries are loaded
        setTimeout(() => {
            if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                const headerLoc = document.getElementById('header-location');
                if (headerLoc) {
                    const autocomplete = new google.maps.places.Autocomplete(headerLoc, { fields: ['address_components', 'geometry', 'name', 'formatted_address'] });
                    autocomplete.addListener('place_changed', function() {
                        const place = autocomplete.getPlace();
                        if (place.geometry) {
                            headerLoc.setAttribute('data-place-selected', '1');
                        }
                    });
                }
            }

            if (typeof flatpickr !== 'undefined') {
                const headerCheckin = document.getElementById('header-checkin');
                const headerCheckout = document.getElementById('header-checkout');
                if (headerCheckin && headerCheckout) {
                    const hCheckoutPicker = flatpickr("#header-checkout", { minDate: "today", dateFormat: "Y-m-d" });
                    flatpickr("#header-checkin", {
                        minDate: "today",
                        dateFormat: "Y-m-d",
                        onChange: function (selectedDates, dateStr) {
                            hCheckoutPicker.set("minDate", dateStr ? dateStr : "today");
                            hCheckoutPicker.set("disable", [dateStr]);
                            hCheckoutPicker.redraw();
                            setTimeout(() => hCheckoutPicker.open(), 50);
                        }
                    });
                }
            }

            const headerGuestsSelect = document.getElementById('header-guests-select');
            if (headerGuestsSelect && typeof buildCustomSelect === 'function') {
                headerGuestsSelect.removeAttribute('data-cs-built');
                buildCustomSelect(headerGuestsSelect, { showSearch: false });
            }
        }, 800);

        // Close any open datepickers when scrolling
        window.addEventListener('scroll', () => {
            document.querySelectorAll('.flatpickr-input, .field-input').forEach(input => {
                if (input._flatpickr && input._flatpickr.isOpen) {
                    input._flatpickr.close();
                    input.blur();
                }
            });
        }, { passive: true });

        // Google Autocomplete enforcement for header search
        const hForm = document.getElementById('global-header-search-form');
        const hLocInput = document.getElementById('header-location');
        if (hForm && hLocInput) {
            // Create inline error message
            const hErrorMsg = document.createElement('div');
            hErrorMsg.style.color = '#ef4444'; // Red
            hErrorMsg.style.fontSize = '11px';
            hErrorMsg.style.marginTop = '2px';
            hErrorMsg.style.fontWeight = '600';
            hErrorMsg.style.display = 'none';
            hErrorMsg.style.textAlign = 'left';
            hErrorMsg.style.position = 'absolute';
            hErrorMsg.style.bottom = '-18px';
            hErrorMsg.style.left = '16px';
            hErrorMsg.innerText = 'Please select a location from the dropdown.';
            
            // Add relative positioning to wrapper and append
            hLocInput.parentNode.style.position = 'relative';
            hLocInput.parentNode.appendChild(hErrorMsg);

            hLocInput.addEventListener('input', () => {
                hLocInput.removeAttribute('data-place-selected');
                hErrorMsg.style.display = 'none';
            });
            hForm.addEventListener('submit', function(e) {
                if (!hLocInput.value.trim()) {
                    e.preventDefault();
                    hErrorMsg.innerText = 'Please enter a destination.';
                    hErrorMsg.style.display = 'block';
                    hLocInput.focus();
                } else if (!hLocInput.hasAttribute('data-place-selected')) {
                    // Strict validation
                    e.preventDefault();
                    hErrorMsg.innerText = 'Please select a location from the dropdown.';
                    hErrorMsg.style.display = 'block';
                    hLocInput.focus();
                } else {
                    hErrorMsg.style.display = 'none';
                }
            });
        }
    });
</script>