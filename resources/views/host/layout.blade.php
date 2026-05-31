<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Your Property | {{ App\Models\SiteSetting::get('site_name', 'Laravel') }}</title>
    @php
        $favicon = \App\Models\SiteSetting::get('site_favicon');
    @endphp
    @if($favicon)
        <link rel="shortcut icon" href="{{ asset('storage/' . $favicon) }}" type="image/x-icon">
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <!-- Tippy.js Core -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/shift-away.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    @php $mapKey = \App\Models\SiteSetting::get('map_key'); @endphp
    @if($mapKey)
        <script src="https://maps.googleapis.com/maps/api/js?key={{ $mapKey }}&libraries=places"></script>
    @endif

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }

        :root {
            --bg-primary: #F9FAFB;
            --card-bg: #FFFFFF;
            --text-main: #111827;
            --text-muted: #6B7280;
            --border: #E5E7EB;
            --primary: #2563EB;
            --primary-dark: #1D4ED8;
            --success: #10B981;
            --error: #EF4444;
        }

        body.dark-mode {
            --bg-primary: #111827;
            --card-bg: #1F2937;
            --text-main: #F3F4F6;
            --text-muted: #9CA3AF;
            --border: #374151;
            --primary: #3B82F6;
            --primary-dark: #2563EB;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            transition: background-color 0.3s, color 0.3s;
        }

        .host-container {
            max-width: 1250px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Stepper Styles */
        /* Fixed & Improved Stepper Styles */
        .steps-wrapper {
            width: 100%;
            margin-bottom: 40px;
            overflow-x: auto;
            padding: 15px 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .steps-wrapper::-webkit-scrollbar { display: none; }

        .steps-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-width: 950px;
        }

        .step-card {
            flex: 1;
            min-width: 0;
            min-height: 120px;
            position: relative;
            background: transparent;
            border-radius: 20px;
            padding: 15px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
            z-index: 1;
            overflow: hidden; /* Clips the rotating gradient */
        }

        .step-card::after {
            content: '';
            position: absolute;
            inset: 2px; /* Creates the 2px border space */
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border-radius: 18px;
            z-index: -1;
            transition: background 0.3s;
        }

        .step-card:hover:not(.disabled) {
            transform: translateY(-5px);
            border-color: rgba(59, 130, 246, 0.4);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
        }

        /* Active State - Spinning Border */
        .step-card.active {
            transform: scale(1.05);
            z-index: 10;
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.3);
            border-color: transparent;
        }

        .step-card.active::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 250%; /* Large enough to cover corners */
            height: 250%;
            background: conic-gradient(from 0deg, transparent 60%, #3b82f6, #60a5fa, transparent 100%);
            transform: translate(-50%, -50%) rotate(0deg);
            animation: rotate-border 3s linear infinite;
            z-index: -2;
            pointer-events: none;
        }

        @keyframes rotate-border {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to   { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Success & Error States - Solid Border Glow */
        .step-card.success {
            border-color: #10b981;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
        }
        .step-card.success .step-icon-wrapper { color: #10b981; }

        .step-card.error {
            border-color: #ef4444;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.2);
        }
        .step-card.error .step-icon-wrapper { color: #ef4444; }

        /* Inactive State - Normally Visible */
        .step-card.inactive {
            opacity: 1;
        }
        .step-card.disabled {
            opacity: 0.8;
            cursor: not-allowed;
            border-color: rgba(255, 255, 255, 0.05);
        }

        /* Content Styles */
        .step-icon-wrapper {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            color: #94a3b8;
            transition: all 0.3s;
        }

        .step-card.active .step-icon-wrapper {
            color: #3b82f6;
            animation: pulse-icon 2s infinite cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes pulse-icon {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(59, 130, 246, 0)); }
            50% { transform: scale(1.15); filter: drop-shadow(0 0 10px rgba(59, 130, 246, 0.5)); }
        }

        .step-number-badge {
            position: absolute;
            top: 14px;
            right: 14px;
            font-size: 11px;
            font-weight: 900;
            color: #fff;
            background: rgba(59, 130, 246, 0.5);
            backdrop-filter: blur(6px);
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            z-index: 5;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .step-card.success .step-number-badge { background: rgba(16, 185, 129, 0.6); }
        .step-card.error .step-number-badge { background: rgba(239, 68, 68, 0.6); }

        .step-title {
            font-size: 12px;
            font-weight: 800;
            color: #f1f5f9;
            margin: 0 0 6px 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .step-subtitle {
            font-size: 10.5px;
            color: #94a3b8;
            margin: 0;
            line-height: 1.4;
            max-width: 140px;
        }

        /* Checkmark Animation */
        .checkmark-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            animation: checkmark-pop 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        /* Arrow Between Steps */
        .step-arrow-divider {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            color: rgba(148, 163, 184, 0.15);
            transition: all 0.5s;
        }

        .step-arrow-divider svg {
            width: 24px;
            height: 24px;
        }

        .step-arrow-divider.completed {
            color: #3b82f6;
            animation: arrow-glow 2s infinite ease-in-out;
        }

        @keyframes arrow-glow {
            0%, 100% { opacity: 0.4; transform: translateX(0); }
            50% { opacity: 1; transform: translateX(4px); filter: drop-shadow(0 0 5px #3b82f6); }
        }

        /* Split Card Layout */
        .host-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 32px;
            display: flex;
            min-height: 650px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            padding: 0 !important; /* Remove original padding for split view */
            transition: background-color 0.3s, border-color 0.3s;
        }

        .form-section {
            flex: 0 0 60%;
            padding: 60px;
            display: flex;
            flex-direction: column;
            background: var(--card-bg);
            overflow-y: auto;
        }

        .image-section {
            /* flex: 0 0 40%; */
            position: relative;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.5));
        }

        /* Glowing Animated Text */
        @keyframes colorGlow {
            0% { color: #fff; text-shadow: 0 0 10px #ff385c, 0 0 20px #ff385c; }
            33% { color: #ffd700; text-shadow: 0 0 15px #ffd700, 0 0 30px #ff8c00; }
            66% { color: #00ffcc; text-shadow: 0 0 15px #00ffcc, 0 0 30px #0099ff; }
            100% { color: #fff; text-shadow: 0 0 10px #ff385c, 0 0 20px #ff385c; }
        }

        .glowing-description {
            position: relative;
            z-index: 5;
            font-size: 22px;
            font-weight: 900;
            text-align: center;
            line-height: 1.2;
            /* animation: colorGlow 6s infinite ease-in-out, float 4s infinite ease-in-out; */
            max-width: 90%;
            color: white;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Premium Floating Labels */
        .form-floating-airbnb {
            position: relative;
            margin-bottom: 25px;
        }

        .form-control-airbnb {
            width: 100%;
            height: 64px;
            padding: 28px 16px 10px;
            border: 1.5px solid var(--border);
            border-radius: 14px;
            font-size: 16px;
            background: var(--card-bg);
            color: var(--text-main);
            transition: all 0.2s;
        }

        .form-control-airbnb:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255, 56, 92, 0.1);
        }

        .form-floating-airbnb label {
            position: absolute;
            left: 16px;
            top: 20px;
            font-size: 15px;
            color: var(--text-muted);
            pointer-events: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: 0 0;
        }

        .form-control-airbnb:focus ~ label,
        .form-control-airbnb:not(:placeholder-shown) ~ label {
            transform: scale(0.8) translateY(-14px);
            color: var(--primary);
            font-weight: 600;
        }

        .form-floating-airbnb.has-error .form-control-airbnb {
            border-color: var(--error);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
            animation: shake-airbnb 0.5s ease-in-out;
        }

        @keyframes shake-airbnb {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        textarea.form-control-airbnb {
            height: auto;
            padding-top: 32px;
        }

        .host-title {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .host-subtitle {
            font-size: 15px;
            color: var(--text-muted);
            /* margin-bottom: 30px; */
            margin-top: -4px;
        }

        /* Floating Label Form Group */
        .form-floating {
            position: relative;
            margin-bottom: 24px;
        }

        .form-floating .form-control {
            width: 100%;
            height: 60px;
            padding: 24px 16px 8px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-size: 16px;
            background: transparent;
            color: var(--text-main);
            transition: border-color 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit;
        }

        /* Textarea needs different padding for floating label */
        .form-floating textarea.form-control {
            padding-top: 32px;
            height: auto;
        }

        /* Standard Select styles (floating doesn't work perfectly on native select placeholders, but we mimic it) */
        .form-floating select.form-control {
            padding-top: 24px;
            padding-bottom: 8px;
        }

        .form-floating .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        .form-floating label {
            position: absolute;
            left: 16px;
            top: 20px;
            font-size: 15px;
            color: var(--text-muted);
            pointer-events: none;
            transform-origin: 0 0;
            transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1), transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Float the label when focused or has value */
        .form-floating .form-control:focus ~ label,
        .form-floating .form-control:not(:placeholder-shown) ~ label {
            transform: scale(0.8) translateY(-14px);
            color: var(--primary);
        }
        
        .form-floating select.form-control ~ label {
            transform: scale(0.8) translateY(-14px);
        }

        /* Error state & Animation */
        .form-floating.has-error .form-control {
            border-color: var(--error);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2);
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }
        
        .form-floating.has-error label {
            color: var(--error);
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }

        /* Old Form Group (for non-floating components) */
        .form-group { margin-bottom: 24px; }
        .form-label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-main); }
        .form-control-standard {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-size: 15px;
            background: var(--card-bg);
            color: var(--text-main);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .form-control-standard:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        /* Toaster */
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 9999;
            background: var(--card-bg);
            color: var(--text-main);
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border: 1px solid var(--border);
        }
        
        .toast-container.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast-icon { width: 20px; height: 20px; }
        .toast-container.saving .toast-icon { color: var(--primary); animation: spin 1s linear infinite; }
        .toast-container.success .toast-icon { color: var(--success); }
        .toast-container.error .toast-icon { color: var(--error); }

        @keyframes spin { 100% { transform: rotate(360deg); } }

        /* Buttons & Actions */
        .host-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 40px;
        }

        .btn-next, .btn-prev {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .btn-next {
            background: var(--primary);
            color: #fff;
            border: none;
        }
        .btn-next:hover:not(:disabled) { background: var(--primary-dark); transform: translateY(-2px); }
        .btn-next:disabled { opacity: 0.7; cursor: not-allowed; }

        .btn-prev {
            background: transparent;
            color: var(--text-main);
            border: 1px solid var(--border);
            margin-right: 12px;
        }
        .btn-prev:hover { background: var(--border); }

        .btn-spinner {
            display: none;
            width: 20px; height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .btn-next.loading .btn-text { opacity: 0; }
        .btn-next.loading .btn-spinner { display: block; position: absolute; }

        @media (max-width: 992px) {
            .steps-container {
                min-width: 100%;
            }
            /* Only show the active step on mobile/tablet */
            .step-card:not(.active) {
                display: none !important;
            }
            .step-arrow-divider {
                display: none !important;
            }
            .step-card.active {
                width: 100%;
                flex-direction: row;
                text-align: left;
                padding: 20px;
                gap: 20px;
                transform: none !important; /* Disable scale effect on mobile */
            }
            
            .host-card { 
                flex-direction: column !important; 
                min-height: auto;
            }
            
            .image-section { 
                display: none !important; 
            }
            
            .form-section {
                flex: 1 1 100%;
                padding: 40px 24px;
            }
            .step-icon-wrapper { margin-bottom: 0; }
            .step-subtitle { max-width: 100%; }
        }

        @media (max-width: 768px) {
            .host-container { padding: 0 12px; margin: 20px auto; }
            .form-section { padding: 30px 16px; }
            .host-actions {
                flex-direction: column-reverse;
                gap: 12px;
            }
            .btn-prev { margin-right: 0; width: 100%; }
            .btn-next { width: 100%; }
        }
    </style>
    @yield('styles')
</head>
<body class="{{ Cookie::get('theme') === 'dark' ? 'dark-mode' : 'light-mode' }}">

    @include('partials.header')

    <div class="host-container">
        <!-- Stepper -->
        <!-- Modern Premium Stepper -->
        <div class="steps-wrapper">
            <div class="steps-container">
                @php
                    $steps = [
                        1 => [
                            'label' => 'Basics', 
                            'subtitle' => 'Set your property details.', 
                            'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'
                        ],
                        2 => [
                            'label' => 'Media Hub', 
                            'subtitle' => 'Upload high-quality assets.', 
                            'icon' => '<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>'
                        ],
                        3 => [
                            'label' => 'Location', 
                            'subtitle' => 'Pinpoint precise coordinates.', 
                            'icon' => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>'
                        ],
                        4 => [
                            'label' => 'Services', 
                            'subtitle' => 'Select premium services.', 
                            'icon' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>'
                        ],
                        5 => [
                            'label' => 'Pricing', 
                            'subtitle' => 'Define rates & options.', 
                            'icon' => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>'
                        ],
                        6 => [
                            'label' => 'Rules & Calendar', 
                            'subtitle' => 'Set rules & block dates.', 
                            'icon' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>'
                        ]
                    ];
                @endphp
                @foreach($steps as $num => $data)
                    @php
                        $isCompleted = $num < $step;
                        $isActive = $num == $step;
                        // A step is disabled only if the previous step is invalid (and it's not the first step)
                        // This allows jumping back and forth between already filled steps.
                        $isDisabled = ($num > 1) && !$room->isStepValid($num - 1);
                        
                        // Validation logic for status glow
                        $isValid = $room->isStepValid($num);
                        $statusClass = '';
                        if ($isValid) {
                            $statusClass = 'success';
                        } elseif ($isCompleted) {
                            $statusClass = 'error';
                        }
                    @endphp
                    
                    <a href="{{ $isDisabled ? '#' : route('host.step', ['room' => $room->id, 'step' => $num]) }}" 
                       id="step-card-{{ $num }}"
                       class="step-card {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }} {{ $isDisabled ? 'disabled' : 'inactive' }} {{ $statusClass }}"
                       @if($isDisabled) data-tippy-content="Please complete previous steps" @endif
                       onclick="{{ $isDisabled ? 'event.preventDefault()' : '' }}">
                        
                        <div class="step-number-badge">{{ $num }}</div>
                        
                        <div class="step-icon-wrapper" id="step-icon-{{ $num }}">
                            @if($isValid)
                                <div class="checkmark-wrapper">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                            @else
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    {!! $data['icon'] !!}
                                </svg>
                            @endif
                        </div>
                        
                        <div class="step-desc-wrapper">
                            <h3 class="step-title">{{ $data['label'] }}</h3>
                            <p class="step-subtitle">{{ $data['subtitle'] }}</p>
                        </div>
                    </a>

                    @if(!$loop->last)
                        <div class="step-arrow-divider {{ $isCompleted ? 'completed' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        @php 
            // Alternating logic: Odd steps (1,3,5) Form left, Image right. Even steps (2,4) Image left, Form right.
            $isEven = ($step % 2 == 0); 
            $imagePath = $stepSetting && $stepSetting->image ? asset('storage/' . $stepSetting->image) : 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80';
        @endphp

        @if($step == 6)
            <div class="host-card" style="display: flex; gap: 30px; background: transparent; border: none; box-shadow: none; padding: 0; align-items: flex-start;">
                <!-- Form Section (70%) -->
                <div class="form-section" style="flex: 0 0 68%; max-width: 70%; background: var(--card-bg); border: 1.5px solid var(--border); border-radius: 28px; padding: 40px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); min-height: 600px;">
                    @yield('host-content')
                </div>
                <!-- Premium Summary Card Section (30%) -->
                <div class="summary-section" style="flex: 0 0 30%; max-width: 30%;">
                    @include('host.step6_summary')
                </div>
            </div>
        @else
            <div class="host-card" style="flex-direction: {{ $isEven ? 'row-reverse' : 'row' }};">
                <!-- Form Section (60%) -->
                <div class="form-section">
                    @yield('host-content')
                </div>

                <!-- Image Section (40%) -->
                <div class="image-section" style="background-image: url('{{ $imagePath }}');">
                    <div class="image-overlay"></div>
                    <div class="glowing-description">
                        {{ $stepSetting->description ?? 'Almost there! Just a few more details.' }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    @include('partials.footer')

    <!-- Toaster -->
    <div class="toast-container" id="toastStatus">
        <svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"></svg>
        <span class="toast-text">Saving...</span>
    </div>

    <script>
        // Initialize Tippy.js Tooltips
        tippy('[data-tippy-content]', {
            animation: 'shift-away',
            theme: 'light',
            arrow: true
        });

        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const toastStatus = document.getElementById('toastStatus');
        const toastIcon = toastStatus.querySelector('.toast-icon');
        const toastText = toastStatus.querySelector('.toast-text');
        
        let saveTimeout;
        let debounceTimer;

        function showToast(state, message) {
            toastStatus.className = `toast-container show ${state}`;
            toastText.textContent = message;
            
            if (state === 'saving') {
                toastIcon.innerHTML = '<path d="M21 12a9 9 0 11-6.219-8.56"/>';
                clearTimeout(saveTimeout);
            } else if (state === 'success') {
                toastIcon.innerHTML = '<polyline points="20 6 9 17 4 12"/>';
                saveTimeout = setTimeout(() => {
                    toastStatus.classList.remove('show');
                }, 2000);
            } else if (state === 'error') {
                toastIcon.innerHTML = '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>';
                saveTimeout = setTimeout(() => {
                    toastStatus.classList.remove('show');
                }, 3000);
            }
        }

        const stepIcons = {
            @foreach($steps as $num => $data)
                {{ $num }}: `{!! $data['icon'] !!}`,
            @endforeach
        };

        function updateStepperStatus(num, isValid) {
            const card = document.getElementById(`step-card-${num}`);
            const iconWrapper = document.getElementById(`step-icon-${num}`);
            if (!card || !iconWrapper) return;

            if (isValid) {
                card.classList.add('success');
                card.classList.remove('error');
                iconWrapper.innerHTML = `
                    <div class="checkmark-wrapper">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                `;
                
                // Real-time: Enable the NEXT step card if it exists
                const nextNum = parseInt(num) + 1;
                const nextCard = document.getElementById(`step-card-${nextNum}`);
                if (nextCard && nextCard.classList.contains('disabled')) {
                    nextCard.classList.remove('disabled');
                    nextCard.classList.add('inactive');
                    nextCard.removeAttribute('data-tippy-content');
                    // Re-enable click by updating href (we'll need to know the base URL)
                    const baseUrl = "{{ route('host.step', ['room' => $room->id, 'step' => ':step']) }}";
                    nextCard.setAttribute('href', baseUrl.replace(':step', nextNum));
                    nextCard.onclick = null; // Remove the preventDefault
                    
                    // Re-init tippy to remove the "Please complete previous steps" tooltip
                    if (nextCard._tippy) nextCard._tippy.destroy();
                }
            } else {
                card.classList.remove('success');
                iconWrapper.innerHTML = `
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        ${stepIcons[num]}
                    </svg>
                `;
                
                // Optional: If current step becomes invalid, should we block all subsequent steps?
                // The user said: "block the next step until the current steps is filled"
                // So yes, if step N becomes invalid, step N+1 (and others) should be blocked.
                let nextNum = parseInt(num) + 1;
                while(nextNum <= 6) {
                    const nextCard = document.getElementById(`step-card-${nextNum}`);
                    if (nextCard) {
                        nextCard.classList.add('disabled');
                        nextCard.classList.remove('inactive', 'success', 'error', 'active');
                        nextCard.setAttribute('href', '#');
                        nextCard.onclick = (e) => e.preventDefault();
                    }
                    nextNum++;
                }
            }
        }

        // Debounced Save Function
        function autoSave(field, value, table = 'rooms', elementId = null) {
            clearTimeout(debounceTimer);
            showToast('saving', 'Saving...');
            
            // Remove error state if typing
            if(elementId) {
                const el = document.getElementById(elementId);
                if(el && el.closest('.form-floating-airbnb')) {
                    el.closest('.form-floating-airbnb').classList.remove('has-error');
                }
            }

            debounceTimer = setTimeout(() => {
                fetch(`/host/{{ $room->id }}/save-field`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({ field, value, table, step: {{ $step }} })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        showToast('success', 'Saved');
                        if (data.step_valid !== null) {
                            updateStepperStatus(data.step, data.step_valid);
                        }
                    } else {
                        throw new Error('Save failed');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('error', 'Error saving!');
                    if(elementId) {
                        const el = document.getElementById(elementId);
                        if(el && el.closest('.form-floating-airbnb')) {
                            el.closest('.form-floating-airbnb').classList.add('has-error');
                        }
                    }
                });
            }, 800); // 800ms debounce
        }

        // Transition buttons to loading state
        document.querySelectorAll('a.btn-next, form.host-form, button.btn-next').forEach(btn => {
            btn.addEventListener('click', function(e) {
                // If it's a link (Next step), show loader and redirect
                if(this.tagName === 'A' || this.tagName === 'BUTTON') {
                    this.classList.add('loading');
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
