<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\Payment;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AlatPayController extends Controller
{
    public function initializePayment($orderId, $email, $amount, $type)
    {
        try {
            $timezone = 'Africa/Lagos';
            $reference = 'ALAT-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(12));

            // Create payment record
            $transaction = new Payment([
                'flight_order_id' => $orderId,
                'email' => $email,
                'amount' => $amount,
                'provider' => 'alatpay',
                'payment_type' => $type,
                'payment_status' => 'PENDING',
                'payment_date' => now($timezone)->format('Y-m-d H:i:s'),
                'reference' => $reference,
            ]);

            // In a real scenario, we would hit the AlatPay API here to get a checkout URL.
            // For now, we'll return a placeholder or the data needed for the frontend.
            // According to some docs, we can redirect to their checkout with params.
            $businessId = config('services.alatpay.business_id');
            $apiKey = config('services.alatpay.api_key');
            
            // This is a placeholder URL structure for AlatPay hosted checkout
            $checkoutUrl = "https://web.alatpay.ng/checkout?businessId=$businessId&email=$email&amount=$amount&reference=$reference";

            if ($transaction->save()) {
                return response([
                    'payment_status' => 'Initialize',
                    'message' => 'AlatPay initialized successfully',
                    'alatpayURL' => $checkoutUrl,
                    'payment_id' => $transaction->id,
                ], 200);
            }

            return response([
                'status' => 'error',
                'message' => 'Could not save payment record',
            ], 500);

        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
