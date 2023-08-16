<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Razorpay\Api\Api;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use DB;

class RazorpayController extends Controller
{
    public function payment()
    {
        $cart = Cart::where('user_id', auth()->user()->id)->where('order_id', null)->get()->toArray();

        $data = [];

        $data['items'] = array_map(function ($item) use ($cart) {
            $name = Product::where('id', $item['product_id'])->pluck('title');
            return [
                'name' => $name,
                'price' => $item['price'],
                'description' => 'Thank you for using Razorpay',
                'quantity' => $item['quantity']
            ];
        }, $cart);

        $data['invoice_id'] = 'ORD-' . strtoupper(uniqid());
        $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";

        $total = 0;
        foreach ($data['items'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $data['total'] = $total;

        if (session('coupon')) {
            $data['shipping_discount'] = session('coupon')['value'];
        }

        Cart::where('user_id', auth()->user()->id)->where('order_id', null)->update(['order_id' => session()->get('id')]);

        $curlOptions = [
            CURLOPT_CAINFO => 'xampp/php/cacert.pem', // Replace with the actual path to cacert.pem
        ];

        $api = new Api('rzp_test_ooEcNZEYeUB2sN', 'nYAXUE0E13RBycqLBtb8dVkp');

        $order = $api->order->create([
            'receipt' => 'order_receipt_id',
            'amount' => $data['total']*100, // Amount in paise (e.g., for INR, 1000 paise = 10.00 INR)
            'currency' => 'INR',
        ]);

        $orderId = $order->id;

        return view('razorpay.checkout', compact('orderId','order'),['pay'=>$data]);
    }

    public function success(Request $request)
    {
        // Handle Razorpay success response here

        // For example:
        $paymentId = $request->input('razorpay_payment_id');
        // Save payment details or update your database records

        session()->flash('success', 'Payment successfully completed with Razorpay! Thank you.');
        session()->forget('cart');
        session()->forget('coupon');

        return redirect()->route('home');
    }

    public function cancel()
    {
        // Handle Razorpay payment cancelation here

        return redirect()->back();
    }
}
