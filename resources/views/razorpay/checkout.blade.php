@extends('frontend.layouts.master')

@section('title', 'Razorpay Checkout')

@section('main-content')
    @if(isset($pay))
        <!-- Razorpay Checkout Form -->
        <section class="razorpay-checkout section">
            <div class="container">
                    <form action="{{ route('payment.success') }}" method="POST">
                        @csrf
                        <script
                            src="https://checkout.razorpay.com/v1/checkout.js"
                            data-key="rzp_test_ooEcNZEYeUB2sN"
                            data-amount="{{  $pay['total']*100}}" 
                            data-currency="INR"
                            data-name="Preisha wellness"
                            data-description="{{ $pay['invoice_id'] }}"
                            data-image="{{ asset('storage/photos/1/logo.png') }}"
                            data-order_id="{{ $orderId }}"
                            data-razorpay_id="rzp_test_ooEcNZEYeUB2sN"
                            data-theme.color="#F7941D">
                        </script>
                        <input type="hidden" value="{{ $order->id }}" name="razorpay_order_id">
                    </form>
            </div>
        </section>
        <!-- End Razorpay Checkout Form -->
    @endif
@endsection
