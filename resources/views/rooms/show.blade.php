@extends('layouts.app')

@section('title', $room->title ?: $room->name)

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

    <!-- SweetAlert2 CDN for toaster -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        /* Premium Room Details Styles */
        :root {
            --room-bg: #f8fafc;
            --room-surface: #ffffff;
            --room-text: #0f172a;
            --room-muted: #64748b;
            --room-accent: #3b82f6;
            --room-accent-hover: #2563eb;
            --room-border: #e2e8f0;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.4);
        }

        body.dark-mode {
            --room-bg: #0f172a;
            --room-surface: #1e293b;
            --room-text: #f8fafc;
            --room-muted: #94a3b8;
            --room-border: #334155;
            --glass-bg: rgba(30, 41, 59, 0.85);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        /* Flatpickr Custom Styling */
        .flatpickr-calendar,
        .flatpickr-calendar.open,
        .flatpickr-calendar.animate.open {
            transform: scale(0.85) !important;
            /* Reduce size consistently */
            transform-origin: top left !important;
            border-radius: 16px !important;
            border: 1px solid rgba(59, 130, 246, 0.4) !important;
            box-shadow: 0 15px 40px rgba(59, 130, 246, 0.15) !important;
            padding: 5px !important;
            box-sizing: content-box !important;
            width: 315px !important;
        }

        .flatpickr-day.is-sunday:not(.flatpickr-disabled):not(.disabled) {
            color: #ef4444 !important;
            /* Red for active Sundays only */
            font-weight: bold;
        }

        .flatpickr-day.today {
            background: var(--room-accent) !important;
            border-color: var(--room-accent) !important;
            color: white !important;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        }

        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.flatpickr-disabled:hover,
        .flatpickr-day.disabled,
        .flatpickr-day.disabled:hover {
            color: #475569 !important;
            /* Darker slate gray so it is visible but distinctly faded and blocked */
            opacity: 0.45 !important;
            /* Faded effect */
            font-weight: normal !important;
            /* Strip bold text from disabled days */
            cursor: not-allowed !important;
            background: transparent !important;
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
            cursor: not-allowed !important;
        }

        /* Custom Range selection styling for dual pickers (hover & in-range) */
        .flatpickr-day.is-in-range,
        .flatpickr-day.is-hover-range,
        .flatpickr-day:hover {
            background: #f1f5f9 !important;
            /* Soft slate-gray */
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

        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background: #10b981 !important;
            /* Premium Emerald for selected points */
            border-color: #10b981 !important;
            color: white !important;
            font-weight: bold !important;
        }

        /* Custom Tooltip */
        .tooltip-container {
            position: relative;
            display: inline-flex;
        }

        .custom-tooltip {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(10px);
            background: #111827;
            color: #f9fafb;
            padding: 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            width: max-content;
            max-width: 250px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
            transition: all 0.2s ease;
            z-index: 100;
            pointer-events: none;
            margin-bottom: 8px;
        }

        .custom-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 6px;
            border-style: solid;
            border-color: #111827 transparent transparent transparent;
        }

        .tooltip-container:hover .custom-tooltip {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        body {
            background-color: var(--room-bg);
            color: var(--room-text);
        }

        .room-show-page {
            max-width: 1300px;
            margin: 0 auto;
            padding: 20px 24px 60px;
        }

        /* --- Hero Slider --- */
        .room-hero-section {
            position: relative;
            margin-bottom: 24px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .swiper.room-hero-slider {
            width: 100%;
            height: 60vh;
            min-height: 400px;
            max-height: 700px;
        }

        .swiper-slide a {
            display: block;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 10s ease;
        }

        .swiper-slide-active img {
            transform: scale(1.05);
            /* Slow zoom effect on active slide */
        }

        /* Custom Swiper Controls */
        .swiper-button-next,
        .swiper-button-prev {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            width: 50px !important;
            height: 50px !important;
            border-radius: 50%;
            color: #0f172a !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s;
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background: #fff;
            transform: scale(1.1);
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 20px !important;
            font-weight: 800;
        }

        .swiper-pagination-bullet {
            background: #fff !important;
            opacity: 0.6;
            width: 10px;
            height: 10px;
            transition: all 0.3s;
        }

        .swiper-pagination-bullet-active {
            opacity: 1;
            width: 24px;
            border-radius: 5px;
        }

        /* --- Fancybox Overrides --- */
        .fancybox__caption {
            text-align: center !important;
            font-size: 16px !important;
            font-weight: 500;
            padding-top: 15px !important;
            color: white;
        }

        /* --- Global Header Block (Below Image) --- */
        .room-header-block {
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* Vertically center host card with title/meta */
            padding-bottom: 28px;
            margin-bottom: 36px;
            border-bottom: 1px solid var(--room-border);
            gap: 30px;
        }

        .room-title {
            font-size: 36px;
            font-weight: 800;
            margin: 0 0 8px;
            line-height: 1.2;
            letter-spacing: -1px;
            color: var(--room-text);
        }

        .room-meta {
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 14px;
        }

        .room-meta span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(99, 102, 241, 0.05);
            /* Soft Indigo Tint */
            border: 1px solid rgba(99, 102, 241, 0.12);
            border-radius: 100px;
            font-weight: 600;
            color: var(--room-text);
            font-size: 13.5px;
            transition: all 0.2s ease;
        }

        body.dark-mode .room-meta span {
            background: rgba(167, 139, 250, 0.06);
            border-color: rgba(167, 139, 250, 0.15);
        }

        .room-meta span:hover {
            transform: translateY(-1.5px);
            background: rgba(99, 102, 241, 0.09);
            border-color: rgba(99, 102, 241, 0.22);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.06);
        }

        body.dark-mode .room-meta span:hover {
            background: rgba(167, 139, 250, 0.1);
            border-color: rgba(167, 139, 250, 0.25);
            box-shadow: 0 4px 12px rgba(167, 139, 250, 0.06);
        }

        .room-meta span i {
            font-size: 14px;
            transition: transform 0.2s;
        }

        /* Vivid Semantic Icon Colors */
        .room-meta span i.fa-map-marker-alt {
            color: #ef4444;
            /* High-legibility Location Coral/Red */
        }

        .room-meta span i.fa-users {
            color: #3b82f6;
            /* Modern Blue for Guest Capacity */
        }

        .room-meta span i.fa-home {
            color: #10b981;
            /* Emerald Green for Home/Space type */
        }

        .host-badge {
            display: flex;
            align-items: center;
            gap: 16px;
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 12px 24px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.05);
            border: 1px solid var(--glass-border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .host-badge:hover {
            transform: translateY(-2.5px);
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.08);
            border-color: var(--room-accent);
        }

        .host-avatar-wrapper {
            position: relative;
            flex-shrink: 0;
        }

        .host-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--room-accent), #6366f1);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
            overflow: hidden;
            border: 2.5px solid var(--room-surface);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .host-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .verified-badge {
            position: absolute;
            bottom: -3px;
            right: -3px;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
        }

        .verified-badge img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .host-details h4 {
            margin: 0 0 2px 0;
            font-size: 14px;
            font-weight: 700;
            color: var(--room-text);
        }

        .host-details p {
            margin: 0;
            font-size: 12px;
            color: var(--room-muted);
        }

        /* --- Main Content Grid --- */
        .room-layout-grid {
            display: grid;
            grid-template-columns: 1.8fr 1fr;
            gap: 40px;
            align-items: start;
        }

        /* --- Left Column: Details --- */
        .room-main-info {
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .section-title {
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.5px;
        }

        .section-title i {
            color: var(--room-accent);
        }

        .room-description {
            font-size: 17px;
            line-height: 1.8;
            color: var(--room-text);
            opacity: 0.9;
            white-space: pre-line;
            padding-left: 20px;
            border-left: 4px solid var(--room-accent);
            border-radius: 2px;
        }

        /* Enhancements Section (Grouped) */
        .enhancements-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            align-items: start;
        }

        .enhancement-group {
            background: var(--room-surface);
            border: 1px solid var(--room-border);
            border-radius: 16px;
            padding: 14px 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        }

        .enhancement-type-title {
            font-size: 15px;
            font-weight: 800;
            margin: 0 0 10px 0;
            color: var(--room-text);
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 8px;
            border-bottom: 2px solid rgb(51 122 238 / 32%);
        }

        .enhancement-type-title i {
            color: var(--room-accent);
            font-size: 13px;
            background: rgba(59, 130, 246, 0.1);
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .enhancement-per-guest-banner {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            background: rgba(16, 185, 129, 0.06) !important;
            border: 1px solid rgba(16, 185, 129, 0.15) !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            margin-top: 10px !important;
            color: #10b981 !important;
            font-family: 'Outfit', sans-serif !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .enhancement-per-guest-banner>div:first-child {
            width: auto !important;
            height: auto !important;
            background: transparent !important;
            color: #10b981 !important;
            font-size: 12px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .enhancement-per-guest-banner>div:last-child {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
        }

        .enhancement-per-guest-banner>div:last-child>div:first-child {
            font-size: 10px !important;
            text-transform: uppercase !important;
            font-weight: 600 !important;
            opacity: 0.85 !important;
            letter-spacing: 0.5px !important;
            color: #10b981 !important;
            margin: 0 !important;
        }

        .enhancement-per-guest-banner>div:last-child>div:last-child {
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #10b981 !important;
            margin: 0 !important;
        }

        .enhancements-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .enhancement-card {
            background: var(--room-surface);
            border: 1px solid var(--room-border);
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .enhancement-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            border-color: var(--room-accent);
        }

        .enhancement-details h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: var(--room-text);
        }

        .enhancement-price {
            font-size: 14px;
            font-weight: 700;
            color: var(--room-accent);
            background: rgba(59, 130, 246, 0.1);
            padding: 4px 10px;
            border-radius: 100px;
        }

        /* Amenities Grid */
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 16px;
        }

        .amenity-card {
            background: var(--room-surface);
            border: 1px solid var(--room-border);
            border-radius: 12px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }

        .amenity-card:hover {
            background: rgba(59, 130, 246, 0.03);
            border-color: var(--room-accent);
        }

        .amenity-icon {
            font-size: 18px;
            color: var(--room-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            flex-shrink: 0;
        }

        .amenity-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .amenity-name {
            font-weight: 600;
            font-size: 14px;
        }

        /* --- Right Column: Booking Form --- */
        .booking-sidebar-wrapper {
            position: sticky;
            top: 100px;
            /* Adjust based on your header height */
        }

        .glass-booking-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08), inset 0 0 0 1px rgba(255, 255, 255, 0.2);
        }

        .booking-price-header {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--room-border);
        }

        .booking-price {
            font-size: 26px;
            font-weight: 800;
            color: var(--room-accent);
        }

        .booking-price span {
            font-size: 14px;
            font-weight: 500;
            color: var(--room-muted);
        }

        .booking-form .input-group {
            margin-bottom: 16px;
        }

        .booking-form label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            color: var(--room-muted);
        }

        .booking-form input,
        .booking-form select {
            box-sizing: border-box;
            /* Fix for overlap */
            min-width: 0;
            /* Fix flex overflow */
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1.5px solid var(--room-border);
            background: var(--room-surface);
            color: var(--room-text);
            font-size: 14px;
            font-weight: 600;
            outline: none;
            transition: all 0.2s;
        }

        .booking-form input:focus,
        .booking-form select:focus {
            border-color: var(--room-accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .date-row {
            display: flex;
            gap: 12px;
        }

        .date-row .input-group {
            flex: 1;
        }

        .btn-reserve {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 100px;
            background: linear-gradient(135deg, var(--room-accent), #6366f1);
            color: #fff;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.25);
            margin-top: 4px;
        }

        .btn-reserve:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(59, 130, 246, 0.4);
        }

        .charge-note {
            text-align: center;
            font-size: 13px;
            color: var(--room-muted);
            margin-top: 16px;
        }

        /* --- Video Section --- */
        .room-video-container {
            margin-top: 80px;
            margin-bottom: 40px;
            border-radius: 32px;
            overflow: hidden;
            background: #000;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .room-video-container video,
        .room-video-container iframe {
            width: 100%;
            aspect-ratio: 16 / 9;
            display: block;
            border: none;
        }

        .video-overlay-title {
            position: absolute;
            top: 30px;
            left: 30px;
            color: #fff;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            padding: 10px 24px;
            border-radius: 100px;
            font-weight: 700;
            letter-spacing: 1px;
            z-index: 10;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .room-layout-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .booking-sidebar-wrapper {
                position: relative;
                top: 0;
                order: -1;
                /* Put booking form above details on mobile */
            }

            .room-hero-slider {
                height: 40vh;
                min-height: 300px;
            }
        }

        @media (max-width: 768px) {
            .room-show-page {
                padding: 16px;
            }

            .room-hero-section {
                border-radius: 16px;
            }

            .room-title {
                font-size: 28px;
            }

            .room-header-block {
                flex-direction: column;
                align-items: flex-start;
                gap: 22px;
            }

            .date-row {
                flex-direction: column;
                gap: 0;
            }

            .amenities-grid {
                grid-template-columns: 1fr 1fr;
            }

            .enhancements-container {
                grid-template-columns: 1fr;
            }
        }

        /* --- Sleeping Arrangements in Room Show Page --- */
        .bedrooms-display-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .bedroom-display-card {
            background: var(--room-surface);
            border: 1px solid var(--room-border);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .bedroom-display-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            border-color: rgba(59, 130, 246, 0.25);
        }

        .bedroom-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            padding-bottom: 12px;
        }

        .bed-icon-wrapper {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: rgba(59, 130, 246, 0.08);
            color: var(--room-accent);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .bedroom-display-title {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            color: var(--room-text);
        }

        .bedroom-display-body {
            flex-grow: 1;
        }

        .bed-types-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .bed-type-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14.5px;
            font-weight: 600;
            color: var(--room-text);
        }

        .bed-image-icon {
            width: 20px;
            height: 20px;
            object-fit: contain;
        }

        .bed-default-icon {
            color: var(--room-muted);
            font-size: 14px;
        }

        .bedroom-card-footer {
            margin-top: 18px;
            padding-top: 10px;
            border-top: 1px dashed var(--room-border);
            font-size: 13px;
            font-weight: 700;
            color: var(--room-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* --- Modern Price Structure & Special Offers --- */
        .price-structure-card {
            background: var(--room-surface);
            border: 1px solid var(--room-border);
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            margin-top: 20px;
        }

        .price-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .summary-box {
            background: rgba(99, 102, 241, 0.02);
            border: 1px solid var(--room-border);
            border-radius: 16px;
            padding: 16px;
            transition: all 0.2s ease;
        }

        .summary-box:hover {
            transform: translateY(-2px);
            background: rgba(99, 102, 241, 0.04);
            border-color: rgba(99, 102, 241, 0.2);
        }

        .box-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--room-muted);
            display: block;
            margin-bottom: 6px;
        }

        .box-value {
            font-size: 20px;
            font-weight: 800;
            color: var(--room-text);
            margin: 0;
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .box-value small {
            font-size: 12px;
            font-weight: 600;
            color: var(--room-muted);
        }

        .discounts-header-divider {
            display: flex;
            align-items: center;
            margin: 24px 0 16px 0;
            position: relative;
        }

        .discounts-header-divider::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--room-border);
            z-index: 1;
        }

        .divider-title {
            background: var(--room-surface);
            padding: 0 16px;
            font-size: 13px;
            font-weight: 700;
            color: var(--room-muted);
            position: relative;
            z-index: 2;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0 auto;
        }

        .active-discounts-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        @media (min-width: 768px) {
            .active-discounts-list {
                grid-template-columns: repeat(2, 1fr);
            }

            .discount-badge-card:last-child:nth-child(odd) {
                grid-column: span 2;
            }
        }

        /* --- Modern Property Highlights Chips --- */
        .property-highlights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 10px;
        }

        .highlight-chip-card {
            background: var(--room-surface);
            border: 1.5px solid var(--room-border);
            border-radius: 18px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        }

        .highlight-chip-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
            border-color: var(--room-accent);
        }

        .chip-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .prop-type-icon {
            background: rgba(99, 102, 241, 0.08);
            color: #4f46e5;
        }

        .space-type-icon {
            background: rgba(16, 185, 129, 0.08);
            color: #059669;
        }

        .bed-count-icon {
            background: rgba(245, 158, 11, 0.08);
            color: #d97706;
        }

        .chip-content {
            display: flex;
            flex-direction: column;
        }

        .chip-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--room-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .chip-value {
            font-size: 14.5px;
            font-weight: 700;
            color: var(--room-text);
            margin: 0;
        }

        .discount-badge-card {
            display: flex;
            align-items: center;
            gap: 16px;
            border-radius: 16px;
            padding: 16px;
            border: 1.5px solid transparent;
            transition: all 0.25s ease;
        }

        .discount-badge-card:hover {
            transform: scale(1.01);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
        }

        .badge-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .badge-details {
            flex-grow: 1;
        }

        .badge-details h4 {
            font-size: 14.5px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }

        .badge-details p {
            font-size: 13px;
            color: var(--room-muted);
            margin: 0;
            line-height: 1.4;
        }

        .badge-tag {
            padding: 6px 12px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Semantic colors for each discount type */
        /* 1. Last Minute: Vibrant Coral/Orange */
        .last-minute-card {
            background: rgba(249, 115, 22, 0.05);
            border-color: rgba(249, 115, 22, 0.15);
        }

        .last-minute-card .badge-icon {
            background: rgba(249, 115, 22, 0.1);
            color: #ea580c;
        }

        .last-minute-card h4 {
            color: #ea580c;
        }

        .last-minute-card .badge-tag {
            background: #ffedd5;
            color: #ea580c;
        }

        /* 2. Early Bird: Fresh Green/Emerald */
        .early-bird-card {
            background: rgba(16, 185, 129, 0.05);
            border-color: rgba(16, 185, 129, 0.15);
        }

        .early-bird-card .badge-icon {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .early-bird-card h4 {
            color: #059669;
        }

        .early-bird-card .badge-tag {
            background: #d1fae5;
            color: #059669;
        }

        /* 3. Length of Stay: Sky Blue/Indigo */
        .length-of-stay-card {
            background: rgba(99, 102, 241, 0.05);
            border-color: rgba(99, 102, 241, 0.15);
        }

        .length-of-stay-card .badge-icon {
            background: rgba(99, 102, 241, 0.1);
            color: #4f46e5;
        }

        .length-of-stay-card h4 {
            color: #4f46e5;
        }

        .length-of-stay-card .badge-tag {
            background: #e0e7ff;
            color: #4f46e5;
        }

        /* 4. Custom/Seasonal: Elegant Purple/Gold */
        .custom-period-card {
            background: rgba(217, 70, 239, 0.05);
            border-color: rgba(217, 70, 239, 0.15);
        }

        .custom-period-card .badge-icon {
            background: rgba(217, 70, 239, 0.1);
            color: #c026d3;
        }

        .custom-period-card h4 {
            color: #c026d3;
        }

        .custom-period-card .badge-tag {
            background: #fae8ff;
            color: #c026d3;
        }

        /* --- Leaflet Map & Directions Styling --- */
        .room-map-wrapper {
            position: relative;
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
            border: 1.5px solid var(--room-border);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.04);
            margin-top: 10px;
        }

        .map-direction-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            z-index: 499;
            /* Must be above Leaflet controls */
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            color: #0f172a;
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .map-direction-btn:hover {
            transform: translateY(-2px);
            background: #ffffff;
            color: #4f46e5;
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.2);
            border-color: rgba(79, 70, 229, 0.3);
        }

        body.dark-mode .map-direction-btn {
            background: rgba(15, 23, 42, 0.8);
            border-color: rgba(255, 255, 255, 0.1);
            color: #f1f5f9;
        }

        body.dark-mode .map-direction-btn:hover {
            background: #0f172a;
            color: #818cf8;
            box-shadow: 0 12px 30px rgba(129, 140, 248, 0.25);
            border-color: rgba(129, 140, 248, 0.4);
        }

        .map-info-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            z-index: 499;
            /* Must be above Leaflet controls */
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            color: #0f172a;
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.dark-mode .map-info-badge {
            background: rgba(15, 23, 42, 0.8);
            border-color: rgba(255, 255, 255, 0.1);
            color: #f1f5f9;
        }

        /* Custom styles to match theme with Leaflet circular highlight */
        .leaflet-circle {
            animation: pulse-circle 3s infinite ease-in-out;
        }

        @keyframes pulse-circle {
            0% {
                fill-opacity: 0.12;
            }

            50% {
                fill-opacity: 0.22;
            }

            100% {
                fill-opacity: 0.12;
            }
        }
    </style>
@endsection

@section('content')
    <div class="room-show-page">

        <!-- Hero Slider -->
        @if($room->photos->count() > 0)
            <div class="room-hero-section">
                <div class="swiper room-hero-slider">
                    <div class="swiper-wrapper">
                        @foreach($room->photos as $photo)
                            <div class="swiper-slide">
                                <a data-fancybox="gallery" data-caption="{{ $photo->description ?? $room->title }}"
                                    href="{{ asset('storage/' . $photo->photo_path) }}">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                        alt="{{ $room->title }} - Photo {{ $loop->iteration }}">
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <!-- Add Pagination & Navigation -->
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
        @endif

        <!-- Full Width Header Block -->
        <div class="room-header-block">
            <div>
                <div
                    style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--room-accent); letter-spacing: 1.5px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-sparkles"></i> Top Rated Experience
                </div>
                <h1 class="room-title">{{ $room->title ?: ($room->name ?: 'Premium Property') }}</h1>
                <div class="room-meta">
                    @if($room->city || $room->country)
                        <span><i class="fas fa-map-marker-alt"></i>
                            {{ $room->city }}{{ $room->city && $room->country ? ',' : '' }} {{ $room->country }}</span>
                    @endif
                    <span><i class="fas fa-users"></i> Up to {{ $room->accommodation ?: '2' }} guests</span>
                    <span><i class="fas fa-home"></i> {{ optional($room->propertyType)->name ?: 'Luxury Space' }}</span>
                </div>
            </div>

            <div class="host-badge">
                <div class="host-avatar-wrapper">
                    <div class="host-avatar">
                        @if($room->user && $room->user->profile_image)
                            @php
                                $profilePath = file_exists(public_path($room->user->profile_image))
                                    ? asset($room->user->profile_image)
                                    : asset('storage/' . $room->user->profile_image);
                            @endphp
                            <img src="{{ $profilePath }}" alt="{{ $room->user->name }}">
                        @else
                            {{ strtoupper(substr($room->user->name ?? 'H', 0, 1)) }}
                        @endif
                    </div>
                    <div class="verified-badge" title="Verified Host">
                        <img src="{{ asset('images/email_verify.png') }}" alt="Verified">
                    </div>
                </div>
                <div class="host-details">
                    <h4>Hosted by {{ $room->user->name ?? 'Host' }}</h4>
                    <p style="margin:0; font-size:12px; color:var(--room-muted); font-weight:600;">Verified Partner</p>
                </div>
            </div>
        </div>

        <!-- Main Layout -->
        <div class="room-layout-grid">

            <!-- Left: Room Details -->
            <div class="room-main-info">

                <!-- 1. About this space -->
                @if($room->description)
                    <div class="room-section">
                        <h2 class="section-title"><i class="fas fa-align-left"></i> About this space</h2>
                        <div class="room-description">
                            {{ $room->description }}
                        </div>
                    </div>
                @endif

                <!-- 1b. Property Type, Space Type, and Bedroom Count -->
                <div class="room-section property-highlights-section">
                    <div class="property-highlights-grid">
                        <div class="highlight-chip-card">
                            <div class="chip-icon-wrapper prop-type-icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="chip-content">
                                <span class="chip-label">Property Type</span>
                                <h4 class="chip-value">{{ optional($room->propertyType)->name ?: 'Luxury Rental' }}</h4>
                            </div>
                        </div>

                        <div class="highlight-chip-card">
                            <div class="chip-icon-wrapper space-type-icon">
                                <i class="fas fa-hotel"></i>
                            </div>
                            <div class="chip-content">
                                <span class="chip-label">Space Type</span>
                                <h4 class="chip-value">{{ $room->space_type ?: 'Entire Space' }}</h4>
                            </div>
                        </div>

                        @php
                            $bedroomCount = $room->bedroomBeds->groupBy('bedroom_index')->count() ?: 1;
                        @endphp
                        <div class="highlight-chip-card">
                            <div class="chip-icon-wrapper bed-count-icon">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <div class="chip-content">
                                <span class="chip-label">Bedrooms</span>
                                <h4 class="chip-value">{{ $bedroomCount }} Bedroom{{ $bedroomCount > 1 ? 's' : '' }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Premium Amenities & Comforts -->
                @if($room->amenities->count() > 0)
                    <div class="room-section">
                        <h2 class="section-title"><i class="fas fa-sparkles"></i> Premium Amenities & Comforts</h2>
                        <div class="amenities-grid">
                            @foreach($room->amenities as $amenity)
                                <div class="amenity-card">
                                    <div class="amenity-icon">
                                        @if($amenity->image)
                                            <img src="{{ Storage::url($amenity->image) }}" alt="{{ $amenity->name }}">
                                        @else
                                            <i class="fas fa-check"></i>
                                        @endif
                                    </div>
                                    <div class="amenity-name">{{ $amenity->name }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 3. Sleeping Arrangements -->
                @if($room->bedroomBeds->count() > 0)
                    <div class="room-section sleeping-arrangements-section">
                        <h2 class="section-title"><i class="fas fa-bed"></i> Sleeping Arrangements</h2>
                        <div class="bedrooms-display-grid">
                            @php
                                $groupedBeds = $room->bedroomBeds->groupBy('bedroom_index');
                            @endphp
                            @foreach($groupedBeds as $bedroomIdx => $beds)
                                <div class="bedroom-display-card">
                                    <div>
                                        <div class="bedroom-card-header">
                                            <div class="bed-icon-wrapper">
                                                <i class="fas fa-door-open"></i>
                                            </div>
                                            <h3 class="bedroom-display-title">Bedroom {{ $bedroomIdx }}</h3>
                                        </div>
                                        <div class="bedroom-display-body">
                                            <ul class="bed-types-list">
                                                @php $totalBeds = 0; @endphp
                                                @foreach($beds as $bed)
                                                    @php $totalBeds += $bed->count; @endphp
                                                    <li class="bed-type-item">
                                                        @if($bed->bedType->image)
                                                            <img src="{{ Storage::url($bed->bedType->image) }}" class="bed-image-icon"
                                                                alt="{{ $bed->bedType->name }}">
                                                        @else
                                                            <i class="fas fa-bed bed-default-icon"></i>
                                                        @endif
                                                        <span>{{ $bed->count }} {{ $bed->bedType->name }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="bedroom-card-footer">
                                        <span>{{ $totalBeds }} bed{{ $totalBeds == 1 ? '' : 's' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 4. Dining & Services -->
                @if($room->enhancements && $room->enhancements->where('is_active', true)->count() > 0)
                    <div class="room-section">
                        <h2 class="section-title"><i class="fas fa-concierge-bell"></i> Dining & Services</h2>

                        @php
                            $groupedEnhancements = $room->enhancements->where('is_active', true)->groupBy(function ($item) {
                                return ucfirst(strtolower($item->type));
                            });
                        @endphp

                        <div class="enhancements-container">
                            @foreach($groupedEnhancements as $type => $items)
                                @php
                                    $isPerGuest = $items->where('is_per_guest', true)->count() > 0;
                                    $perGuestPrice = $isPerGuest ? $items->where('is_per_guest', true)->first()->price : 0;
                                @endphp
                                <div class="enhancement-group">
                                    <h3 class="enhancement-type-title">
                                        @php
                                            $t = strtolower($type);
                                            if (str_contains($t, 'breakfast'))
                                                echo '<i class="fas fa-coffee"></i>';
                                            elseif (str_contains($t, 'lunch'))
                                                echo '<i class="fas fa-hamburger"></i>';
                                            elseif (str_contains($t, 'dinner'))
                                                echo '<i class="fas fa-utensils"></i>';
                                            else
                                                echo '<i class="fas fa-star"></i>';
                                        @endphp
                                        @if($isPerGuest)
                                            {{ $type }} Service
                                        @else
                                            {{ $type }} Menu
                                        @endif
                                    </h3>

                                    @if($isPerGuest)
                                        <div class="enhancement-per-guest-banner"
                                            style="display: flex; align-items: center; gap: 10px; background: rgba(16, 185, 129, 0.08); border: 1.5px solid rgba(16, 185, 129, 0.15); border-radius: 12px; padding: 10px 12px; margin-top: 10px; width: 100%;">
                                            <div
                                                style="width: 32px; height: 32px; border-radius: 50%; background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 14px; flex-shrink: 0;">
                                                <i class="fas fa-user-tag"></i>
                                            </div>
                                            <div>
                                                <div
                                                    style="font-size: 9px; color: var(--room-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    Price per guest</div>
                                                <div
                                                    style="font-family: 'Outfit', sans-serif; font-size: 15px; font-weight: 800; color: #10b981; margin-top: 1px;">
                                                    {{ $room->currency_symbol }}{{ number_format($perGuestPrice, 0) }}/guest
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="enhancements-grid">
                                            @foreach($items as $enhancement)
                                                <div class="enhancement-card">
                                                    <div class="enhancement-details">
                                                        <h4>{{ $enhancement->item_name }}</h4>
                                                    </div>
                                                    <div class="enhancement-price">
                                                        {{ $room->currency_symbol }}{{ number_format($enhancement->price, 2) }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 5. Special Rates & Pricing Structure (At the very end) -->
                @if(isset($room->roomPrice))
                    <div class="room-section price-structure-section">
                        <h2 class="section-title"><i class="fas fa-tags"></i> Special Rates & Pricing Structure</h2>

                        <div class="price-structure-card">
                            <div class="price-summary-grid">
                                <div class="summary-box base-price-box">
                                    <span class="box-label">Base Rate</span>
                                    <h3 class="box-value">
                                        {{ $room->currency_symbol }}{{ number_format($room->roomPrice->price ?? $room->price, 2) }}
                                        <small>/ Night</small>
                                    </h3>
                                </div>

                                @if(isset($room->roomPrice->tax_amount) && $room->roomPrice->tax_amount > 0)
                                    <div class="summary-box tax-box">
                                        <span class="box-label">Taxes</span>
                                        <h3 class="box-value">
                                            @if($room->roomPrice->tax_type === 'percentage')
                                                {{ number_format($room->roomPrice->tax_amount, 1) }}%
                                            @else
                                                {{ $room->currency_symbol }}{{ number_format($room->roomPrice->tax_amount, 2) }}
                                            @endif
                                            <small
                                                style="font-size:10px;">({{ ($room->roomPrice->is_tax_included ?? true) ? 'Included' : 'Added' }})</small>
                                        </h3>
                                    </div>
                                @endif

                                @if(isset($room->roomPrice->security_deposit) && $room->roomPrice->security_deposit > 0)
                                    <div class="summary-box deposit-box">
                                        <span class="box-label">Security Deposit</span>
                                        <h3 class="box-value">
                                            {{ $room->currency_symbol }}{{ number_format($room->roomPrice->security_deposit, 2) }}
                                            <small style="font-size:10px;">Refundable</small>
                                        </h3>
                                    </div>
                                @endif
                            </div>

                            {{-- Additional Fees & Charges --}}
                            @php
                                $additionalPricing = $room->roomPrice->additional_pricing ?? [];
                                $cleaningFee = $additionalPricing['cleaning_fee'] ?? null;
                                $additionalGuests = $additionalPricing['additional_guests'] ?? null;
                                $weekendPricing = $additionalPricing['weekend_pricing'] ?? null;

                                $serviceFeePct = (float) \App\Models\SiteSetting::get('service_fee', 0);
                                $basePriceVal = (float) ($room->roomPrice->price ?? $room->price);
                                $serviceFeeAmt = $basePriceVal * ($serviceFeePct / 100);

                                $hasAnyAdditional = ($cleaningFee && ($cleaningFee['active'] ?? false) && ($cleaningFee['amount'] ?? 0) > 0) ||
                                    ($additionalGuests && ($additionalGuests['active'] ?? false) && ($additionalGuests['amount'] ?? 0) > 0) ||
                                    ($weekendPricing && ($weekendPricing['active'] ?? false) && ($weekendPricing['amount'] ?? 0) > 0 && ($weekendPricing['amount'] ?? 0) != $basePriceVal) ||
                                    ($serviceFeePct > 0);
                            @endphp

                            @if($hasAnyAdditional)
                                <div
                                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-top: 16px;">
                                    {{-- 1. Cleaning Fee --}}
                                    @if($cleaningFee && ($cleaningFee['active'] ?? false) && ($cleaningFee['amount'] ?? 0) > 0)
                                        <div class="summary-box"
                                            style="display: flex; align-items: center; gap: 10px; padding: 12px 16px;">
                                            <div
                                                style="width: 32px; height: 32px; border-radius: 8px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                                <i class="fas fa-broom"></i>
                                            </div>
                                            <div>
                                                <span class="box-label"
                                                    style="font-size: 10px; margin-bottom: 2px; text-transform: uppercase;">Cleaning
                                                    Fee</span>
                                                <h4 style="font-size: 15px; font-weight: 700; margin: 0; color: var(--room-text);">
                                                    {{ $room->currency_symbol }}{{ number_format($cleaningFee['amount'], 2) }}
                                                </h4>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- 2. Additional Guests --}}
                                    @if($additionalGuests && ($additionalGuests['active'] ?? false) && ($additionalGuests['amount'] ?? 0) > 0)
                                        <div class="summary-box"
                                            style="display: flex; align-items: center; gap: 10px; padding: 12px 16px;">
                                            <div
                                                style="width: 32px; height: 32px; border-radius: 8px; background: #f0f9ff; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div>
                                                <span class="box-label"
                                                    style="font-size: 10px; margin-bottom: 2px; text-transform: uppercase;">Extra Guest
                                                    <small style="font-size:8px;">after
                                                        {{ $additionalGuests['after_guests'] ?? 2 }}</small></span>
                                                <h4 style="font-size: 15px; font-weight: 700; margin: 0; color: var(--room-text);">
                                                    {{ $room->currency_symbol }}{{ number_format($additionalGuests['amount'], 2) }}<small
                                                        style="font-size: 12px; font-weight: normal; color: var(--room-muted);">/night</small>
                                                </h4>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- 3. Weekend Pricing --}}
                                    @if($weekendPricing && ($weekendPricing['active'] ?? false) && ($weekendPricing['amount'] ?? 0) > 0 && ($weekendPricing['amount'] ?? 0) != $basePriceVal)
                                        <div class="summary-box"
                                            style="display: flex; align-items: center; gap: 10px; padding: 12px 16px;">
                                            <div
                                                style="width: 32px; height: 32px; border-radius: 8px; background: #fdf4ff; color: #9333ea; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                                <i class="fas fa-calendar-week"></i>
                                            </div>
                                            <div>
                                                <span class="box-label"
                                                    style="font-size: 10px; margin-bottom: 2px; text-transform: uppercase;">Weekend
                                                    Price <small style="font-size:8px;">Fri/Sat</small></span>
                                                <h4 style="font-size: 15px; font-weight: 700; margin: 0; color: var(--room-text);">
                                                    {{ $room->currency_symbol }}{{ number_format($weekendPricing['amount'], 2) }}
                                                </h4>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- 4. Service Fee --}}
                                    @if($serviceFeePct > 0)
                                        <div class="summary-box"
                                            style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background: rgba(99, 102, 241, 0.03); border-color: rgba(99, 102, 241, 0.15);">
                                            <div
                                                style="width: 32px; height: 32px; border-radius: 8px; background: #e0e7ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                                <i class="fas fa-percent"></i>
                                            </div>
                                            <div>
                                                <span class="box-label"
                                                    style="font-size: 10px; margin-bottom: 2px; text-transform: uppercase;">Service Fee
                                                    ({{ $serviceFeePct }}%)</span>
                                                <h4 style="font-size: 15px; font-weight: 700; margin: 0; color: var(--room-text);">
                                                    {{ $room->currency_symbol }}{{ number_format($serviceFeeAmt, 2) }}
                                                </h4>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @php
                                $discounts = $room->roomPrice->discounts;
                                $hasAnyDiscount = false;
                                if ($discounts) {
                                    $lm = $discounts['last_minute'] ?? null;
                                    $eb = $discounts['early_bird'] ?? null;
                                    $los = $discounts['length_of_stay'] ?? null;
                                    $cust = $discounts['custom'] ?? null;
                                    if (
                                        ($lm && ($lm['active'] ?? false)) ||
                                        ($eb && ($eb['active'] ?? false) && count($eb['rules'] ?? []) > 0) ||
                                        ($los && ($los['active'] ?? false) && count($los['rules'] ?? []) > 0) ||
                                        ($cust && ($cust['active'] ?? false) && count($cust['rules'] ?? []) > 0)
                                    ) {
                                        $hasAnyDiscount = true;
                                    }
                                }
                            @endphp

                            @if($hasAnyDiscount)
                                <div class="discounts-header-divider">
                                    <span class="divider-title">Available Special Deals</span>
                                </div>

                                <div class="active-discounts-list">
                                    {{-- 1. Last Minute Discount --}}
                                    @if(isset($discounts['last_minute']) && ($discounts['last_minute']['active'] ?? false))
                                        <div class="discount-badge-card last-minute-card animate__animated animate__fadeIn">
                                            <div class="badge-icon"><i class="fas fa-bolt"></i></div>
                                            <div class="badge-details">
                                                <h4>Last-Minute Booking Deal</h4>
                                                <p>Book within <strong>{{ $discounts['last_minute']['days'] ?? 14 }} days</strong> of
                                                    check-in to get an instant discount.</p>
                                            </div>
                                            <div class="badge-tag">
                                                <span>{{ $discounts['last_minute']['percentage'] ?? 10 }}% OFF</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- 2. Early Bird Discount --}}
                                    @if(isset($discounts['early_bird']) && ($discounts['early_bird']['active'] ?? false) && count($discounts['early_bird']['rules'] ?? []) > 0)
                                        @foreach($discounts['early_bird']['rules'] as $rule)
                                            <div class="discount-badge-card early-bird-card animate__animated animate__fadeIn">
                                                <div class="badge-icon"><i class="fas fa-dove"></i></div>
                                                <div class="badge-details">
                                                    <h4>Early-Bird Special Promotion</h4>
                                                    <p>Secure this room <strong>{{ $rule['days_ahead'] ?? $rule['days'] ?? 30 }} days or
                                                            more</strong> in advance for extra savings.</p>
                                                </div>
                                                <div class="badge-tag">
                                                    <span>{{ $rule['percentage'] ?? 10 }}% OFF</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    {{-- 3. Length of Stay Discount --}}
                                    @if(isset($discounts['length_of_stay']) && ($discounts['length_of_stay']['active'] ?? false) && count($discounts['length_of_stay']['rules'] ?? []) > 0)
                                        @foreach($discounts['length_of_stay']['rules'] as $rule)
                                            @php
                                                $nightsCount = $rule['nights'] ?? 7;
                                                $durationLabel = $nightsCount >= 7 ? (($nightsCount >= 28 ? 'Monthly stay' : 'Weekly stay') . ' (' . $nightsCount . '+ nights)') : ($nightsCount . '+ nights stay');
                                            @endphp
                                            <div class="discount-badge-card length-of-stay-card animate__animated animate__fadeIn">
                                                <div class="badge-icon"><i class="fas fa-hourglass-half"></i></div>
                                                <div class="badge-details">
                                                    <h4>{{ $durationLabel }}</h4>
                                                    <p>Automatically save big when staying for extended periods.</p>
                                                </div>
                                                <div class="badge-tag">
                                                    <span>{{ $rule['percentage'] ?? 10 }}% OFF</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    {{-- 4. Custom Discount --}}
                                    @if(isset($discounts['custom']) && ($discounts['custom']['active'] ?? false))
                                        @php
                                            $today = strtotime(date('Y-m-d'));
                                            $activeRules = [];
                                            $hasExpired = false;

                                            if (!empty($discounts['custom']['rules'])) {
                                                foreach ($discounts['custom']['rules'] as $rule) {
                                                    if (!empty($rule['start_date']) && !empty($rule['end_date'])) {
                                                        if (strtotime($rule['end_date']) < $today) {
                                                            $hasExpired = true;
                                                        } else {
                                                            $activeRules[] = $rule;
                                                        }
                                                    }
                                                }
                                            }
                                         @endphp

                                        @if(count($activeRules) > 0)
                                            @foreach($activeRules as $rule)
                                                <div class="discount-badge-card custom-period-card animate__animated animate__fadeIn">
                                                    <div class="badge-icon"><i class="fas fa-umbrella-beach"></i></div>
                                                    <div class="badge-details">
                                                        <h4>Seasonal / Promotional Window</h4>
                                                        <p>Valid for stays between
                                                            <strong>{{ date('M d, Y', strtotime($rule['start_date'])) }}</strong> and
                                                            <strong>{{ date('M d, Y', strtotime($rule['end_date'])) }}</strong>.
                                                        </p>
                                                    </div>
                                                    <div class="badge-tag">
                                                        <span>{{ $rule['percentage'] ?? 20 }}% OFF</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="discount-badge-card custom-period-card animate__animated animate__fadeIn"
                                                style="border-color: var(--accent); background: linear-gradient(135deg, rgba(168, 85, 247, 0.08), rgba(99, 102, 241, 0.08));">
                                                <div class="badge-icon" style="color: var(--accent);"><i
                                                        class="fas fa-envelope-open-text"></i></div>
                                                <div class="badge-details">
                                                    <h4>Comming soon stay tuined with email</h4>
                                                    <p>Our seasonal custom discount window has concluded. Stay tuned for upcoming premium
                                                        offers!</p>
                                                </div>
                                                <div class="badge-tag" style="background: var(--accent); color: #fff;">
                                                    <span>SOON</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- House Rules, Booking Options & Cancellation Policy -->
                <div class="room-section rules-cancellation-section">
                    <h2 class="section-title"><i class="fas fa-gavel"></i> House Rules & Policies</h2>

                    <div style="display: flex; flex-direction: column; gap: 24px; margin-top: 20px;">

                        <!-- 1. Terms & Policies (First) -->
                        <div
                            style="background: var(--room-surface); border: 1px solid var(--room-border); border-radius: 24px; padding: 24px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);">
                            <h3
                                style="font-size: 18px; font-weight: 800; color: var(--room-text); margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-shield-alt" style="color: var(--room-accent);"></i> Terms & Policies
                            </h3>

                            <div
                                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 16px;">
                                <!-- Booking Option Type -->
                                <div
                                    style="display: flex; align-items: center; gap: 12px; padding: 14px; background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.15); border-radius: 16px;">
                                    <div
                                        style="width: 36px; height: 36px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 15px;">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                    <div>
                                        <span
                                            style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--room-muted); display: block; letter-spacing: 0.5px;">Booking
                                            Method</span>
                                        <strong
                                            style="font-size: 14.5px; color: var(--room-text); font-weight: 700;">{{ $room->booking_type ?: 'Instant Booking' }}</strong>
                                    </div>
                                </div>

                                <!-- Checkout Policy Time -->
                                <div
                                    style="display: flex; align-items: center; gap: 12px; padding: 14px; background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.15); border-radius: 16px;">
                                    <div
                                        style="width: 36px; height: 36px; border-radius: 50%; background: rgba(59, 130, 246, 0.1); color: var(--room-accent); display: flex; align-items: center; justify-content: center; font-size: 15px;">
                                        <i class="far fa-clock"></i>
                                    </div>
                                    <div>
                                        <span
                                            style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--room-muted); display: block; letter-spacing: 0.5px;">Checkout
                                            Time</span>
                                        <strong
                                            style="font-size: 14.5px; color: var(--room-text); font-weight: 700;">{{ $room->checkout_policy ?: '11:00 AM' }}</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Cancellation policy and descriptions -->
                            <div
                                style="padding: 16px; background: rgba(255,255,255,0.03); border: 1px solid var(--room-border); border-radius: 16px;">
                                <span
                                    style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--room-muted); display: block; margin-bottom: 6px; letter-spacing: 0.5px;">Cancellation
                                    Policy</span>

                                @if($room->custom_cancellation)
                                    <strong
                                        style="font-size: 16px; color: var(--room-accent); font-weight: 800; display: block; margin-bottom: 6px;">
                                        Custom Policy ({{ $room->free_cancellation_days }} Days Free)
                                    </strong>
                                    <p
                                        style="font-size: 13px; color: var(--room-text); margin: 0; line-height: 1.5; opacity: 0.85;">
                                        Guests receive a 100% free cancellation until
                                        <strong>{{ $room->free_cancellation_days }} days</strong> before check-in.
                                        Cancellations made afterwards will incur a <strong>{{ $room->cancellation_fee }}%
                                            penalty fee</strong> based on the booking's base price.
                                    </p>
                                @else
                                    @php
                                        $policy = $room->cancellation_policy ?: 'Flexible';
                                    @endphp
                                    <strong
                                        style="font-size: 16px; color: var(--room-text); font-weight: 800; display: block; margin-bottom: 6px;">
                                        {{ $policy }} Policy
                                    </strong>
                                    <p
                                        style="font-size: 13px; color: var(--room-text); margin: 0; line-height: 1.5; opacity: 0.85;">
                                        @if($policy === 'Flexible')
                                            Free cancellation up to 24 hours before check-in. Cancellations made within 24 hours
                                            are non-refundable.
                                        @elseif($policy === 'Moderate')
                                            Free cancellation up to 5 days before check-in. Cancellations made within 5 days
                                            incur a 50% penalty fee.
                                        @else
                                            Strict cancellation rules. 50% refund up to 14 days before check-in, non-refundable
                                            afterwards.
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- 2. Official House Rules (Second) -->
                        <div
                            style="background: var(--room-surface); border: 1px solid var(--room-border); border-radius: 24px; padding: 24px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);">
                            <h3
                                style="font-size: 18px; font-weight: 800; color: var(--room-text); margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-clipboard-list" style="color: var(--room-accent);"></i> Official House
                                Rules
                            </h3>

                            @php
                                $selectedRuleIds = is_array($room->selected_rules) ? $room->selected_rules : [];
                                $selectedRules = \App\Models\RoomRule::whereIn('id', $selectedRuleIds)->get();
                            @endphp

                            @if($selectedRules->count() > 0)
                                <div style="display: flex; flex-direction: column; gap: 14px;">
                                    <div
                                        style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px;">
                                        @foreach($selectedRules as $index => $rule)
                                            <div class="house-rule-item" data-index="{{ $index }}"
                                                style="display: {{ $index >= 4 ? 'none' : 'flex' }}; align-items: flex-start; gap: 12px; padding: 12px 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--room-border); border-radius: 14px; transition: all 0.3s ease;">
                                                <div
                                                    style="width: 32px; height: 32px; border-radius: 8px; background: rgba(59, 130, 246, 0.08); color: var(--room-accent); display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                                    <i class="{{ $rule->icon ?: 'fas fa-info' }}"></i>
                                                </div>
                                                <div style="flex-grow: 1;">
                                                    <h4
                                                        style="font-size: 14px; font-weight: 700; color: var(--room-text); margin: 0 0 2px 0;">
                                                        {{ $rule->rule_name }}
                                                    </h4>
                                                    <p
                                                        style="font-size: 12px; color: var(--room-muted); margin: 0; line-height: 1.4;">
                                                        {{ $rule->rule_text }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($selectedRules->count() > 4)
                                        <div style="text-align: center; margin-top: 16px;">
                                            <button type="button" id="btn-toggle-show-rules" onclick="toggleShowAllRules()"
                                                style="background: transparent; border: 1.5px solid var(--room-accent); color: var(--room-accent); padding: 8px 24px; border-radius: 12px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s ease; font-family: 'Outfit', sans-serif;">
                                                Show More <i class="fas fa-chevron-down ms-1" style="font-size: 11px;"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div
                                    style="padding: 16px; text-align: center; color: var(--room-muted); font-size: 14px; border: 1px dashed var(--room-border); border-radius: 16px;">
                                    <i class="fas fa-check-circle"
                                        style="font-size: 20px; color: #10b981; margin-bottom: 8px; display: block;"></i>
                                    No specific house rules listed. Enjoy your stay!
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 6. Interactive Leaflet Map & Directions Block -->
                <div class="room-section room-location-map-section">
                    <h2 class="section-title"><i class="fas fa-map-marked-alt"></i> Where you'll be</h2>
                    <div class="room-map-wrapper">
                        <div id="room-map" style="height: 380px; z-index: 1;"></div>
                        <button id="map-direction-btn" class="map-direction-btn">
                            <i class="fas fa-route"></i> Direction
                        </button>
                    </div>
                </div>

            </div>

            <!-- Right: Glass Booking Form -->
            <div class="booking-sidebar-wrapper">
                <div class="glass-booking-card">
                    <div class="booking-price-header">
                        <div class="booking-price" id="bookingHeaderPrice">
                            {{ $room->currency_symbol }}{{ number_format($room->price, 2) }} <span>/ Night</span>
                        </div>
                    </div>

                    <form class="booking-form" action="{{ route('rooms.booking_page', $room->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="enhancement_ids" id="hiddenEnhancementIds" value="">
                        <input type="hidden" name="enhancement_dates" id="hiddenEnhancementDates" value="">
                        <div class="date-row">
                            <div class="input-group">
                                <label><i class="far fa-calendar-check"></i> Check-in</label>
                                <input type="text" id="checkin" name="checkin" placeholder="Select Date" required readonly>
                            </div>
                            <div class="input-group">
                                <label><i class="far fa-calendar-times"></i> Check-out</label>
                                <input type="text" id="checkout" name="checkout" placeholder="Select Date" required
                                    readonly>
                            </div>
                        </div>
                        <div class="input-group">
                            <label><i class="fas fa-user-friends"></i> Guests</label>
                            <select id="bookingGuests" name="guests" onchange="calculateBookingTotal()" required>
                                <option value="1">1 Guest</option>
                                <option value="2" selected>2 Guests</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                                <option value="5">5 Guests</option>
                                <option value="6">6 Guests</option>
                                <option value="7">7 Guests</option>
                                <option value="8">8 Guests</option>
                            </select>
                        </div>

                        {{-- Dining & Services Selector --}}
                        @if($room->enhancements && $room->enhancements->where('is_active', true)->count() > 0)
                            <div style="margin-top: 16px; margin-bottom: 8px;">
                                <button type="button" onclick="openEnhancementsModal()"
                                    style="width: 100%; display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.05); border: 1px solid var(--room-border); padding: 12px 16px; border-radius: 12px; color: var(--room-text); font-family: 'Outfit', sans-serif; cursor: pointer; transition: all 0.3s ease;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="fas fa-concierge-bell" style="color: #10b981;"></i>
                                        <span style="font-weight: 500; font-size: 14px;">Dining & Services</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span id="selectedEnhancementsCount"
                                            style="font-size: 12px; color: var(--room-muted);">Optional</span>
                                        <i class="fas fa-chevron-right" style="font-size: 12px; opacity: 0.5;"></i>
                                    </div>
                                </button>
                            </div>
                        @endif

                        {{-- Dynamic Breakdown Container --}}
                        <div id="bookingBreakdown"
                            style="display:none; border-top: 1px dashed rgba(255, 255, 255, 0.15); margin-top: 10px; padding-top: 10px;">
                        </div>

                        @if(Auth::check() && Auth::id() == $room->user_id)
                            <div style="margin-top: 16px; padding: 14px; background: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.2); text-align: center; border-radius: 12px; color: #4f46e5; font-weight: 700; font-family: 'Outfit', sans-serif;">
                                <i class="fas fa-home me-2"></i> You are the host of this property
                            </div>
                        @else
                            <button type="submit" class="btn-reserve" style="margin-top: 16px;">Reserve Now</button>
                            <p class="charge-note">
                                @if(Auth::check())
                                    You won't be charged yet
                                @else
                                    Please login to continue
                                @endif
                            </p>
                        @endif
                    </form>
                </div>
            </div>

        </div>

        <!-- Bottom: Cinematic Video Section -->
        @if($room->video_path || $room->video_link)
            <div class="room-section" style="margin-top: 60px;">
                <h2 class="section-title"><i class="fas fa-play-circle"></i> Cinematic Tour</h2>
                <div class="room-video-container">
                    <div class="video-overlay-title">Virtual Tour</div>
                    @if($room->video_type === 'upload' && $room->video_path)
                        <video controls crossorigin playsinline
                            poster="{{ $room->photos->first() ? asset('storage/' . $room->photos->first()->photo_path) : '' }}">
                            <source src="{{ asset('storage/' . $room->video_path) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @elseif($room->video_type === 'link' && $room->video_link)
                        @php
                            // Extremely basic YouTube embed converter
                            $videoUrl = $room->video_link;
                            if (str_contains($videoUrl, 'youtube.com/watch?v=')) {
                                $videoUrl = str_replace('watch?v=', 'embed/', $videoUrl);
                            } elseif (str_contains($videoUrl, 'youtu.be/')) {
                                $videoUrl = str_replace('youtu.be/', 'youtube.com/embed/', $videoUrl);
                            }
                        @endphp
                        <iframe src="{{ $videoUrl }}"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    @endif
                </div>
            </div>
        @endif

        {{-- Dining & Services Modal --}}
        @if($room->enhancements && $room->enhancements->where('is_active', true)->count() > 0)
            <div id="enhancementsModal"
                style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
                <div
                    style="background: var(--room-bg); color: var(--room-text); border: 1px solid var(--room-border); border-radius: 20px; width: 90%; max-width: 480px; max-height: 80vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); transform: translateY(20px); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                    <div
                        style="padding: 20px 24px; border-bottom: 1px solid var(--room-border); display: flex; justify-content: space-between; align-items: center;">
                        <h3
                            style="margin: 0; color: var(--room-text); font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 700;">
                            <i class="fas fa-concierge-bell" style="color: #10b981; margin-right: 8px;"></i> Dining & Services
                        </h3>
                        <button type="button" onclick="closeEnhancementsModal()"
                            style="background: none; border: none; color: var(--room-muted); cursor: pointer; font-size: 20px; padding: 4px;"><i
                                class="fas fa-times"></i></button>
                    </div>

                    <div style="padding: 0 24px; overflow-y: auto; flex-grow: 1;">
                        <p style="font-size: 13px; color: var(--room-muted); margin: 16px 0;">Enhance your stay! Select optional
                            dining and services. Prices shown are per guest.</p>

                        <div style="display: flex; flex-direction: column; gap: 24px; margin-bottom: 24px;">
                            @php
                                $priority = ['breakfast' => 1, 'lunch' => 2, 'dinner' => 3];
                                $groupedEnhancements = $room->enhancements
                                    ->where('is_active', true)
                                    ->groupBy('type')
                                    ->sortBy(function ($val, $key) use ($priority) {
                                        return $priority[strtolower($key)] ?? 99;
                                    });
                            @endphp

                            @foreach($groupedEnhancements as $type => $enhancements)
                                <div>
                                    <h4
                                        style="font-size: 13px; font-weight: 700; color: var(--room-text); text-transform: uppercase; margin-top: 0; margin-bottom: 10px; letter-spacing: 0.5px; opacity: 0.8;">
                                        {{ $type ?: 'Other Services' }}
                                    </h4>
                                    <div style="display: flex; flex-direction: column; gap: 10px;">
                                        @foreach($enhancements as $enhancement)
                                            <div style="display: flex; flex-direction: column; gap: 0; padding: 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--room-border); border-radius: 12px; transition: all 0.2s ease;"
                                                class="enhancement-label"
                                                onmouseover="this.style.borderColor='#10b981'; this.style.background='rgba(16,185,129,0.05)'"
                                                onmouseout="this.style.borderColor='var(--room-border)'; this.style.background='rgba(255,255,255,0.03)'">

                                                <label
                                                    style="display: flex; align-items: center; gap: 14px; cursor: pointer; width: 100%; margin: 0; user-select: none;">
                                                    <div style="margin-top: 2px;">
                                                        <input type="checkbox" class="enhancement-checkbox"
                                                            value="{{ $enhancement->id }}" data-name="{{ $enhancement->item_name }}"
                                                            data-type="{{ strtolower($enhancement->type) }}"
                                                            onchange="toggleEnhancementDates({{ $enhancement->id }}, this.checked); handleEnhancementSelection();"
                                                            style="width: 18px; height: 18px; accent-color: #10b981; cursor: pointer;">
                                                    </div>
                                                    <div style="flex-grow: 1;">
                                                        <div
                                                            style="font-family: 'Outfit', sans-serif; font-weight: 600; color: var(--room-text); font-size: 15px; display: flex; align-items: center; gap: 6px;">
                                                            @if($enhancement->is_per_guest)
                                                                <i class="fas fa-user-tag text-success" style="font-size: 12px;"></i>
                                                            @endif
                                                            <span>{{ $enhancement->item_name }}</span>
                                                        </div>
                                                    </div>
                                                    <div
                                                        style="font-family: 'Outfit', sans-serif; font-weight: 700; color: #10b981; font-size: 15px; display: flex; flex-direction: column; align-items: flex-end;">
                                                        <span>{{ $room->currency_symbol }}{{ number_format($enhancement->price, 2) }}</span>
                                                        @if($enhancement->is_per_guest)
                                                            <span
                                                                style="font-size: 10px; color: var(--room-muted); font-weight: 500; margin-top: 2px;">/
                                                                guest</span>
                                                        @endif
                                                    </div>
                                                </label>

                                                <div class="enhancement-dates-container" id="dates_container_{{ $enhancement->id }}"
                                                    style="display: none; padding-top: 10px; margin-top: 10px; border-top: 1px dashed var(--room-border); flex-direction: column; gap: 8px; width: 100%;">
                                                    <!-- Populated dynamically in Javascript -->
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div style="padding: 16px 24px; border-top: 1px solid var(--room-border); background: rgba(0,0,0,0.1);">
                        <button type="button" onclick="closeEnhancementsModal()"
                            style="width: 100%; background: #10b981; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; font-family: 'Outfit', sans-serif; cursor: pointer; transition: all 0.2s ease;"
                            onmouseover="this.style.background='#0ea5e9'"
                            onmouseout="this.style.background='#10b981'">Done</button>
                    </div>
                </div>
            </div>

            <script>
                function isMealAllowedOnCheckoutDate(type, policyStr) {
                    type = (type || "").toLowerCase().trim();
                    let hour = 11; // Default to 11 AM if not parsed
                    const match = policyStr.match(/(\d+):(\d+)\s*(AM|PM)/i);
                    if (match) {
                        let h = parseInt(match[1]);
                        const isPM = match[3].toUpperCase() === 'PM';
                        if (isPM && h < 12) h += 12;
                        if (!isPM && h === 12) h = 0;
                        hour = h;
                    }
                    if (type === 'breakfast') {
                        return hour >= 8; // breakfast is served mornings, minimum checkout is 8 AM so always allowed
                    } else if (type === 'lunch') {
                        return hour >= 13; // lunch is served afternoon, allowed if checkout is 1 PM or later
                    } else if (type === 'dinner') {
                        return hour >= 19; // dinner is served evening, allowed if checkout is 7 PM or later
                    }
                    return true;
                }

                function getDatesInRange(startDateStr, endDateStr) {
                    const dates = [];
                    let start = new Date(startDateStr);
                    const end = new Date(endDateStr);

                    let count = 0;
                    while (start <= end && count < 60) {
                        dates.push(new Date(start));
                        start.setDate(start.getDate() + 1);
                        count++;
                    }
                    return dates;
                }

                function formatDateDisplay(date) {
                    const options = { day: 'numeric', month: 'short' };
                    return date.toLocaleDateString('en-US', options);
                }

                function formatDateValue(date) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }

                function openEnhancementsModal() {
                    const checkinVal = document.getElementById("checkin").value;
                    const checkoutVal = document.getElementById("checkout").value;

                    if (!checkinVal || !checkoutVal) {
                        alert("Please select Check-in and Check-out dates first.");
                        return;
                    }

                    const dates = getDatesInRange(checkinVal, checkoutVal);
                    const checkoutPolicyStr = "{{ $room->checkout_policy ?: '11:00 AM' }}";

                    const checkboxes = document.querySelectorAll('.enhancement-checkbox');
                    checkboxes.forEach(cb => {
                        const id = cb.value;
                        const datesContainer = document.getElementById('dates_container_' + id);
                        if (datesContainer) {
                            const currentRange = checkinVal + '_' + checkoutVal;
                            if (datesContainer.getAttribute('data-range') !== currentRange) {
                                datesContainer.setAttribute('data-range', currentRange);

                                let datesHtml = `
                                                                                                    <div style="font-size: 10px; font-weight: 700; color: var(--room-muted); text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px;">Select Days:</div>
                                                                                                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 6px;">
                                                                                                `;

                                dates.forEach(date => {
                                    const dateVal = formatDateValue(date);
                                    const dateText = formatDateDisplay(date);

                                    // If it's the checkout date, check if the meal/enhancement type is allowed by checkout time
                                    if (dateVal === checkoutVal) {
                                        const type = cb.getAttribute('data-type') || "";
                                        if (!isMealAllowedOnCheckoutDate(type, checkoutPolicyStr)) {
                                            return; // Skip showing checkout date for this early checkout policy time
                                        }
                                    }

                                    datesHtml += `
                                                                                                        <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; color: var(--room-text); cursor: pointer; user-select: none; padding: 4px 6px; background: rgba(255,255,255,0.02); border: 1px solid var(--room-border); border-radius: 6px;">
                                                                                                            <input type="checkbox" class="enhancement-date-chk-${id}" value="${dateVal}" checked onchange="handleDateSelection(${id})" style="accent-color: #10b981; width: 12px; height: 12px; cursor: pointer;">
                                                                                                            <span>${dateText}</span>
                                                                                                        </label>
                                                                                                    `;
                                });

                                datesHtml += `</div>`;
                                datesContainer.innerHTML = datesHtml;
                            }

                            datesContainer.style.display = cb.checked ? 'flex' : 'none';
                        }
                    });

                    const modal = document.getElementById('enhancementsModal');
                    modal.style.display = 'flex';
                    // Trigger reflow for animation
                    void modal.offsetWidth;
                    modal.style.opacity = '1';
                    modal.querySelector('div').style.transform = 'translateY(0)';
                }

                function closeEnhancementsModal() {
                    const modal = document.getElementById('enhancementsModal');
                    modal.style.opacity = '0';
                    modal.querySelector('div').style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 300);
                }

                function toggleEnhancementDates(id, checked) {
                    const datesContainer = document.getElementById('dates_container_' + id);
                    if (datesContainer) {
                        datesContainer.style.display = checked ? 'flex' : 'none';
                        const dateChks = datesContainer.querySelectorAll('input[type="checkbox"]');
                        dateChks.forEach(chk => {
                            chk.checked = checked;
                        });
                    }
                }

                function handleDateSelection(id) {
                    const datesContainer = document.getElementById('dates_container_' + id);
                    const parentCheckbox = document.querySelector(`.enhancement-checkbox[value="${id}"]`);
                    if (datesContainer && parentCheckbox) {
                        const checkedCount = datesContainer.querySelectorAll('input[type="checkbox"]:checked').length;
                        parentCheckbox.checked = (checkedCount > 0);
                    }
                    handleEnhancementSelection();
                }

                function handleEnhancementSelection() {
                    const checkboxes = document.querySelectorAll('.enhancement-checkbox:checked');
                    const countSpan = document.getElementById('selectedEnhancementsCount');

                    const selectedIds = Array.from(checkboxes).map(cb => cb.value);
                    const hiddenInput = document.getElementById('hiddenEnhancementIds');
                    if (hiddenInput) {
                        hiddenInput.value = selectedIds.join(',');
                    }

                    // Build enhancement_dates object
                    const enhancementDates = {};
                    checkboxes.forEach(cb => {
                        const id = cb.value;
                        const dateChks = document.querySelectorAll(`.enhancement-date-chk-${id}:checked`);
                        enhancementDates[id] = Array.from(dateChks).map(chk => chk.value);
                    });

                    const hiddenDatesInput = document.getElementById('hiddenEnhancementDates');
                    if (hiddenDatesInput) {
                        hiddenDatesInput.value = JSON.stringify(enhancementDates);
                    }

                    if (checkboxes.length > 0) {
                        countSpan.textContent = checkboxes.length + ' selected';
                        countSpan.style.color = '#10b981';
                        countSpan.style.fontWeight = '600';
                    } else {
                        countSpan.textContent = 'Optional';
                        countSpan.style.color = 'var(--room-muted)';
                        countSpan.style.fontWeight = 'normal';
                    }

                    // Trigger the global pricing recalculation
                    if (typeof window.calculateBookingTotal === 'function') {
                        window.calculateBookingTotal();
                    }
                }
            </script>
        @endif

    </div>
@endsection

@section('scripts')
    <!-- Flatpickr for booking form -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/rangePlugin.js"></script>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                });
            });
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        let showAllRulesState = false;
        function toggleShowAllRules() {
            const items = document.querySelectorAll('.house-rule-item');
            const btn = document.getElementById('btn-toggle-show-rules');
            showAllRulesState = !showAllRulesState;

            items.forEach(item => {
                const index = parseInt(item.getAttribute('data-index'));
                if (index >= 4) {
                    item.style.display = showAllRulesState ? 'flex' : 'none';
                }
            });

            if (showAllRulesState) {
                btn.innerHTML = 'Show Less <i class="fas fa-chevron-up ms-1" style="font-size: 11px;"></i>';
            } else {
                btn.innerHTML = 'Show More <i class="fas fa-chevron-down ms-1" style="font-size: 11px;"></i>';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Swiper
            const swiper = new Swiper('.room-hero-slider', {
                loop: true,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });

            // Initialize Fancybox Lightbox
            Fancybox.bind("[data-fancybox]", {
                Images: {
                    zoom: true,
                },
                Toolbar: {
                    display: {
                        left: ["infobar"],
                        middle: [
                            "zoomIn",
                            "zoomOut",
                            "toggle1to1",
                            "rotateCCW",
                            "rotateCW",
                            "flipX",
                            "flipY",
                        ],
                        right: ["slideshow", "thumbs", "close"],
                    },
                },
            });

            // Initialize Leaflet Map
            (function () {
                let lat = {{ $room->roomLocation->latitude ?? 'null' }};
                let lng = {{ $room->roomLocation->longitude ?? 'null' }};
                const locName = "{{ $room->roomLocation->location_name ?? ($room->city . ', ' . $room->country) }}";

                // Najibabad fallback coords
                const isNajibabad = "{{ strtolower($room->roomLocation->city ?? $room->city) }}".includes('najibabad');
                if (!lat || !lng) {
                    if (isNajibabad) {
                        lat = 29.6200;
                        lng = 78.3300;
                    } else {
                        lat = 28.6139; // Delhi fallback
                        lng = 77.2090;
                    }
                }

                const map = L.map('room-map', {
                    center: [lat, lng],
                    zoom: 14,
                    zoomControl: false // Place zoom control nicely at bottom right
                });

                // Add modern dark/light tile layer based on active theme
                const isDarkMode = document.body.classList.contains('dark-mode');
                const tileUrl = isDarkMode
                    ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
                    : 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';

                L.tileLayer(tileUrl, {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                // Add custom zoom control on bottom right
                L.control.zoom({
                    position: 'bottomright'
                }).addTo(map);

                // Highlight circle with pulse animation classes
                const circle = L.circle([lat, lng], {
                    color: '#4f46e5',
                    fillColor: '#818cf8',
                    fillOpacity: 0.15,
                    weight: 2,
                    radius: {{ \App\Models\SiteSetting::get('map_radius', 400) }}
                                                }).addTo(map);

                // Add a beautiful custom center marker
                const centerMarker = L.divIcon({
                    className: 'custom-map-center-pin',
                    html: `<div style="width: 24px; height: 24px; border-radius: 50%; background: #4f46e5; border: 3px solid #ffffff; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4); display: flex; align-items: center; justify-content: center;"><i class="fas fa-hotel" style="color: #ffffff; font-size: 10px;"></i></div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });
                L.marker([lat, lng], { icon: centerMarker }).addTo(map);

                // Bind click to popup
                circle.bindPopup(`<strong style="font-family:'Outfit', sans-serif; font-size: 13px; color: #1e293b;">General Location</strong><br/><span style="font-family:'Outfit', sans-serif; font-size: 12px; color: #64748b;">For privacy, this circular region marks the neighborhood of ${locName}.</span>`).openPopup();

                // Dynamically calculate and display distance if browser location is accessible
                const infoBadgeEl = document.getElementById('map-info-badge');
                const infoTextEl = document.getElementById('map-info-text');
                const liveBtn = document.getElementById('map-info-live-btn');

                // Utility geodesic calculator to verify coordinates difference
                function calculateGeodesicDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371;
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a =
                        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    return R * c;
                }

                // Accurate coordinate resolver prioritizing live browser high-accuracy GPS
                function resolveAccurateCoordinates(successCallback, errorCallback, highAccuracy) {
                    // If the user has set a custom override location, ALWAYS prioritize it for maximum accuracy!
                    const customLat = localStorage.getItem('custom_user_lat');
                    const customLng = localStorage.getItem('custom_user_lng');
                    if (customLat && customLng) {
                        successCallback(parseFloat(customLat), parseFloat(customLng), localStorage.getItem('custom_user_name') || 'Custom Location');
                        return;
                    }

                    // Query the browser's high-precision GPS device location first
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            const userLat = position.coords.latitude;
                            const userLng = position.coords.longitude;

                            // Browser GPS is highly accurate (always trusted)
                            successCallback(userLat, userLng, 'GPS Location');
                        }, function (err) {
                            // Geolocation blocked, disabled or timed out, fallback to IP Geolocation
                            fetchIpLocation(successCallback, errorCallback);
                        }, {
                            enableHighAccuracy: true, // Always request hardware-level high precision GPS values
                            timeout: highAccuracy ? 8000 : 4000,
                            maximumAge: 0 // Force fresh positioning query bypasses outdated router cache
                        });
                    } else {
                        fetchIpLocation(successCallback, errorCallback);
                    }
                }

                function fetchIpLocation(successCallback, errorCallback) {
                    // Fetch highly precise Indian ISP nodes via ipinfo.io
                    fetch('https://ipinfo.io/json')
                        .then(response => response.json())
                        .then(ipData => {
                            if (ipData && ipData.loc) {
                                const coords = ipData.loc.split(',');
                                const ipLat = parseFloat(coords[0]);
                                const ipLng = parseFloat(coords[1]);
                                const ipCity = ipData.city || 'Detected Location';
                                successCallback(ipLat, ipLng, ipCity);
                            } else {
                                throw new Error('ipinfo failed');
                            }
                        })
                        .catch(err => {
                            // Secondary fallback to freeipapi
                            fetch('https://freeipapi.com/api/json')
                                .then(response => response.json())
                                .then(ipData => {
                                    const ipLat = parseFloat(ipData.latitude);
                                    const ipLng = parseFloat(ipData.longitude);
                                    const ipCity = ipData.cityName || 'Detected Location';
                                    successCallback(ipLat, ipLng, ipCity);
                                })
                                .catch(err2 => {
                                    errorCallback(err2);
                                });
                        });
                }

                // Function to update distance with specific GPS high-accuracy parameters
                function updateLocationDistance(highAccuracy) {
                    if (liveBtn) {
                        liveBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size: 9px;"></i>';
                        liveBtn.style.color = '#4f46e5';
                    }
                    if (infoTextEl) {
                        infoTextEl.innerHTML = `<i class="fas fa-spinner fa-spin" style="font-size: 11px; color: #4f46e5; margin-right: 4px;"></i> Pinpointing GPS...`;
                    }

                    resolveAccurateCoordinates(function (userLat, userLng, cityName) {
                        calculateRealDistance(userLat, userLng, lat, lng, function (distance) {
                            if (infoTextEl) {
                                let distText = distance < 1
                                    ? `${Math.round(distance * 1000)} m`
                                    : `${distance.toFixed(1)} km`;
                                infoTextEl.innerHTML = `<i class="fas fa-location-arrow" style="font-size: 11px; color: #4f46e5; margin-right: 4px;"></i> ${distText} away <span style="font-size: 11px; font-weight: normal; color: #64748b;">(from ${cityName})</span>`;
                            }

                            if (liveBtn) {
                                liveBtn.innerHTML = '<i class="fas fa-check" style="font-size: 9px; color: #10b981;"></i>';
                                setTimeout(() => {
                                    liveBtn.innerHTML = '<i class="fas fa-info" style="font-size: 9px; font-weight: 800;"></i>';
                                }, 1500);
                            }
                        });
                    }, function (err) {
                        console.warn('Geolocation coordinate resolution error:', err);

                        if (infoTextEl) {
                            if (err.code === 1) {
                                infoTextEl.innerHTML = `<i class="fas fa-ban" style="font-size: 11px; color: #64748b; margin-right: 4px;"></i> Permission blocked`;
                            } else {
                                infoTextEl.innerHTML = `<i class="fas fa-exclamation-triangle" style="font-size: 11px; color: #64748b; margin-right: 4px;"></i> GPS unavailable`;
                            }
                        }

                        if (liveBtn) {
                            liveBtn.innerHTML = '<i class="fas fa-exclamation-triangle" style="font-size: 9px; color: #ef4444;"></i>';
                            setTimeout(() => {
                                liveBtn.innerHTML = '<i class="fas fa-info" style="font-size: 9px; font-weight: 800;"></i>';
                            }, 1500);
                        }
                    }, highAccuracy);
                }

                // Initial fast approximation
                updateLocationDistance(false);

                // Elements for custom location overriding
                const editBtn = document.getElementById('map-info-edit-btn');
                const searchContainer = document.getElementById('map-info-search-container');
                const searchInput = document.getElementById('map-info-search-input');
                const searchSubmit = document.getElementById('map-info-search-submit');
                const searchCancel = document.getElementById('map-info-search-cancel');

                if (editBtn) {
                    editBtn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        // Hide text and helper buttons, show search bar inside badge
                        infoTextEl.style.display = 'none';
                        editBtn.style.display = 'none';
                        liveBtn.style.display = 'none';
                        searchContainer.style.display = 'flex';
                        searchInput.focus();
                    });
                }

                if (searchCancel) {
                    searchCancel.addEventListener('click', function (e) {
                        e.stopPropagation();
                        // Restore original display mode
                        infoTextEl.style.display = 'inline-block';
                        editBtn.style.display = 'flex';
                        liveBtn.style.display = 'flex';
                        searchContainer.style.display = 'none';
                    });
                }

                if (searchSubmit) {
                    searchSubmit.addEventListener('click', function (e) {
                        e.stopPropagation();
                        performCustomGeocoding();
                    });
                }

                if (searchInput) {
                    searchInput.addEventListener('keypress', function (e) {
                        if (e.key === 'Enter') {
                            e.stopPropagation();
                            performCustomGeocoding();
                        }
                    });
                }

                function performCustomGeocoding() {
                    const query = searchInput.value.trim();
                    if (!query) return;

                    searchSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    searchSubmit.disabled = true;

                    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=1`)
                        .then(res => res.json())
                        .then(data => {
                            searchSubmit.innerHTML = '<i class="fas fa-check"></i>';
                            searchSubmit.disabled = false;

                            if (data && data.length > 0) {
                                const customLat = parseFloat(data[0].lat);
                                const customLng = parseFloat(data[0].lon);

                                // Extract first word or clean name for city display
                                const parts = data[0].display_name.split(',');
                                const cityName = parts[0].trim();

                                // Save to localStorage for permanent accuracy & seamless page reloads!
                                localStorage.setItem('custom_user_lat', customLat);
                                localStorage.setItem('custom_user_lng', customLng);
                                localStorage.setItem('custom_user_name', cityName);

                                // Transition back
                                infoTextEl.style.display = 'inline-block';
                                editBtn.style.display = 'flex';
                                liveBtn.style.display = 'flex';
                                searchContainer.style.display = 'none';

                                // Trigger distance recalculation instantly
                                updateLocationDistance(true);
                            } else {
                                alert('City not found. Please try a different name (e.g. Madurai).');
                            }
                        })
                        .catch(err => {
                            searchSubmit.innerHTML = '<i class="fas fa-check"></i>';
                            searchSubmit.disabled = false;
                            console.error('Custom geocoding failed:', err);
                            alert('Service temporarily unavailable. Please try again.');
                        });
                }

                // Bind high-accuracy live triggers to hover/click of info button
                if (liveBtn) {
                    let hoverTimeout;
                    liveBtn.addEventListener('mouseenter', function () {
                        hoverTimeout = setTimeout(() => {
                            updateLocationDistance(true);
                        }, 350);
                    });

                    liveBtn.addEventListener('mouseleave', function () {
                        clearTimeout(hoverTimeout);
                    });

                    liveBtn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        // Clear any custom user overrides to reset to real-time auto detection!
                        localStorage.removeItem('custom_user_lat');
                        localStorage.removeItem('custom_user_lng');
                        localStorage.removeItem('custom_user_name');
                        updateLocationDistance(true);
                    });
                }

                // Helper to fetch actual road/driving distance via Google API or OSRM Route Service
                function calculateRealDistance(userLat, userLng, hotelLat, hotelLng, callback) {
                    // 1. Try Google Distance Matrix Service if google maps object is present
                    if (window.google && window.google.maps) {
                        try {
                            const service = new google.maps.DistanceMatrixService();
                            service.getDistanceMatrix({
                                origins: [new google.maps.LatLng(userLat, userLng)],
                                destinations: [new google.maps.LatLng(hotelLat, hotelLng)],
                                travelMode: google.maps.TravelMode.DRIVING,
                            }, function (response, status) {
                                if (status === 'OK' && response.rows && response.rows[0].elements && response.rows[0].elements[0].status === 'OK') {
                                    const distanceInMeters = response.rows[0].elements[0].distance.value;
                                    callback(distanceInMeters / 1000);
                                    return;
                                }
                                fallbackRoute(userLat, userLng, hotelLat, hotelLng, callback);
                            });
                            return;
                        } catch (e) {
                            console.error('Google DistanceMatrix failed, falling back to OSRM:', e);
                        }
                    }
                    fallbackRoute(userLat, userLng, hotelLat, hotelLng, callback);
                }

                function fallbackRoute(userLat, userLng, hotelLat, hotelLng, callback) {
                    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${userLng},${userLat};${hotelLng},${hotelLat}?overview=false`;
                    fetch(osrmUrl)
                        .then(response => response.json())
                        .then(data => {
                            if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                                const distanceInMeters = data.routes[0].distance;
                                callback(distanceInMeters / 1000);
                            } else {
                                throw new Error('OSRM failed');
                            }
                        })
                        .catch(err => {
                            // Geodesic distance with road multiplier fallback
                            const R = 6371;
                            const dLat = (hotelLat - userLat) * Math.PI / 180;
                            const dLon = (hotelLng - userLng) * Math.PI / 180;
                            const a =
                                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                                Math.cos(userLat * Math.PI / 180) * Math.cos(hotelLat * Math.PI / 180) *
                                Math.sin(dLon / 2) * Math.sin(dLon / 2);
                            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                            const geodesicDistance = R * c;
                            callback(geodesicDistance * 1.3);
                        });
                }

                // Direction button click handler
                const directionBtn = document.getElementById('map-direction-btn');
                if (directionBtn) {
                    directionBtn.addEventListener('click', function () {
                        // Open directly with Google's native high-accuracy "My Location" route
                        window.open(`https://www.google.com/maps/dir/?api=1&origin=My+Location&destination=${lat},${lng}&travelmode=driving`, '_blank');
                    });
                }
            })();

            // Global Booking Calculation Engine calling backend price_calculation helper
            window.calculateBookingTotal = function () {
                const checkinVal = window.checkinPicker && window.checkinPicker.selectedDates.length > 0
                    ? flatpickr.formatDate(window.checkinPicker.selectedDates[0], "Y-m-d")
                    : document.getElementById("checkin").value;
                const checkoutVal = window.checkoutPicker && window.checkoutPicker.selectedDates.length > 0
                    ? flatpickr.formatDate(window.checkoutPicker.selectedDates[0], "Y-m-d")
                    : document.getElementById("checkout").value;
                const guestsCount = parseInt(document.getElementById("bookingGuests").value) || 2;
                const breakdownEl = document.getElementById("bookingBreakdown");

                if (!checkinVal || !checkoutVal || !breakdownEl) {
                    breakdownEl.style.display = "none";
                    return;
                }

                // Get checked enhancements
                const checkedEnhancements = Array.from(document.querySelectorAll('.enhancement-checkbox:checked')).map(cb => parseInt(cb.value));

                // Get enhancement dates
                const enhancementDates = {};
                document.querySelectorAll('.enhancement-checkbox:checked').forEach(cb => {
                    const id = cb.value;
                    const dateChks = document.querySelectorAll(`.enhancement-date-chk-${id}:checked`);
                    enhancementDates[id] = Array.from(dateChks).map(chk => chk.value);
                });

                fetch("{{ route('rooms.calculate-price', $room->id) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        checkin: checkinVal,
                        checkout: checkoutVal,
                        guests: guestsCount,
                        enhancement_ids: checkedEnhancements,
                        enhancement_dates: enhancementDates
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            breakdownEl.style.display = "none";
                            return;
                        }

                        const symbol = data.currencySymbol;

                        // Update header price based on weekend nights selection
                        const headerPriceEl = document.getElementById("bookingHeaderPrice");
                        if (headerPriceEl) {
                            if (data.hasWeekendNightsBooked && data.weekendPrice > 0) {
                                headerPriceEl.innerHTML = `${symbol}${data.weekendPrice.toFixed(2)} <span>/ Night (weekend rate)</span>`;
                            } else {
                                headerPriceEl.innerHTML = `${symbol}${data.standardBasePrice.toFixed(2)} <span>/ Night</span>`;
                            }
                        }

                        const standardTotal = data.standardBasePrice * data.totalNights;
                        const weekendSurcharge = data.totalRawBasePrice - standardTotal;

                        let html = `
                                                                                <div class="pricing-breakdown-card" style="display:flex; flex-direction:column; gap:12px; background:rgba(0,0,0,0.02); border:1px solid var(--room-border); border-radius:14px; padding:16px; font-family:'Outfit',sans-serif; backdrop-filter:blur(10px);">

                                                                                    <!-- Standard Nightly Rate Row -->
                                                                                    <div style="display:flex; justify-content:space-between; align-items:center;">
                                                                                        <div style="display:flex; flex-direction:column;">
                                                                                            <span style="font-size:13px; font-weight:600; color:var(--room-text);">Nightly Rate</span>
                                                                                            <span style="font-size:11px; color:var(--room-muted);">${symbol}${data.standardBasePrice.toFixed(2)} × ${data.totalNights} night${data.totalNights > 1 ? 's' : ''}</span>
                                                                                        </div>
                                                                                        <span style="font-size:14px; font-weight:700; color:var(--room-text);">${symbol}${standardTotal.toFixed(2)}</span>
                                                                                    </div>
                                                                            `;

                        if (weekendSurcharge > 0) {
                            html += `
                                                                                    <!-- Weekend Surcharge Row -->
                                                                                    <div style="display:flex; justify-content:space-between; align-items:center; padding-top:4px;">
                                                                                        <div style="display:flex; flex-direction:column;">
                                                                                            <span style="font-size:13px; font-weight:600; color:var(--room-text);">Weekend Surcharge</span>
                                                                                            <span style="font-size:11px; color:var(--room-muted);">Additional rate for Fri/Sat nights</span>
                                                                                        </div>
                                                                                        <span style="font-size:14px; font-weight:700; color:var(--room-text);">${symbol}${weekendSurcharge.toFixed(2)}</span>
                                                                                    </div>
                                                                                `;
                        } else if (weekendSurcharge < 0) {
                            html += `
                                                                                    <!-- Weekend Discount Row -->
                                                                                    <div style="display:flex; justify-content:space-between; align-items:center; padding-top:4px;">
                                                                                        <div style="display:flex; flex-direction:column;">
                                                                                            <span style="font-size:13px; font-weight:600; color:#10b981;">Weekend Deal</span>
                                                                                            <span style="font-size:11px; color:#10b981; opacity:0.85;">Lower rate for Fri/Sat nights</span>
                                                                                        </div>
                                                                                        <span style="font-size:14px; font-weight:700; color:#10b981;">-${symbol}${Math.abs(weekendSurcharge).toFixed(2)}</span>
                                                                                    </div>
                                                                                `;
                        }

                        if (data.discountPct > 0) {
                            html += `
                                                                                    <!-- Applied Discount Row -->
                                                                                    <div style="display:flex; justify-content:space-between; align-items:center; padding-top:4px;">
                                                                                        <div style="display:flex; flex-direction:column;">
                                                                                            <span style="font-size:13px; font-weight:600; color:#10b981;">Discount Applied</span>
                                                                                            <span style="font-size:11px; color:#10b981; opacity:0.85;">${data.discountAppliedName} (-${data.discountPct}%)</span>
                                                                                        </div>
                                                                                        <span style="font-size:14px; font-weight:700; color:#10b981;">-${symbol}${data.discountSavings.toFixed(2)}</span>
                                                                                    </div>
                                                                                `;
                        }

                        if (data.cleaningFee > 0) {
                            html += `
                                                                                    <!-- Cleaning Fee Row -->
                                                                                    <div style="display:flex; justify-content:space-between; align-items:center; padding-top:4px;">
                                                                                        <div style="display:flex; flex-direction:column;">
                                                                                            <span style="font-size:13px; font-weight:600; color:var(--room-text);">Cleaning Fee</span>
                                                                                            <span style="font-size:11px; color:var(--room-muted);">Flat rate sanitization</span>
                                                                                        </div>
                                                                                        <span style="font-size:14px; font-weight:700; color:var(--room-text);">${symbol}${data.cleaningFee.toFixed(2)}</span>
                                                                                    </div>
                                                                                `;
                        }

                        if (data.extraGuestsFee > 0) {
                            const threshold = {{ $room->roomPrice->additional_pricing['additional_guests']['after_guests'] ?? 2 }};
                            html += `
                                                                                    <!-- Extra Guests Row -->
                                                                                    <div style="display:flex; justify-content:space-between; align-items:center; padding-top:4px;">
                                                                                        <div style="display:flex; flex-direction:column;">
                                                                                            <span style="font-size:13px; font-weight:600; color:var(--room-text);">Extra Guests</span>
                                                                                            <span style="font-size:11px; color:var(--room-muted);">Surcharge (after ${threshold} guests)</span>
                                                                                        </div>
                                                                                        <span style="font-size:14px; font-weight:700; color:var(--room-text);">${symbol}${data.extraGuestsFee.toFixed(2)}</span>
                                                                                    </div>
                                                                                `;
                        }

                        if (data.selectedEnhancements && data.selectedEnhancements.length > 0) {
                            let tooltipHtml = data.selectedEnhancements.map(e => `
                                                                            <div style="display:flex; justify-content:space-between; gap:16px; margin-bottom:6px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:6px;">
                                                                                <span style="opacity:0.9;">${e.item_name} (&times;${guestsCount} guests &times;${e.days_count} day${e.days_count > 1 ? 's' : ''})</span>
                                                                                <span style="font-weight:700; color:#10b981;">${symbol}${e.item_total.toFixed(2)}</span>
                                                                            </div>
                                                                        `).join('');
                            // remove the last border bottom
                            tooltipHtml = tooltipHtml.replace(/border-bottom:1px solid rgba\(255,255,255,0\.1\); padding-bottom:6px;"(?=[^>]*>[\s\n]*$)/, '"');

                            html += `
                                                                                <!-- Consolidated Enhancements Row -->
                                                                                <div style="display:flex; justify-content:space-between; align-items:center; padding-top:4px;">
                                                                                    <div style="display:flex; flex-direction:column;">
                                                                                        <div style="display:flex; align-items:center; gap:6px;">
                                                                                            <span style="font-size:13px; font-weight:600; color:var(--room-text);">Dining & Services</span>
                                                                                            <div class="tooltip-container" style="cursor:help;">
                                                                                                <i class="fas fa-info-circle" style="font-size:12px; color:var(--room-muted);"></i>
                                                                                                <div class="custom-tooltip">
                                                                                                    <div style="font-weight:700; margin-bottom:8px; font-size:11px; text-transform:uppercase; color:var(--room-muted);">Included Items</div>
                                                                                                    ${tooltipHtml}
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <span style="font-size:11px; color:var(--room-muted);">${data.selectedEnhancements.length} item${data.selectedEnhancements.length > 1 ? 's' : ''} selected</span>
                                                                                    </div>
                                                                                    <span style="font-size:14px; font-weight:700; color:var(--room-text);">${symbol}${data.totalEnhancementFee.toFixed(2)}</span>
                                                                                </div>
                                                                            `;
                        }

                        if (data.serviceFeeAmt > 0) {
                            html += `
                                                                                    <!-- Service Fee Row -->
                                                                                    <div style="display:flex; justify-content:space-between; align-items:center; padding-top:4px;">
                                                                                        <div style="display:flex; flex-direction:column;">
                                                                                            <span style="font-size:13px; font-weight:600; color:var(--room-text);">Service Fee</span>
                                                                                            <span style="font-size:11px; color:var(--room-muted);">Platform maintenance (${data.serviceFeePct}%)</span>
                                                                                        </div>
                                                                                        <span style="font-size:14px; font-weight:700; color:var(--room-text);">${symbol}${data.serviceFeeAmt.toFixed(2)}</span>
                                                                                    </div>
                                                                                `;
                        }

                        if (data.taxAmt > 0) {
                            const taxLabel = data.taxType === 'percentage' ? `Taxes (${data.taxRate}%)` : 'Taxes';
                            html += `
                                                                                    <!-- Taxes Row -->
                                                                                    <div style="display:flex; justify-content:space-between; align-items:center; padding-top:4px;">
                                                                                        <div style="display:flex; flex-direction:column;">
                                                                                            <span style="font-size:13px; font-weight:600; color:var(--room-text);">Taxes</span>
                                                                                            <span style="font-size:11px; color:var(--room-muted);">${taxLabel}</span>
                                                                                        </div>
                                                                                        <span style="font-size:14px; font-weight:700; color:var(--room-text);">${symbol}${data.taxAmt.toFixed(2)}</span>
                                                                                    </div>
                                                                                `;
                        }

                        html += `
                                                                                    <!-- Total Separator -->
                                                                                    <div style="height:1px; background:var(--room-border); margin:8px 0;"></div>

                                                                                    <!-- Final Grand Total -->
                                                                                    <div style="display:flex; justify-content:space-between; align-items:center; padding-top:4px;">
                                                                                        <span style="font-size:15px; font-weight:800; color:var(--room-text);">Total Price</span>
                                                                                        <span style="font-size:18px; font-weight:800; color:#10b981; text-shadow: 0 0 10px rgba(16,185,129,0.1);">${symbol}${data.finalTotal.toFixed(2)}</span>
                                                                                    </div>
                                                                                </div>
                                                                            `;

                        breakdownEl.innerHTML = html;
                        breakdownEl.style.display = "block";
                    })
                    .catch(error => {
                        console.error("Pricing calculation error:", error);
                        breakdownEl.style.display = "none";
                    });
            };

            // Shared logic for creating days to manage persistent range and hover effects
            const handleDayCreate = function (dObj, dStr, fp, dayElem) {
                // Highlight Sundays
                if (dayElem.dateObj.getDay() === 0) {
                    dayElem.classList.add("is-sunday");
                }

                const checkinVal = document.getElementById("checkin").value;
                const checkoutVal = document.getElementById("checkout").value;

                // Highlight exact Check-in date
                if (checkinVal) {
                    const currentDayStr = flatpickr.formatDate(dayElem.dateObj, "Y-m-d");
                    if (currentDayStr === checkinVal) {
                        dayElem.classList.add("is-checkin-date");
                    }
                }

                // Highlight exact Check-out date
                if (checkoutVal) {
                    const currentDayStr = flatpickr.formatDate(dayElem.dateObj, "Y-m-d");
                    if (currentDayStr === checkoutVal) {
                        dayElem.classList.add("is-checkout-date");
                    }
                }

                // Persistent Range Selection (if both are picked, make background permanently gray)
                if (checkinVal && checkoutVal) {
                    const ciDate = fp.parseDate(checkinVal, "Y-m-d");
                    const coDate = fp.parseDate(checkoutVal, "Y-m-d");
                    if (dayElem.dateObj > ciDate && dayElem.dateObj < coDate) {
                        dayElem.classList.add("is-in-range");
                    }
                }

                // Dynamic Hover Trail (relevant if checkin is picked)
                dayElem.addEventListener('mouseenter', function () {
                    if (!checkinVal) return;

                    const ciDate = fp.parseDate(checkinVal, "Y-m-d");
                    const hoverDate = dayElem.dateObj;

                    // Clear existing hover classes
                    fp.days.childNodes.forEach(day => {
                        if (day.classList) day.classList.remove('is-hover-range');
                    });

                    // Apply hover trail up to the hovered date (if it's > checkin)
                    if (hoverDate > ciDate) {
                        fp.days.childNodes.forEach(day => {
                            if (day.dateObj && day.dateObj > ciDate && day.dateObj <= hoverDate) {
                                // Don't override if it's already permanently in-range and we hovered past it
                                if (!day.classList.contains('is-in-range') && !day.classList.contains('is-checkout-date')) {
                                    day.classList.add('is-hover-range');
                                }
                            }
                        });
                    }
                });

                // Clear dynamic hover classes on mouse leave
                dayElem.addEventListener('mouseleave', function () {
                    fp.days.childNodes.forEach(day => {
                        if (day.classList) day.classList.remove('is-hover-range');
                    });
                });
            };

            const blockedDates = @json($room->calendars()->where('is_blocked', true)->pluck('date')->toArray());

            // Initialize Flatpickr for Check-out
            const checkoutPicker = flatpickr("#checkout", {
                minDate: "today",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d-m-Y",
                disableMobile: "true",
                disable: blockedDates,
                onChange: function (selectedDates, dateStr, instance) {
                    calculateBookingTotal();
                    instance.redraw(); // Ensure checkout picker updates persistent range styles
                },
                onOpen: function (selectedDates, dateStr, instance) {
                    instance.redraw(); // Force evaluation of the gray range trail every time the calendar opens
                },
                onDayCreate: handleDayCreate
            });
            window.checkoutPicker = checkoutPicker;

            // Initialize Flatpickr for Check-in
            const checkinPicker = flatpickr("#checkin", {
                minDate: "today",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d-m-Y",
                disableMobile: "true",
                disable: blockedDates,
                onChange: function (selectedDates, dateStr, instance) {
                    // Ensure checkout is strictly after checkin
                    checkoutPicker.set('minDate', dateStr);
                    // Disable the exact check-in date itself plus all blocked dates
                    checkoutPicker.set('disable', [dateStr, ...blockedDates]);
                    checkoutPicker.redraw();

                    calculateBookingTotal();

                    // Automatically open checkout picker with animation
                    setTimeout(() => {
                        checkoutPicker.open();
                    }, 50);
                },
                onDayCreate: function (dObj, dStr, fp, dayElem) {
                    // Highlight Sundays
                    if (dayElem.dateObj.getDay() === 0) {
                        dayElem.classList.add("is-sunday");
                    }

                    const checkinVal = window.checkinPicker && window.checkinPicker.selectedDates.length > 0
                        ? flatpickr.formatDate(window.checkinPicker.selectedDates[0], "Y-m-d")
                        : document.getElementById("checkin").value;

                    // Highlight exact Check-in date ONLY
                    if (checkinVal) {
                        const currentDayStr = flatpickr.formatDate(dayElem.dateObj, "Y-m-d");
                        if (currentDayStr === checkinVal) {
                            dayElem.classList.add("is-checkin-date");
                        }
                    }
                }
            });
            window.checkinPicker = checkinPicker;

            // Close calendars when scrolling the page
            window.addEventListener('scroll', function () {
                if (checkinPicker.isOpen) checkinPicker.close();
                if (checkoutPicker.isOpen) checkoutPicker.close();
            }, { passive: true });
        });
    </script>
@endsection