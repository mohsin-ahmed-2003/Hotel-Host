@extends('layouts.app')
@section('content')
<div class="container py-5 text-center" style="min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
    <h2>Processing your payment...</h2>
    <p>Please do not refresh or close this page while we redirect you to Razorpay securely.</p>
    
    <div class="spinner-border text-primary mt-3" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>

    <!-- Hidden form to post success details back -->
    <form id="razorpaySuccessForm" action="{{ route('payment.razorpay.success') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="reservation_id" value="{{ $reservation_id }}">
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
    </form>
    
    <!-- Hidden form for cancellation -->
    <form id="razorpayCancelForm" action="{{ route('payment.razorpay.cancel') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="reservation_id" value="{{ $reservation_id }}">
    </form>
</div>
@endsection

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
        "key": "{{ $key }}",
        "amount": "{{ $amount }}",
        "currency": "INR",
        "name": "{{ \App\Models\SiteSetting::get('site_name', 'Booking App') }}",
        "description": "Booking for {{ $room->title ?? $room->name }}",
        "image": "{{ \App\Models\SiteSetting::get('site_logo') ? asset('storage/' . \App\Models\SiteSetting::get('site_logo')) : '' }}",
        "order_id": "{{ $order_id }}",
        "handler": function (response){
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.getElementById('razorpaySuccessForm').submit();
        },
        "prefill": {
            "name": "{{ auth()->user()->name ?? 'Guest' }}",
            "email": "{{ auth()->user()->email ?? '' }}",
            "contact": "{{ auth()->user()->phone ?? '9999999999' }}"
        },
        "theme": {
            "color": "#3395FF"
        },
        "modal": {
            "ondismiss": function(){
                document.getElementById('razorpayCancelForm').submit();
            }
        }
    };
    
    var rzp1 = new Razorpay(options);
    
    rzp1.on('payment.failed', function (response){
        // Allow user to retry within the modal, if they dismiss it triggers ondismiss
    });
    
    // Automatically open the Razorpay checkout overlay once the page loads
    window.onload = function() {
        setTimeout(() => {
            rzp1.open();
        }, 500); // Slight delay for smoother UX
    };
</script>
@endsection
