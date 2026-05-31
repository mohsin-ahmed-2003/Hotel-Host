@extends('layouts.app')

@section('title', 'Hotel Host - Book Perfect Rooms and Stays')

@section('styles')
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        /* ── Hero Section ── */
        .hero-section {
            position: relative;
            height: 540px;
            background-color: #0f172a;
            background-size: cover;
            background-position: center;
            border-radius: 0 0 32px 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #ffffff;
            padding: 0 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: visible !important;
            /* Force visibility so dropdowns can extend below */
        }

        /* Dedicated wrapper for clipping background videos and media elements cleanly */
        .hero-bg-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            overflow: hidden;
            border-radius: 0 0 32px 32px;
            pointer-events: none;
        }

        .hero-video-bg {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(15, 23, 42, 0.45), rgba(15, 23, 42, 0.7));
        }

        .hero-content {
            max-width: 840px;
            z-index: 10;
            position: relative;
            width: 100%;
            animation: heroFadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes heroFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-title {
            font-size: 46px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -1.5px;
            text-shadow: 0 4px 16px rgba(15, 23, 42, 0.4);
        }

        .hero-subtitle {
            font-size: 19px;
            color: #f1f5f9;
            margin-bottom: 36px;
            font-weight: 500;
            text-shadow: 0 2px 10px rgba(15, 23, 42, 0.4);
        }

        /* Glassmorphic Buttons upon Hero Image */
        .hero-btn-group {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .hero-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            padding: 10px 24px;
            border-radius: 30px;
            font-size: 13.5px;
            font-weight: 750;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }

        .hero-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.35);
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
        }

        .hero-btn.recommended {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.2));
            border-color: rgba(16, 185, 129, 0.35);
        }

        .hero-btn.recommended:hover {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.35), rgba(5, 150, 105, 0.35));
            border-color: rgba(16, 185, 129, 0.5);
        }

        .hero-btn.nearby {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(79, 70, 229, 0.2));
            border-color: rgba(99, 102, 241, 0.35);
        }

        .hero-btn.nearby:hover {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.35), rgba(79, 70, 229, 0.35));
            border-color: rgba(99, 102, 241, 0.5);
        }

        .hero-btn.offers {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.2));
            border-color: rgba(239, 68, 68, 0.35);
        }

        .hero-btn.offers:hover {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.35), rgba(220, 38, 38, 0.35));
            border-color: rgba(239, 68, 68, 0.5);
        }

        /* ── Hero Buttons Glow & Active Rotating Border ── */
        .hero-btn {
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .hero-btn::after {
            content: '';
            position: absolute;
            inset: 2px;
            background: #0f172a;
            border-radius: 28px;
            z-index: -1;
            transition: opacity 0.3s ease;
            opacity: 0;
        }

        .hero-btn.active {
            border-color: transparent !important;
            background: transparent !important;
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.15);
        }

        .hero-btn.active::after {
            opacity: 1;
        }

        .hero-btn.active::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300%;
            height: 300%;
            background: conic-gradient(from 0deg, transparent 50%, var(--btn-glow-color, #3b82f6), var(--btn-glow-color-light, #60a5fa), transparent 100%);
            transform: translate(-50%, -50%) rotate(0deg);
            animation: rotate-border 3s linear infinite;
            z-index: -2;
            pointer-events: none;
        }

        .hero-btn.recommended {
            --btn-glow-color: #10b981;
            --btn-glow-color-light: #34d399;
        }

        .hero-btn.nearby {
            --btn-glow-color: #6366f1;
            --btn-glow-color-light: #818cf8;
        }

        .hero-btn.offers {
            --btn-glow-color: #ef4444;
            --btn-glow-color-light: #f87171;
        }

        @keyframes rotate-border {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to   { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* ── CSS Skeleton Loading Placeholders ── */
        .skeleton-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            width: 100%;
        }

        .skeleton-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .skeleton-image {
            height: 180px;
            border-radius: 16px;
            animation: skeleton-pulse 1.5s infinite ease-in-out;
        }

        .skeleton-content {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .skeleton-line {
            height: 12px;
            border-radius: 6px;
            animation: skeleton-pulse 1.5s infinite ease-in-out;
        }

        .skeleton-line.title { width: 70%; height: 16px; }
        .skeleton-line.review { width: 25%; height: 16px; }
        .skeleton-line.type { width: 50%; }
        .skeleton-line.price { width: 35%; height: 18px; }
        .skeleton-line.footer-left { width: 40%; }
        .skeleton-line.footer-right { width: 30%; }

        @keyframes skeleton-pulse {
            0% { background-color: rgba(255, 255, 255, 0.04); }
            50% { background-color: rgba(255, 255, 255, 0.1); }
            100% { background-color: rgba(255, 255, 255, 0.04); }
        }

        /* Offers custom titles styling */
        .offers-section-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--body-text);
            margin: 32px 0 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ── Dynamic Tab Transitions ── */
        .tab-content-fade {
            animation: tabFade 0.4s ease forwards;
        }

        @keyframes tabFade {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Premium Airbnb-Style Glassmorphic Search Bar ── */
        .hero-search-bar-wrap {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1.5px solid rgba(255, 255, 255, 0.28);
            border-radius: 40px;
            padding: 8px 10px 8px 24px;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.3);
            max-width: 820px;
            margin: 0 auto;
            animation: heroFadeIn 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            overflow: visible !important;
            /* Force dropdowns to remain unclipped */
            position: relative;
            z-index: 1000;
        }

        .hero-search-form {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .search-field {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            flex: 1;
            min-width: 110px;
        }

        /* Brighter, highly legible labels with prominent icons */
        .field-label {
            font-size: 10px;
            font-weight: 800;
            color: #e2e8f0;
            text-shadow: 0 1px 2px rgba(15, 23, 42, 0.2);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .field-label i {
            font-size: 13px;
            color: #818cf8;
        }

        /* High legibility white input values */
        .field-input {
            background: transparent;
            border: none;
            color: #ffffff !important;
            font-size: 14px;
            font-weight: 800;
            outline: none;
            width: 100%;
            padding: 2px 0;
        }

        .field-input::placeholder {
            color: #cbd5e1 !important;
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
            color: #ffffff !important;
            font-size: 14px !important;
            font-weight: 800 !important;
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
            font-size: 14px !important;
            font-weight: 800 !important;
            color: #ffffff !important;
            padding: 2px 0 !important;
        }

        .hero-search-bar-wrap .cs-arrow {
            color: #818cf8 !important;
            width: 12px !important;
            height: 12px !important;
            margin-left: 6px;
            flex-shrink: 0;
        }

        /* Custom options list dropdown panel - Gorgeous Slate Dark Theme! */
        .hero-search-bar-wrap .cs-panel {
            background: #1e293b !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45) !important;
            margin-top: 8px !important;
            padding: 4px 0 !important;
            z-index: 99999 !important;
            width: 140px !important;
            /* Perfectly sized to fit "10+ Guests" comfortably */
            position: absolute;
            top: 100%;
            left: 0;
        }

        .hero-search-bar-wrap .cs-options {
            max-height: none !important;
            /* Completely remove max-height constraints */
            overflow-y: visible !important;
            /* Remove scrollbars entirely */
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
            /* Indigo active color */
            color: #ffffff !important;
        }

        .field-divider {
            width: 1.5px;
            height: 32px;
            background: rgba(255, 255, 255, 0.25);
        }

        .search-submit-btn {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border: none;
            color: #ffffff;
            padding: 12px 26px;
            border-radius: 30px;
            font-size: 13.5px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.25, 1, 0.5, 1);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
            flex-shrink: 0;
        }

        .search-submit-btn:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 24px rgba(99, 102, 241, 0.5);
        }

        /* ── Responsive Styling for Search Box & Hero Groups ── */
        @media (max-width: 991px) {
            .hero-section {
                height: auto;
                padding: 60px 24px;
                border-radius: 0 0 24px 24px;
                overflow: visible !important;
            }

            .hero-title {
                font-size: 34px;
                letter-spacing: -1px;
            }

            .hero-subtitle {
                font-size: 16px;
                margin-bottom: 28px;
            }

            .hero-btn-group {
                gap: 10px;
                margin-bottom: 24px;
            }

            .hero-btn {
                padding: 8px 18px;
                font-size: 12px;
            }

            .hero-search-bar-wrap {
                border-radius: 24px;
                padding: 16px 20px;
                max-width: 600px;
                overflow: visible !important;
            }

            .hero-search-form {
                flex-direction: column;
                align-items: stretch;
                gap: 16px;
            }

            .field-divider {
                display: none;
            }

            .search-field {
                width: 100% !important;
                max-width: 100% !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.15);
                padding-bottom: 12px;
            }

            .search-field:last-of-type {
                border-bottom: none;
                padding-bottom: 0;
            }

            .search-submit-btn {
                width: 100%;
                justify-content: center;
                padding: 14px;
                font-size: 15px;
                border-radius: 20px;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 28px;
            }

            .hero-btn-group {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .hero-btn {
                justify-content: center;
            }
        }

        /* ── Flatpickr Custom Premium Styling ── */
        .flatpickr-calendar,
        .flatpickr-calendar.open,
        .flatpickr-calendar.animate.open {
            transform: scale(0.8) !important;
            transform-origin: top left !important;
            border-radius: 16px !important;
            border: 1px solid rgba(99, 102, 241, 0.3) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12) !important;
            padding: 5px !important;
            width: 305px !important;
            background: #ffffff !important;
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
            border-color: #475569 !important;
            color: #f8fafc !important;
        }

        /* ── Google Autocomplete Dropdown Styling ── */
        .pac-container {
            background-color: #ffffff !important;
            border: 1px solid rgba(99, 102, 241, 0.25) !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
            font-family: inherit !important;
            margin-top: 4px !important;
            z-index: 99999 !important;
            min-width: 350px !important;
            /* Force a generous width so address lines do not clip */
            max-width: 450px !important;
        }

        body.dark-mode .pac-container {
            background-color: #1e293b !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4) !important;
        }

        .pac-item {
            padding: 10px 14px !important;
            font-size: 13px !important;
            color: #475569 !important;
            cursor: pointer !important;
            border-top: 1px solid rgba(99, 102, 241, 0.08) !important;
            display: flex !important;
            align-items: center !important;
        }

        body.dark-mode .pac-item {
            color: #cbd5e1 !important;
            border-top: 1px solid rgba(255, 255, 255, 0.05) !important;
        }

        .pac-item:hover {
            background-color: #f8fafc !important;
        }

        body.dark-mode .pac-item:hover {
            background-color: #334155 !important;
        }

        .pac-item-query {
            font-size: 13px !important;
            color: #1e293b !important;
            font-weight: 700 !important;
        }

        body.dark-mode .pac-item-query {
            color: #ffffff !important;
        }

        .pac-matched {
            color: #6366f1 !important;
        }

        .pac-icon {
            display: none !important;
            /* Hide Google default icons for absolute clean UI */
        }

        /* ── Main Layout Sections ── */
        .home-sections-container {
            max-width: 1200px;
            margin: 48px auto 56px auto;
            padding: 0 24px;
        }

        .section-header-wrapper {
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 14px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .section-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
            display: block;
        }

        .section-main-title {
            font-size: 28px;
            font-weight: 850;
            color: var(--body-text);
            letter-spacing: -0.8px;
            margin: 0;
        }

        /* ── Compact Modernized Rooms Grid ── */
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 28px;
            margin-bottom: 56px;
        }

        /* ── Premium Compact Room Card (Auto Height) ── */
        .room-card {
            background: var(--card-bg);
            border: 1.5px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            height: auto;
            max-width: 275px;
            margin: 0 auto;
            width: 100%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.01);
        }

        .room-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-md);
            border-color: rgba(99, 102, 241, 0.3);
        }

        /* Slider Container */
        .room-image-slider {
            position: relative;
            height: 165px;
            overflow: hidden;
            background: #f1f5f9;
            flex-shrink: 0;
            border-radius: 18px 18px 0 0;
        }

        .slides-container {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .slide-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            transition: opacity 0.4s ease;
            display: none;
        }

        .slide-img.active {
            opacity: 1;
            z-index: 1;
            display: block;
        }

        /* Hover Chevron Navigation arrows */
        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%) scale(0.85);
            z-index: 10;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
            cursor: pointer;
            opacity: 0;
            transition: all 0.25s cubic-bezier(0.25, 1, 0.5, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
            outline: none;
        }

        .slider-btn:hover {
            background: #ffffff;
            color: var(--accent);
            transform: translateY(-50%) scale(1.05);
        }

        .room-card:hover .slider-btn {
            opacity: 1;
            transform: translateY(-50%) scale(1);
        }

        .prev-btn {
            left: 12px;
        }

        .next-btn {
            right: 12px;
        }

        /* ── Wishlist Heart Button inside top-right corner of image ── */
        .wishlist-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 15;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.25, 1, 0.5, 1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            outline: none;
        }

        .wishlist-btn:hover {
            transform: scale(1.1);
            background: #ffffff;
            color: #ef4444;
        }

        .wishlist-btn.active {
            color: #ef4444;
            background: #ffffff;
        }

        body.dark-mode .wishlist-btn {
            background: rgba(15, 23, 42, 0.8);
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        body.dark-mode .wishlist-btn:hover,
        body.dark-mode .wishlist-btn.active {
            background: #0f172a;
            color: #f87171;
        }

        /* ── Room Card Contents ── */
        .room-card-content {
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            background: var(--card-bg);
            border-top: none;
        }

        /* First Row: Room Name & Review (Right Side) */
        .room-title-review-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .room-title-link {
            text-decoration: none;
            flex-grow: 1;
            overflow: hidden;
        }

        .room-card-title {
            font-size: 14px;
            font-weight: 800;
            color: var(--body-text);
            margin: 0;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            line-height: 1.25;
            transition: color 0.15s ease;
        }

        .room-card:hover .room-card-title {
            color: var(--accent);
        }

        .room-card-review {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 800;
            color: var(--body-text);
            flex-shrink: 0;
        }

        .room-card-review .star-icon {
            color: #f59e0b;
            font-size: 11px;
        }

        .room-card-review .no-review-text {
            font-size: 11px;
            color: var(--body-muted);
            font-weight: 550;
            white-space: nowrap;
        }

        /* Second Row: Room Type and Space Type */
        .room-type-space-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: var(--body-muted);
            font-weight: 600;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .room-property-type {
            color: var(--accent);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .room-space-type {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .dot-separator {
            color: var(--border);
            font-size: 8px;
        }

        /* Third Row: Price below Type and Space Type */
        .room-card-price-row {
            display: flex;
            align-items: center;
            font-size: 11px;
            color: var(--body-muted);
            font-weight: 600;
            margin-top: 2px;
        }

        .room-card-price-row .price-val {
            font-size: 15px;
            font-weight: 850;
            color: var(--body-text);
            margin-right: 2px;
        }

        /* Fourth Row: Compact Footer details */
        .room-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--border);
            padding-top: 10px;
            margin-top: 6px;
            font-size: 11px;
            color: var(--body-muted);
            font-weight: 600;
        }

        .room-location,
        .room-guests {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .room-location {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 150px;
        }

        .room-location i {
            color: #ef4444;
        }

        .room-guests i {
            color: var(--accent);
        }
    </style>
@endsection

@section('content')
    <!-- Hero Premium Section -->
    <section class="hero-section"
        style="@if($settings->get('hero_media_type', 'image') === 'image' && $settings->get('hero_media_file')) background-image: linear-gradient(rgba(15, 23, 42, 0.45), rgba(15, 23, 42, 0.7)), url('{{ \Illuminate\Support\Facades\Storage::url($settings->get('hero_media_file')) }}'); @elseif($settings->get('hero_media_type', 'image') === 'image') background-image: linear-gradient(rgba(15, 23, 42, 0.45), rgba(15, 23, 42, 0.7)), url('https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1600&q=80'); @endif">

        <!-- Autoplaying Muted Looped Video or Image wrapper strictly clipping backgrounds -->
        @if($settings->get('hero_media_type') === 'video' && $settings->get('hero_media_file'))
            <div class="hero-bg-wrapper">
                <video class="hero-video-bg" autoplay loop muted playsinline>
                    <source src="{{ \Illuminate\Support\Facades\Storage::url($settings->get('hero_media_file')) }}"
                        type="video/mp4">
                </video>
                <div class="hero-overlay"></div>
            </div>
        @elseif($settings->get('hero_media_type', 'image') === 'image')
            <div class="hero-bg-wrapper">
                <div class="hero-overlay"></div>
            </div>
        @endif

        <div class="hero-content">
            <!-- Inside Hero Navigation Actions: Placed above title! -->
            <div class="hero-btn-group">
                <a href="#recommended" class="hero-btn recommended" onclick="switchTab(event, 'recommended')">
                    <i class="fas fa-magic"></i> Recommended
                </a>
                <a href="#nearby" class="hero-btn nearby" onclick="switchTab(event, 'nearby')">
                    <i class="fas fa-map-marked-alt"></i> Nearby
                </a>
                <a href="#offers" class="hero-btn offers" onclick="switchTab(event, 'offers')">
                    <i class="fas fa-percentage"></i> Hot Offers
                </a>
            </div>

            <h1 class="hero-title">{{ $settings->get('hero_title', 'Discover Your Next Perfect Escape') }}</h1>
            <p class="hero-subtitle">
                {{ $settings->get('hero_subtitle', 'Book uniquely designed hotels, private villas, and compact workspaces tailored for you') }}
            </p>

            <!-- Search bar with: Location, Check-in, Check-out, Number of Guests & Search Button -->
            <div class="hero-search-bar-wrap">
                <form action="/" method="GET" class="hero-search-form">
                    <!-- Location field -->
                    <div class="search-field">
                        <span class="field-label"><i class="fas fa-map-marker-alt"></i> Location</span>
                        <input type="text" name="city" id="location-autocomplete" placeholder="Where are you going?"
                            class="field-input" value="{{ request('city') }}">
                    </div>
                    <div class="field-divider"></div>

                    <!-- Check-in field -->
                    <div class="search-field">
                        <span class="field-label"><i class="far fa-calendar-alt"></i> Check In</span>
                        <input type="text" name="checkin" id="checkin" placeholder="Add date" class="field-input"
                            value="{{ request('checkin') }}">
                    </div>
                    <div class="field-divider"></div>

                    <!-- Check-out field -->
                    <div class="search-field">
                        <span class="field-label"><i class="far fa-calendar-check"></i> Check Out</span>
                        <input type="text" name="checkout" id="checkout" placeholder="Add date" class="field-input"
                            value="{{ request('checkout') }}">
                    </div>
                    <div class="field-divider"></div>

                    <!-- Guests field - Premium Dropdown -->
                    <div class="search-field" style="max-width: 100px;">
                        <span class="field-label"><i class="fas fa-users"></i> Guests</span>
                        <select name="guests" id="guests-select" data-cs-built="1" class="field-input select-styled">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ request('guests', 2) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                            <option value="10+" {{ request('guests') === '10+' ? 'selected' : '' }}>10+</option>
                        </select>
                    </div>

                    <!-- Search Button -->
                    <button type="submit" class="search-submit-btn">
                        <i class="fas fa-search"></i> <span>Search</span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Home Rooms Main Wrapper -->
    <div class="home-sections-container" id="rooms-content-wrapper">
        <div class="tab-content-fade">
            <!-- SECTION 1: Recent Bookings -->
            <section id="recommended-rooms" style="scroll-margin-top: 100px;">
                <div class="section-header-wrapper">
                    <div>
                        <span class="section-label">Most In Demand</span>
                        <h2 class="section-main-title"><i class="fas fa-fire" style="color:#ef4444; margin-right:8px;"></i>
                            Recent Booked</h2>
                    </div>
                </div>

                @include('home.partials.rooms_grid', [
                    'rooms' => $recentlyBooked,
                    'fallbackIcon' => 'fas fa-hotel',
                    'fallbackText' => 'No recently booked properties listed yet.'
                ])
            </section>

            <!-- SECTION 2: Most Views -->
            <section id="most-viewed-section" style="scroll-margin-top: 100px;">
                <div class="section-header-wrapper">
                    <div>
                        <span class="section-label">Highly Visited</span>
                        <h2 class="section-main-title"><i class="fas fa-eye" style="color:var(--accent); margin-right:8px;"></i>
                            Most Views</h2>
                    </div>
                </div>

                @include('home.partials.rooms_grid', [
                    'rooms' => $mostViewed,
                    'fallbackIcon' => 'fas fa-eye-slash',
                    'fallbackText' => 'No popular properties found.'
                ])
            </section>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Flatpickr Script -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Google Places Autocomplete API loaded dynamically with map_key -->
    @php $mapKey = \App\Models\SiteSetting::get('map_key'); @endphp
    @if($mapKey)
        <script src="https://maps.googleapis.com/maps/api/js?key={{ $mapKey }}&libraries=places"></script>
    @endif

    <script>
        // Initialize Autocomplete, Select triggers and Flatpickr Calendars
        document.addEventListener('DOMContentLoaded', function () {

            // ── Google Places Autocomplete (Identical setup to step3.blade.php) ──
            const locationInput = document.getElementById('location-autocomplete');
            if (locationInput && window.google && window.google.maps && window.google.maps.places) {
                const autocomplete = new google.maps.places.Autocomplete(locationInput, {
                    fields: ['address_components', 'geometry', 'name', 'formatted_address']
                });

                // Prevent form submission when selecting suggestions using the Enter key
                google.maps.event.addDomListener(locationInput, 'keydown', function (e) {
                    if (e.key === 'Enter') {
                        const pacContainer = document.querySelector('.pac-container');
                        if (pacContainer && pacContainer.style.display !== 'none') {
                            e.preventDefault();
                        }
                    }
                });
            }

            // ── Custom Guest Select dropdown builder without search ──
            const guestsSelect = document.getElementById('guests-select');
            if (guestsSelect && typeof buildCustomSelect === 'function') {
                // Remove build guard flag temporarily to let our custom builder render it
                guestsSelect.removeAttribute('data-cs-built');
                buildCustomSelect(guestsSelect, { showSearch: false });
            }

            // ── Shared Flatpickr Hover & Range Highlights Handler ──
            const handleDayCreate = function (dObj, dStr, fp, dayElem) {
                // Highlight Sundays
                if (dayElem.dateObj.getDay() === 0) {
                    dayElem.classList.add("is-sunday");
                }

                // Read actual standard selected value of hidden inputs generated by Flatpickr altInput
                const checkinVal = document.getElementById("checkin").value;
                const checkoutVal = document.getElementById("checkout").value;

                // Highlight Check-in point
                if (checkinVal) {
                    const currentDayStr = flatpickr.formatDate(dayElem.dateObj, "Y-m-d");
                    if (currentDayStr === checkinVal) {
                        dayElem.classList.add("is-checkin-date");
                    }
                }

                // Highlight Check-out point
                if (checkoutVal) {
                    const currentDayStr = flatpickr.formatDate(dayElem.dateObj, "Y-m-d");
                    if (currentDayStr === checkoutVal) {
                        dayElem.classList.add("is-checkout-date");
                    }
                }

                // Permanent range selections background highlights
                if (checkinVal && checkoutVal) {
                    const ciDate = fp.parseDate(checkinVal, "Y-m-d");
                    const coDate = fp.parseDate(checkoutVal, "Y-m-d");
                    if (dayElem.dateObj > ciDate && dayElem.dateObj < coDate) {
                        dayElem.classList.add("is-in-range");
                    }
                }

                // Active Calendar hover trails
                dayElem.addEventListener('mouseenter', function () {
                    if (!checkinVal) return;

                    const ciDate = fp.parseDate(checkinVal, "Y-m-d");
                    const hoverDate = dayElem.dateObj;

                    fp.days.childNodes.forEach(day => {
                        if (day.classList) day.classList.remove('is-hover-range');
                    });

                    if (hoverDate > ciDate) {
                        fp.days.childNodes.forEach(day => {
                            if (day.dateObj && day.dateObj > ciDate && day.dateObj <= hoverDate) {
                                if (!day.classList.contains('is-in-range') && !day.classList.contains('is-checkout-date')) {
                                    day.classList.add('is-hover-range');
                                }
                            }
                        });
                    }
                });

                dayElem.addEventListener('mouseleave', function () {
                    fp.days.childNodes.forEach(day => {
                        if (day.classList) day.classList.remove('is-hover-range');
                    });
                });
            };

            // ── Flatpickr Initialization ──
            const checkinEl = document.getElementById('checkin');
            const checkoutEl = document.getElementById('checkout');

            if (checkinEl && checkoutEl) {
                const checkoutPicker = flatpickr("#checkout", {
                    minDate: "today",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d/m/Y",
                    altInputClass: "field-input",
                    disableMobile: "true",
                    onDayCreate: handleDayCreate
                });

                const checkinPicker = flatpickr("#checkin", {
                    minDate: "today",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d/m/Y",
                    altInputClass: "field-input",
                    disableMobile: "true",
                    onChange: function (selectedDates, dateStr, instance) {
                        // Restrict checkout picker range dynamically
                        checkoutPicker.set("minDate", dateStr ? dateStr : "today");
                        // Do not allow same date to be selected for checkout
                        checkoutPicker.set("disable", [dateStr]);
                        checkoutPicker.redraw();

                        // Automatically pop-open the checkout calendar
                        setTimeout(() => {
                            checkoutPicker.open();
                        }, 50);
                    },
                    onDayCreate: handleDayCreate
                });
            }

            // Load wishlist on page render & URL Hash navigation initial triggers
            const wishlist = JSON.parse(localStorage.getItem('user_wishlist') || '{}');
            Object.keys(wishlist).forEach(roomId => {
                if (wishlist[roomId]) {
                    const buttons = document.querySelectorAll(`.wishlist-btn-room-${roomId}`);
                    buttons.forEach(btn => {
                        btn.classList.add('active');
                        const icon = btn.querySelector('i');
                        if (icon) icon.className = 'fas fa-heart';
                    });
                }
            });

            // Handle persistent hash layout navigation
            const initialHash = window.location.hash || '#recommended';
            const initialTab = initialHash.replace('#', '');
            if (['recommended', 'nearby', 'offers'].includes(initialTab)) {
                switchTab(null, initialTab, true);
            } else {
                switchTab(null, 'recommended', true);
            }
        });

        // ── SPA Tab Dynamic Handler ──
        window.switchTab = function(event, tabName, isInitial = false) {
            if (event) {
                event.preventDefault();
            }

            // Sync URL hash state
            if (!isInitial) {
                history.pushState(null, null, `#${tabName}`);
            }

            // Update Tab Button Active states
            const buttons = document.querySelectorAll('.hero-btn');
            buttons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.classList.contains(tabName)) {
                    btn.classList.add('active');
                }
            });

            const container = document.getElementById('rooms-content-wrapper');
            if (!container) return;

            // Scroll smoothly on user interaction
            if (!isInitial && event) {
                container.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            if (isInitial && tabName === 'recommended') {
                syncLoadedWishlist();
                return;
            }

            // Render responsive skeleton loading elements
            renderSkeleton(container, tabName);

            // Geolocation tracking if "nearby" is triggered
            if (tabName === 'nearby') {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            // Reverse geocode via Google Maps API
                            if (typeof google !== 'undefined' && google.maps && google.maps.Geocoder) {
                                const geocoder = new google.maps.Geocoder();
                                geocoder.geocode({ location: { lat, lng } }, (results, status) => {
                                    if (status === 'OK' && results[0]) {
                                        let city = '';
                                        let state = '';
                                        for (const component of results[0].address_components) {
                                            if (component.types.includes('locality')) {
                                                city = component.long_name;
                                            }
                                            if (component.types.includes('administrative_area_level_1')) {
                                                state = component.long_name;
                                            }
                                        }
                                        fetchRooms(tabName, { lat, lng, city, state });
                                    } else {
                                        fetchRooms(tabName, { lat, lng });
                                    }
                                });
                            } else {
                                fetchRooms(tabName, { lat, lng });
                            }
                        },
                        (error) => {
                            console.warn("Geolocation failed or denied, defaulting to Madurai, Tamil Nadu fallback.", error);
                            fetchRooms(tabName, { lat: 9.9575, lng: 78.1720, city: 'Madurai', state: 'Tamil Nadu' });
                        },
                        { 
                            enableHighAccuracy: true, 
                            timeout: 8000, 
                            maximumAge: 0 
                        }
                    );
                } else {
                    fetchRooms(tabName, { lat: 9.9575, lng: 78.1720, city: 'Madurai', state: 'Tamil Nadu' });
                }
            } else {
                fetchRooms(tabName);
            }
        };

        // Render premium visual skeletons matching actual room layouts
        function renderSkeleton(container, tabName) {
            let html = '<div class="tab-content-fade">';
            
            if (tabName === 'recommended') {
                html += `
                    <section style="scroll-margin-top: 100px;">
                        <div class="section-header-wrapper">
                            <div>
                                <span class="section-label">Most In Demand</span>
                                <h2 class="section-main-title"><i class="fas fa-fire" style="color:#ef4444; margin-right:8px;"></i> Recent Booked</h2>
                            </div>
                        </div>
                        <div class="skeleton-grid">
                            ${Array(4).fill().map(() => getSkeletonCardHtml()).join('')}
                        </div>
                    </section>
                    <section style="scroll-margin-top: 100px; margin-top: 40px;">
                        <div class="section-header-wrapper">
                            <div>
                                <span class="section-label">Highly Visited</span>
                                <h2 class="section-main-title"><i class="fas fa-eye" style="color:var(--accent); margin-right:8px;"></i> Most Views</h2>
                            </div>
                        </div>
                        <div class="skeleton-grid">
                            ${Array(4).fill().map(() => getSkeletonCardHtml()).join('')}
                        </div>
                    </section>
                `;
            } else if (tabName === 'nearby') {
                html += `
                    <section style="scroll-margin-top: 100px;">
                        <div class="section-header-wrapper">
                            <div>
                                <span class="section-label">Current Location</span>
                                <h2 class="section-main-title"><i class="fas fa-map-marker-alt" style="color:#ef4444; margin-right:8px;"></i> Nearby ...</h2>
                            </div>
                        </div>
                        <div class="skeleton-grid">
                            ${Array(4).fill().map(() => getSkeletonCardHtml()).join('')}
                        </div>
                    </section>
                    <section style="scroll-margin-top: 100px; margin-top: 40px;">
                        <div class="section-header-wrapper">
                            <div>
                                <span class="section-label">Other Nearby City</span>
                                <h2 class="section-main-title"><i class="fas fa-city" style="color:var(--accent); margin-right:8px;"></i> Rooms in ...</h2>
                            </div>
                        </div>
                        <div class="skeleton-grid">
                            ${Array(4).fill().map(() => getSkeletonCardHtml()).join('')}
                        </div>
                    </section>
                    <section style="scroll-margin-top: 100px; margin-top: 40px;">
                        <div class="section-header-wrapper">
                            <div>
                                <span class="section-label">State Wide Properties</span>
                                <h2 class="section-main-title"><i class="fas fa-map" style="color:#a855f7; margin-right:8px;"></i> Stays in ... State</h2>
                            </div>
                        </div>
                        <div class="skeleton-grid">
                            ${Array(4).fill().map(() => getSkeletonCardHtml()).join('')}
                        </div>
                    </section>
                `;
            } else if (tabName === 'offers') {
                const sections = [
                    { title: 'Last-Minute Discount', icon: 'fas fa-bolt', color: '#ef4444' },
                    { title: 'Early-Bird Discount', icon: 'fas fa-feather', color: '#10b981' },
                    { title: 'Length-of-Stay Discount', icon: 'fas fa-calendar-alt', color: '#f59e0b' },
                    { title: 'Special Discount', icon: 'fas fa-gift', color: '#a855f7' }
                ];
                sections.forEach(sec => {
                    html += `
                        <div class="offers-section-title">
                            <i class="${sec.icon}" style="color:${sec.color};"></i> ${sec.title}
                        </div>
                        <div class="skeleton-grid" style="margin-bottom: 32px;">
                            ${Array(4).fill().map(() => getSkeletonCardHtml()).join('')}
                        </div>
                    `;
                });
            }

            html += '</div>';
            container.innerHTML = html;
        }

        function getSkeletonCardHtml() {
            return `
                <div class="skeleton-card">
                    <div class="skeleton-image"></div>
                    <div class="skeleton-content">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div class="skeleton-line title"></div>
                            <div class="skeleton-line review"></div>
                        </div>
                        <div class="skeleton-line type" style="margin-top: 4px;"></div>
                        <div class="skeleton-line price" style="margin-top: 4px;"></div>
                        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid rgba(255,255,255,0.06); padding-top:10px; margin-top:8px;">
                            <div class="skeleton-line footer-left"></div>
                            <div class="skeleton-line footer-right"></div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Fetch dynamic content relative to requested parameters
        function fetchRooms(tabName, params = {}) {
            const container = document.getElementById('rooms-content-wrapper');
            if (!container) return;

            const url = new URL(window.location.origin + window.location.pathname);
            url.searchParams.set('tab', tabName);
            Object.keys(params).forEach(key => {
                url.searchParams.set(key, params[key]);
            });

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                let html = '<div class="tab-content-fade">';

                if (tabName === 'recommended') {
                    html += `
                        <section style="scroll-margin-top: 100px;">
                            <div class="section-header-wrapper">
                                <div>
                                    <span class="section-label">Most In Demand</span>
                                    <h2 class="section-main-title"><i class="fas fa-fire" style="color:#ef4444; margin-right:8px;"></i> Recent Booked</h2>
                                </div>
                            </div>
                            ${data.recent_html}
                        </section>
                        <section style="scroll-margin-top: 100px; margin-top: 40px;">
                            <div class="section-header-wrapper">
                                <div>
                                    <span class="section-label">Highly Visited</span>
                                    <h2 class="section-main-title"><i class="fas fa-eye" style="color:var(--accent); margin-right:8px;"></i> Most Views</h2>
                                </div>
                            </div>
                            ${data.views_html}
                        </section>
                    `;
                } else if (tabName === 'nearby') {
                    const cityName = data.city_name || 'Madurai';
                    const stateName = data.state_name || 'Tamil Nadu';

                    html += `
                        <section style="scroll-margin-top: 100px;">
                            <div class="section-header-wrapper">
                                <div>
                                    <span class="section-label">Current Location</span>
                                    <h2 class="section-main-title"><i class="fas fa-map-marker-alt" style="color:#ef4444; margin-right:8px;"></i> Nearby ${cityName}</h2>
                                </div>
                            </div>
                            ${data.city_rooms_html}
                        </section>
                    `;

                    if (data.other_cities && data.other_cities.length > 0) {
                        data.other_cities.forEach(group => {
                            html += `
                                <section style="scroll-margin-top: 100px; margin-top: 40px;">
                                    <div class="section-header-wrapper">
                                        <div>
                                            <span class="section-label">Other Nearby City</span>
                                            <h2 class="section-main-title"><i class="fas fa-city" style="color:var(--accent); margin-right:8px;"></i> Rooms in ${group.city}</h2>
                                        </div>
                                    </div>
                                    ${group.html}
                                </section>
                            `;
                        });
                    }

                    html += `
                        <section style="scroll-margin-top: 100px; margin-top: 40px;">
                            <div class="section-header-wrapper">
                                <div>
                                    <span class="section-label">State Wide Properties</span>
                                    <h2 class="section-main-title"><i class="fas fa-map" style="color:#a855f7; margin-right:8px;"></i> Stays in ${stateName} State</h2>
                                </div>
                            </div>
                            ${data.state_rooms_html}
                        </section>
                    `;
                } else if (tabName === 'offers') {
                    html += `
                        <div class="offers-section-title">
                            <i class="fas fa-bolt" style="color:#ef4444;"></i> Last-Minute Discount
                        </div>
                        ${data.last_minute_html}

                        <div class="offers-section-title">
                            <i class="fas fa-feather" style="color:#10b981;"></i> Early-Bird Discount
                        </div>
                        ${data.early_bird_html}

                        <div class="offers-section-title">
                            <i class="fas fa-calendar-alt" style="color:#f59e0b;"></i> Length-of-Stay Discount
                        </div>
                        ${data.length_of_stay_html}

                        <div class="offers-section-title">
                            <i class="fas fa-gift" style="color:#a855f7;"></i> Special Discount
                        </div>
                        ${data.special_html}
                    `;
                }

                html += '</div>';
                container.innerHTML = html;

                // Sync wishlists on loaded DOM items
                syncLoadedWishlist();
            })
            .catch(err => {
                console.error("Failed to load rooms dynamically:", err);
                container.innerHTML = `
                    <div style="text-align: center; padding: 48px; color: var(--body-muted);">
                        <i class="fas fa-exclamation-triangle" style="font-size: 40px; margin-bottom: 12px; opacity: 0.5; color: #ef4444;"></i>
                        <p>An error occurred while fetching properties. Please try reloading the page.</p>
                    </div>
                `;
            });
        }

        // Synchronize active wishlist states in dynamically fetched card segments
        function syncLoadedWishlist() {
            // Evaluated on render inside room_card PHP template direct from DB!
        }

        // Prevent slides navigation click from bubbling up and clicking card link itself
        function prevSlide(event) {
            event.stopPropagation();
            event.preventDefault();
            const btn = event.currentTarget;
            const slider = btn.closest('.room-image-slider');
            if (!slider) return;
            const container = slider.querySelector('.slides-container');
            if (!container) return;
            const slides = container.querySelectorAll('.slide-img');
            if (slides.length <= 1) return;

            let activeIndex = -1;
            slides.forEach((slide, index) => {
                if (slide.classList.contains('active')) {
                    activeIndex = index;
                }
            });

            if (activeIndex !== -1) {
                slides[activeIndex].classList.remove('active');

                let nextIndex = activeIndex - 1;
                if (nextIndex < 0) nextIndex = slides.length - 1;

                slides[nextIndex].classList.add('active');
            }
        }

        // Next Slide helper
        function nextSlide(event) {
            event.stopPropagation();
            event.preventDefault();
            const btn = event.currentTarget;
            const slider = btn.closest('.room-image-slider');
            if (!slider) return;
            const container = slider.querySelector('.slides-container');
            if (!container) return;
            const slides = container.querySelectorAll('.slide-img');
            if (slides.length <= 1) return;

            let activeIndex = -1;
            slides.forEach((slide, index) => {
                if (slide.classList.contains('active')) {
                    activeIndex = index;
                }
            });

            if (activeIndex !== -1) {
                slides[activeIndex].classList.remove('active');

                let nextIndex = activeIndex + 1;
                if (nextIndex >= slides.length) nextIndex = 0;

                slides[nextIndex].classList.add('active');
            }
        }

        let currentActiveRoomId = null;

        // Wishlist Toggle persistent logic via AJAX
        function toggleWishlist(event, roomId) {
            event.stopPropagation();
            event.preventDefault();
            
            const isLoggedIn = {{ session('user_id') ? 'true' : 'false' }};
            if (!isLoggedIn) {
                window.location.href = "{{ route('auth') }}";
                return;
            }

            const btn = event.currentTarget;
            const icon = btn.querySelector('i');
            if (!btn || !icon) return;

            // First toggle AJAX trigger to check/delete or prompt
            fetch('/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ room_id: roomId })
            })
            .then(res => {
                if (res.status === 401) {
                    window.location.href = "{{ route('auth') }}";
                    throw new Error("Unauthorized");
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    if (data.status === 'removed') {
                        updateHeartIcons(roomId, false);
                    } else if (data.status === 'prompt') {
                        currentActiveRoomId = roomId;
                        openWishlistModal(btn);
                    }
                } else {
                    alert(data.message || "An error occurred.");
                }
            })
            .catch(err => {
                console.error("Wishlist error:", err);
            });
        }

        function openWishlistModal(btn) {
            const card = btn.closest('.room-card');
            if (card) {
                const img = card.querySelector('.slide-img.active')?.src || card.querySelector('.slide-img')?.src || '';
                const title = card.querySelector('.room-card-title')?.innerText || '';
                const price = card.querySelector('.price-val')?.innerText || card.querySelector('.room-card-price-row')?.innerText || '';
                
                document.getElementById('wishlistPreviewImg').src = img;
                document.getElementById('wishlistPreviewTitle').innerText = title;
                document.getElementById('wishlistPreviewPrice').innerText = price;
            }
            
            // Populate custom folders
            fetch('/wishlist/groups')
            .then(res => res.json())
            .then(groups => {
                const list = document.getElementById('wishlistGroupsList');
                list.innerHTML = '';
                
                if (groups.length === 0) {
                    list.innerHTML = `
                        <div style="text-align:center; padding:16px; font-size:13px; color:#cbd5e1; opacity:0.6;">
                            No collections yet. Create your first!
                        </div>
                    `;
                } else {
                    groups.forEach(group => {
                        const item = document.createElement('button');
                        item.className = 'wishlist-group-item-btn';
                        item.innerHTML = `
                            <span>${group}</span>
                            <i class="fas fa-plus" style="font-size:10px; opacity:0.6;"></i>
                        `;
                        item.onclick = () => saveWishlistWithExistingGroup(group);
                        list.appendChild(item);
                    });
                }
                
                document.getElementById('newGroupNameInput').value = '';
                
                const modal = document.getElementById('wishlistModal');
                modal.style.display = 'flex';
                setTimeout(() => {
                    modal.classList.add('open');
                }, 20);
            });
        }

        function closeWishlistModal() {
            const modal = document.getElementById('wishlistModal');
            modal.classList.remove('open');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        function saveWishlistWithExistingGroup(groupName) {
            submitWishlistSave(groupName);
        }

        function saveWishlistWithNewGroup() {
            const groupName = document.getElementById('newGroupNameInput').value.trim();
            if (!groupName) {
                alert("Please enter a collection name.");
                return;
            }
            submitWishlistSave(groupName);
        }

        function submitWishlistSave(groupName) {
            if (!currentActiveRoomId) return;
            
            fetch('/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    room_id: currentActiveRoomId,
                    group_name: groupName
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.status === 'added') {
                    updateHeartIcons(currentActiveRoomId, true);
                    closeWishlistModal();
                } else {
                    alert(data.message || "An error occurred.");
                }
            })
            .catch(err => {
                console.error("Save wishlist error:", err);
            });
        }

        function updateHeartIcons(roomId, active) {
            const allButtons = document.querySelectorAll(`.wishlist-btn-room-${roomId}`);
            allButtons.forEach(btn => {
                const icon = btn.querySelector('i');
                if (active) {
                    btn.classList.add('active');
                    if (icon) {
                        icon.className = 'fas fa-heart';
                        icon.style.color = '#f87171';
                    }
                } else {
                    btn.classList.remove('active');
                    if (icon) {
                        icon.className = 'far fa-heart';
                        icon.style.color = '';
                    }
                }
            });
        }
    </script>

    @include('partials.wishlist_modal')
@endsection