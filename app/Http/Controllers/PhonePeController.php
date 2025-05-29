<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Models\Payment;
use App\Models\PhonePePayment;

class PhonePeController extends Controller
{
    public function initiate(Request $request)
    {
        $user = Auth::user();
        $transactionId = uniqid(); 
        $userId = $user->id;
        $paymentType = $request->payment_type ?? 'phonepe';

        PhonePePayment::create([
            'user_id' => $userId,
            'payment_type' => $paymentType,
            'transactionId' => $transactionId,
            'amount' => 100,
            'status' => 'initiated',
        ]);

        $data = [
            'merchantId' => env('PHONEPE_MERCHANT_ID'),
            'merchantTransactionId' => $transactionId,
            'merchantUserId' =>  'user_' . $userId,
            'amount' => 100 * 100, 
            'redirectUrl' => route('phonepe.callback'),
            'redirectMode' => 'POST',
            'callbackUrl' => route('phonepe.callback'),
            'paymentInstrument' => [
                'type' => 'PAY_PAGE',
            ],
        ];
        $encodedPayload = base64_encode(json_encode($data));

        $saltKey = env('PHONEPE_SALT_KEY');
        $saltIndex = 1;

        $path = '/pg/v1/pay';
        $url = "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay";

        $stringToHash = $encodedPayload . $path . $saltKey;
        $sha256Hash = hash('sha256', $stringToHash);
        $finalXHeader = $sha256Hash . "###" . $saltIndex;
        // Log for debugging
        Log::info("PhonePe Payload", $data);
        Log::info("Encoded Payload: $encodedPayload");
        Log::info("X-VERIFY: $finalXHeader");

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $finalXHeader
        ])->post($url, ['request' => $encodedPayload]);
         
        $rData = $response->json();

        if (isset($rData['data']['instrumentResponse']['redirectInfo']['url'])) {
           return response()->json([
                'redirect_url' => $rData['data']['instrumentResponse']['redirectInfo']['url']
            ]);
        } else {
            Log::error('PhonePe payment initiation failed', $rData);
            return response()->json(['error' => 'Failed to initiate PhonePe payment'], 500);
        }
    }


    public function callback(Request $request)
{
    Log::info('PhonePe Callback Received:', $request->all());
    $payload = $request->all();
    $frontendUrl =  env('FRONTEND_URL');

    try {
        $status = $payload['code'] ?? null;

        if ($status === 'PAYMENT_SUCCESS' || $status === 'PAYMENT_ERROR') {
            $merchantTransactionId = $payload['providerReferenceId'] ?? null;
            $transactionId = $payload['transactionId'] ?? null;

            $phonepePayment = PhonePePayment::where('transactionId', $transactionId)->first();

            if (!$phonepePayment) {
                Log::error('Transaction ID not found in PhonePePayments table.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction not found in PhonePePayments table.',
                    'm' => $payload
                ], 400);
            }

            Payment::create([
                'user_id' => $phonepePayment->user_id,
                'razorpay_order_id' => $merchantTransactionId,
                'razorpay_payment_id' => $transactionId,
                'razorpay_signature' => 'phonepe',
                'amount' => $phonepePayment->amount,
                'status' => $status === 'PAYMENT_SUCCESS' ? 'completed' : 'failed',
                'payment_type' => $phonepePayment->payment_type,
            ]);

            $phonepePayment->status = $status === 'PAYMENT_SUCCESS' ? 'completed' : 'failed';
            $phonepePayment->save();
            $redirectUrl = $status === 'PAYMENT_SUCCESS'
                ? "{$frontendUrl}/payment-success?order_id={$merchantTransactionId}&payment_id={$transactionId}"
                : "{$frontendUrl}/payment-failed?order_id={$merchantTransactionId}";

            return redirect()->away($redirectUrl);
        }

        return  redirect()->away("{$frontendUrl}/payment-failed");

    } catch (\Exception $e) {
        Log::error('PhonePe Callback Error: ' . $e->getMessage());
        return  redirect()->away("{$frontendUrl}/payment-failed");
    }
}

}