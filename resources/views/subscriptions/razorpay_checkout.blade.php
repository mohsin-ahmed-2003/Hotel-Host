<!DOCTYPE html>
<html>
<head>
    <title>Razorpay Checkout</title>
</head>
<body>
    <form action="{{ route('payment.subscription.razorpay.success') }}" method="POST" name="razorpayform">
        @csrf
        <input type="hidden" name="sub_id" value="{{ $sub_id }}">
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="{{ $order_id }}">
        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
    </form>

    <form action="{{ route('payment.subscription.razorpay.cancel') }}" method="POST" id="cancelForm" style="display: none;">
        @csrf
        <input type="hidden" name="sub_id" value="{{ $sub_id }}">
    </form>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        var options = {
            "key": "{{ $key }}",
            "amount": "{{ $amount }}",
            "currency": "INR",
            "name": "{{ config('app.name', 'Hotel Host') }}",
            "description": "Subscription for {{ $plan->name }}",
            "order_id": "{{ $order_id }}",
            "handler": function (response){
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.getElementById('razorpay_signature').value = response.razorpay_signature;
                document.razorpayform.submit();
            },
            "modal": {
                "ondismiss": function(){
                    document.getElementById('cancelForm').submit();
                }
            }
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
    </script>
</body>
</html>
