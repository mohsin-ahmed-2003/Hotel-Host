@extends('layouts.app')

@section('title', 'Checkout Subscription')

@section('styles')
<style>
    .checkout-container {
        max-width: 900px;
        margin: 120px auto 60px;
        padding: 0 24px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

    .checkout-section {
        background: #fff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    /* Plan Details Summary */
    .plan-summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 16px;
        font-size: 15px;
        color: #475569;
    }
    .plan-summary-item strong {
        color: #1e293b;
    }
    .plan-summary-total {
        display: flex;
        justify-content: space-between;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 2px dashed #e2e8f0;
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
    }

    /* Payment Methods Grid (copied from booking page) */
    .payment-methods-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .payment-card {
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        height: 80px;
    }

    .payment-card:hover {
        transform: translateY(-4px);
        border-color: #0ea5e9;
        box-shadow: 0 12px 24px rgba(14, 165, 233, 0.1);
    }

    .payment-card.active {
        border-color: #0ea5e9;
        background: rgba(14, 165, 233, 0.04);
        box-shadow: 0 12px 24px rgba(14, 165, 233, 0.15);
    }

    .payment-card img {
        max-height: 40px;
        max-width: 80%;
        object-fit: contain;
        transition: transform 0.2s;
    }

    .payment-card:hover img {
        transform: scale(1.05);
    }

    .payment-card-text {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .easebuzz-custom-logo {
        display: flex;
        align-items: center;
        gap: 4px;
        justify-content: center;
    }

    .easebuzz-custom-logo span {
        font-size: 20px;
        font-weight: 900;
        color: #3b82f6;
        letter-spacing: -1px;
    }

    .easebuzz-custom-logo .eb-green {
        color: #10b981;
    }

    /* Dynamic Alert Box */
    .selection-alert {
        background: rgba(14, 165, 233, 0.08);
        border: 1px solid rgba(14, 165, 233, 0.2);
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        transition: all 0.3s ease;
        margin-bottom: 24px;
    }

    .selection-alert-icon {
        color: #0ea5e9;
        font-size: 20px;
        margin-top: 2px;
    }

    .selection-alert-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .selection-alert-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .selection-alert-text {
        font-size: 13.5px;
        color: #64748b;
        line-height: 1.5;
    }

    .btn-pay-now {
        background: #0ea5e9;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 16px 36px;
        font-size: 16px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
        box-shadow: 0 8px 20px rgba(14, 165, 233, 0.25);
    }

    .btn-pay-now:hover {
        transform: translateY(-2px);
        background: #0284c7;
        box-shadow: 0 12px 24px rgba(14, 165, 233, 0.4);
    }

    @media(max-width: 768px) {
        .checkout-container {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="checkout-container">
    
    <!-- Plan Summary -->
    <div class="checkout-section">
        <h2 class="section-title">Order Summary</h2>
        <div class="plan-summary-item">
            <span>Subscription Plan</span>
            <strong>{{ $plan->name }}</strong>
        </div>
        <div class="plan-summary-item">
            <span>Duration</span>
            <strong>{{ $plan->duration_days }} days</strong>
        </div>
        <div class="plan-summary-item">
            <span>Hosting Allowed</span>
            <strong>{{ is_null($plan->hosting_allowed) ? 'Unlimited' : $plan->hosting_allowed . ' Properties' }}</strong>
        </div>
        <div class="plan-summary-item">
            <span>Host Cancellations</span>
            <strong>{{ $plan->cancellations_allowed }}</strong>
        </div>
        <div class="plan-summary-item">
            <span>Fee Reduction</span>
            <strong>{{ $plan->cancellation_fee_reduction }}%</strong>
        </div>

        <div class="plan-summary-total">
            <span>Total Price</span>
            <span>
                @php
                    $cur = \App\Models\Currency::where('currency_code', $plan->currency)->first();
                    $symbol = $cur ? $cur->symbol : $plan->currency;
                @endphp
                {!! $symbol !!}{{ number_format($plan->price, 2) }}
            </span>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="checkout-section">
        <h2 class="section-title">Select Payment Method</h2>
        
        @if(session('error'))
            <div class="alert alert-danger" style="color: #ef4444; background: #fef2f2; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        @if(!$payment_stripe && !$payment_paypal && !$payment_razorpay && !$payment_easebuzz)
            <p style="color: #64748b; text-align: center;">No payment methods are currently available. Please contact support.</p>
        @else
            <div class="payment-methods-grid">
                @if($payment_paypal)
                <div class="payment-card active" onclick="selectPaymentMethod('PayPal')" id="card-PayPal">
                    <img src="{{ asset('images/paypal-logo-png.png') }}" alt="PayPal">
                    <span class="payment-card-text">PayPal</span>
                </div>
                @else
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        if(typeof selectedPayment !== 'undefined' && selectedPayment === 'PayPal') {
                            const stripeCard = document.getElementById('card-Stripe');
                            if(stripeCard) stripeCard.click();
                        }
                    });
                </script>
                @endif

                @if($payment_stripe)
                <div class="payment-card {{ !$payment_paypal ? 'active' : '' }}" onclick="selectPaymentMethod('Stripe')" id="card-Stripe">
                    <img src="{{ asset('images/stripe_payment.png') }}" alt="Stripe">
                    <span class="payment-card-text">Stripe</span>
                </div>
                @else
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        if(typeof selectedPayment !== 'undefined' && selectedPayment === 'Stripe') {
                            const nextCard = document.getElementById('card-Easebuzz') || document.getElementById('card-Razorpay');
                            if(nextCard) nextCard.click();
                        }
                    });
                </script>
                @endif

                @if($payment_easebuzz)
                <div class="payment-card {{ (!$payment_paypal && !$payment_stripe) ? 'active' : '' }}" onclick="selectPaymentMethod('Easebuzz')" id="card-Easebuzz">
                    <div class="easebuzz-custom-logo">
                        <span>ease<span class="eb-green">buzz</span></span>
                    </div>
                    <span class="payment-card-text">Easebuzz</span>
                </div>
                @endif

                @if($payment_razorpay)
                <div class="payment-card {{ (!$payment_paypal && !$payment_stripe && !$payment_easebuzz) ? 'active' : '' }}" onclick="selectPaymentMethod('Razorpay')" id="card-Razorpay">
                    <img src="{{ asset('images/razorpay.png') }}" alt="Razorpay">
                    <span class="payment-card-text">Razorpay</span>
                </div>
                @endif
            </div>

            <!-- Selected Confirmation Alert Card -->
            <div class="selection-alert" id="selectionAlert">
                <div class="selection-alert-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="selection-alert-content">
                    <div class="selection-alert-title" id="alertTitle">
                        @if($payment_paypal) PayPal Chosen 
                        @elseif($payment_stripe) Stripe Chosen
                        @elseif($payment_easebuzz) Easebuzz Chosen
                        @elseif($payment_razorpay) Razorpay Chosen
                        @endif
                    </div>
                    <div class="selection-alert-text" id="alertMessage">
                        You have chosen to pay using 
                        <strong>
                        @if($payment_paypal) PayPal 
                        @elseif($payment_stripe) Stripe
                        @elseif($payment_easebuzz) Easebuzz
                        @elseif($payment_razorpay) Razorpay
                        @endif
                        </strong>.<br>
                        Click Pay Now to complete the subscription.
                    </div>
                </div>
            </div>

            <button class="btn-pay-now" onclick="completeSubscription()">Pay Now</button>

            <!-- Hidden Forms -->
            <form id="paypalCheckoutForm" action="{{ route('payment.subscription.paypal.initiate') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
            </form>

            <form id="stripeCheckoutForm" action="{{ route('payment.subscription.stripe.initiate') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
            </form>

            <form id="easebuzzCheckoutForm" action="{{ route('payment.subscription.easebuzz.initiate') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
            </form>

            <form id="razorpayCheckoutForm" action="{{ route('payment.subscription.razorpay.initiate') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
            </form>

            <script>
                let selectedPayment = '{{ $payment_paypal ? "PayPal" : ($payment_stripe ? "Stripe" : ($payment_easebuzz ? "Easebuzz" : ($payment_razorpay ? "Razorpay" : ""))) }}';

                function selectPaymentMethod(method) {
                    selectedPayment = method;

                    document.querySelectorAll('.payment-card').forEach(card => {
                        card.classList.remove('active');
                    });

                    const selectedCard = document.getElementById(`card-${method}`);
                    if (selectedCard) {
                        selectedCard.classList.add('active');
                    }

                    document.getElementById('alertTitle').textContent = `${method} Chosen`;
                    document.getElementById('alertMessage').innerHTML = `You have chosen to pay using <strong>${method}</strong>.<br>Click Pay Now to complete the subscription.`;
                }

                function completeSubscription() {
                    if (selectedPayment === 'PayPal') {
                        document.getElementById('paypalCheckoutForm').submit();
                    } else if (selectedPayment === 'Stripe') {
                        document.getElementById('stripeCheckoutForm').submit();
                    } else if (selectedPayment === 'Easebuzz') {
                        document.getElementById('easebuzzCheckoutForm').submit();
                    } else if (selectedPayment === 'Razorpay') {
                        document.getElementById('razorpayCheckoutForm').submit();
                    } else {
                        alert('Please select a payment method.');
                    }
                }
            </script>
        @endif
    </div>
</div>
@endsection
