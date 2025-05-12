<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Payment; 
use Illuminate\Support\Facades\Auth;

class RazorpayController extends Controller
{
    public function createOrder(Request $request)
{
    try {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $orderData = [
            'receipt' => uniqid(),
            'amount' => $request->amount, 
            'currency' => 'INR',
        ];

        $razorpayOrder = $api->order->create($orderData);

        return response()->json([
            'status' => 'success',
            'order_id' => $razorpayOrder['id'],
            'amount' => $razorpayOrder['amount'],
            'currency' => $razorpayOrder['currency']
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'failed',
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function handlePaymentSuccess(Request $request)
    {
        $paymentId = $request->razorpay_payment_id;
        $orderId = $request->razorpay_order_id;
        $signature = $request->razorpay_signature;
        $amount = $request->amount;

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $attributes = [
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $signature,
        ];

        try {
            $api->utility->verifyPaymentSignature($attributes);

            $payment = Payment::create([
                'user_id' => Auth::id(),
                'payment_type' => $request->payment_type,
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
                'amount' => $amount / 100,
                'status' => 'completed',
            ]);

            return response()->json(['status' => 'Payment Successful', 'payment' => $payment]);
        } catch (\Exception $e) {
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'payment_type' => $request->payment_type,
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
                'amount' => $amount / 100,
                'status' => 'failed',
            ]);

            return response()->json(['status' => 'Payment Failed', 'error' => $e->getMessage()]);
        }
    }
}
