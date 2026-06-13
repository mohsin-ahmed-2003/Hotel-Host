@extends('layouts.app')

@section('title', 'Subscription Plans')

@section('styles')
    <style>
        .subscriptions-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0px 24px 80px 24px;
        }

        .subscriptions-header {
            margin-bottom: 50px;
        }

        .subscriptions-title {
            font-size: 42px;
            font-weight: 800;
            color: var(--body-text);
            margin-bottom: 16px;
            letter-spacing: -1px;
        }

        .subscriptions-subtitle {
            font-size: 18px;
            color: var(--body-muted);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .pricing-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            align-items: stretch;
        }

        .pricing-card {
            width: 100%;
            max-width: 380px;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 24px;
            padding: 40px 32px;
            display: flex;
            flex-direction: column;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
            position: relative;
            overflow: hidden;
        }

        .pricing-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #ff385c, #e31c5f);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.05);
            border-color: transparent;
        }

        .pricing-card:hover::before {
            opacity: 1;
        }

        .pricing-header-wrap {
            text-align: center;
            padding-bottom: 32px;
            margin-bottom: 32px;
            border-bottom: 1px solid #f1f5f9;
        }

        .pricing-name {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .pricing-price {
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 8px;
            color: #0f172a;
        }

        .pricing-currency {
            font-size: 56px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -1px;
        }

        .pricing-amount {
            font-size: 56px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -2px;
        }

        .pricing-duration {
            font-size: 18px;
            color: #64748b;
            font-weight: 600;
            margin-left: 4px;
        }

        .pricing-features {
            list-style: none;
            padding: 0;
            margin: 0 0 40px 0;
            flex-grow: 1;
        }

        .pricing-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            font-size: 15px;
            color: var(--body-text);
            font-weight: 500;
        }

        .feature-check {
            color: #ff385c;
            font-size: 18px;
            background: rgba(255, 56, 92, 0.1);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .feature-info {
            color: var(--body-muted);
            cursor: help;
            position: relative;
            font-size: 14px;
            transition: color 0.2s;
        }

        .feature-info:hover {
            color: var(--accent);
        }

        /* Elegant CSS Tooltip */
        .feature-info::before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(-8px);
            background: #1e293b;
            color: #ffffff;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .feature-info::after {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 6px;
            border-style: solid;
            border-color: #1e293b transparent transparent transparent;
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s;
            z-index: 100;
        }

        .feature-info:hover::before,
        .feature-info:hover::after {
            opacity: 1;
            transform: translateX(-50%) translateY(-2px);
        }

        .feature-info:hover::after {
            transform: translateX(-50%) translateY(4px);
        }

        .btn-subscribe {
            display: block;
            width: 100%;
            padding: 16px;
            text-align: center;
            background: linear-gradient(135deg, #ff385c 0%, #e31c5f 100%);
            color: #ffffff;
            font-size: 16px;
            font-weight: 700;
            border-radius: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(255, 56, 92, 0.2);
        }

        .btn-subscribe:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 56, 92, 0.4);
        }

        body.dark-mode .feature-info::before {
            background: #334155;
        }

        body.dark-mode .feature-info::after {
            border-color: #334155 transparent transparent transparent;
        }

        /* ── Toast ── */
        .toast-container {
            position: fixed; top: 20px; right: 20px;
            z-index: 9999;
            display: flex; flex-direction: column; gap: 10px;
        }

        .toast {
            position: relative;
            display: flex; align-items: center; gap: 12px;
            padding: 14px 18px;
            border-radius: 12px;
            min-width: 300px; max-width: 400px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            font-size: 14px; font-weight: 500;
            animation: toastIn 0.4s cubic-bezier(0.34,1.56,0.64,1) forwards;
            background: #fff;
        }

        .toast.success { background:#dcfce7; color:#15803d; border-left:4px solid #16a34a; }
        .toast.error   { background:#fee2e2; color:#b91c1c; border-left:4px solid #dc2626; }

        body.dark-mode .toast.success { background:rgba(16,185,129,0.15); color:#6ee7b7; border-left-color:#10b981; }
        body.dark-mode .toast.error   { background:rgba(239,68,68,0.15);  color:#fca5a5; border-left-color:#ef4444; }

        .toast-close { background:none; border:none; cursor:pointer; font-size:16px; color:inherit; opacity:0.6; margin-left:auto; }
        .toast-close:hover { opacity:1; }

        .toast-progress {
            position:absolute; bottom:0; left:0; height:3px;
            background:currentColor; opacity:0.3;
            border-radius:0 0 12px 12px;
            animation: toastProgress 5s linear forwards;
        }

        @keyframes toastIn {
            from { opacity:0; transform:translateX(60px); }
            to   { opacity:1; transform:translateX(0); }
        }
        @keyframes toastOut {
            from { opacity:1; transform:translateX(0); }
            to   { opacity:0; transform:translateX(60px); }
        }
        @keyframes toastProgress {
            from { width:100%; } to { width:0%; }
        }
    </style>
@endsection

@section('content')
    <div class="subscriptions-container">
        <div class="subscriptions-header"
            style="display: flex; justify-content: space-between; align-items: flex-start; text-align: left; flex-wrap: wrap; gap: 20px;">
            <div style="flex: 1; max-width: 700px;">
                <h1 class="subscriptions-title" style="margin-bottom: 12px;">Choose Your Plan</h1>
                <p class="subscriptions-subtitle" style="margin: 0; max-width: 100%;">Unlock powerful tools to host your
                    properties, manage reservations, and
                    maximize your earnings with our flexible subscription plans.</p>
            </div>
            @if(auth()->check())
                <div style="flex-shrink: 0; margin-top: 40px;">
                    <a href="{{ route('subscriptions.history') }}" class="btn btn-outline"
                        style="background: rgba(14, 165, 233, 0.1); border: 2px solid rgba(14, 165, 233, 0.4); color: #0ea5e9; padding: 12px 24px; border-radius: 12px; font-weight: 700; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.1);">
                        <i class="fas fa-history" style="margin-right: 8px;"></i> My Subscription History
                    </a>
                </div>
            @endif
        </div>

        <div id="toastContainer" class="toast-container"></div>
        <script>
            function showToast(msg, type = 'success') {
                let c = document.getElementById('toastContainer');
                if (!c) {
                    c = document.createElement('div');
                    c.id = 'toastContainer';
                    c.className = 'toast-container';
                    document.body.appendChild(c);
                }
                const t = document.createElement('div');
                t.className = 'toast ' + type;
                t.innerHTML = `<span>${type==='success'?'✅':'❌'}</span><span style="flex:1">${msg}</span><button class="toast-close" onclick="this.closest('.toast').remove()">✕</button><div class="toast-progress"></div>`;
                c.appendChild(t);
                setTimeout(() => { t.style.animation='toastOut 0.4s ease forwards'; setTimeout(()=>t.remove(),400); }, 5000);
            }

            document.addEventListener('DOMContentLoaded', function () {
                @if(session('error'))
                    showToast("{!! addslashes(session('error')) !!}", 'error');
                @endif

                @if(session('success'))
                    showToast("{!! addslashes(session('success')) !!}", 'success');
                @endif
            });
        </script>

        <div class="pricing-grid">
            @forelse($plans as $plan)
                <div class="pricing-card">
                    <div class="pricing-header-wrap">
                        <div class="pricing-name">{{ $plan->name }}</div>
                        <div class="pricing-price">
                            @php
                                $cur = \App\Models\Currency::where('currency_code', $plan->currency)->first();
                                $symbol = $cur ? $cur->symbol : $plan->currency;
                            @endphp
                            <span class="pricing-currency">{!! $symbol !!}</span>
                            <span class="pricing-amount">{{ number_format($plan->price, 0) }}</span>
                            <span class="pricing-duration">/ {{ $plan->duration_days }} days</span>
                        </div>
                    </div>

                    <ul class="pricing-features">
                        <li class="pricing-feature">
                            <i class="fas fa-check feature-check"></i>
                            <span>
                                @if(is_null($plan->hosting_allowed))
                                    Unlimited Properties
                                @else
                                    {{ $plan->hosting_allowed }} Propert{{ $plan->hosting_allowed > 1 ? 'ies' : 'y' }}
                                @endif
                            </span>
                            <i class="fas fa-info-circle feature-info"
                                data-tooltip="Number of properties you can list as a host"></i>
                        </li>
                        <li class="pricing-feature">
                            <i class="fas fa-check feature-check"></i>
                            <span>{{ $plan->cancellations_allowed }} Host Cancellations</span>
                            <i class="fas fa-info-circle feature-info"
                                data-tooltip="Allowed cancellations without account penalties"></i>
                        </li>
                        <li class="pricing-feature">
                            <i class="fas fa-check feature-check"></i>
                            <span>{{ $plan->cancellation_fee_reduction }}% Fee Reduction</span>
                            <i class="fas fa-info-circle feature-info" data-tooltip="Percentage reduction on service fees"></i>
                        </li>
                        <li class="pricing-feature">
                            <i class="fas fa-check feature-check"></i>
                            <span>Priority Support</span>
                            <i class="fas fa-info-circle feature-info"
                                data-tooltip="Get faster responses from our support team"></i>
                        </li>
                    </ul>

                    @if($activeSubscription && $activeSubscription->subscription_plan_id == $plan->id)
                        <button type="button" class="btn-subscribe" disabled
                            style="background: #94a3b8; cursor: not-allowed; box-shadow: none;">Subscribed</button>
                    @else
                        <form action="{{ route('subscriptions.checkout', $plan->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-subscribe">Subscribe Now</button>
                        </form>
                    @endif
                </div>
            @empty
                <div
                    style="grid-column: 1 / -1; text-align: center; padding: 60px; background: var(--card-bg); border-radius: 20px; border: 1px dashed var(--border);">
                    <i class="fas fa-box-open" style="font-size: 48px; color: var(--body-muted); margin-bottom: 16px;"></i>
                    <h3 style="font-size: 20px; color: var(--body-text); margin-bottom: 8px;">No Plans Available</h3>
                    <p style="color: var(--body-muted);">Subscription plans will be available soon. Please check back later.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection