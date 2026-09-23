<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Services\AlatPayPaymentService;
use App\Models\Payment\Payment;
use Illuminate\Support\Str;

class AlatPayController extends Controller
{
    public function __construct(protected AlatPayPaymentService $alatPay)
    {
    }

    public function initializePayment($orderId, $email, $amount, $type, string $source = 'web')
    {
        try {
            $timezone = 'Africa/Lagos';
            $reference = 'ALAT-' . Str::upper(Str::random(12));

            $transaction = new Payment([
                'flight_order_id' => $orderId,
                'email' => $email,
                'amount' => $amount,
                'provider' => 'alatpay',
                'source' => $source,
                'payment_type' => $type,
                'payment_status' => 'PENDING',
                'gateway_status' => 'initiated',
                'payment_date' => now($timezone)->format('Y-m-d H:i:s'),
                'reference' => $reference,
                'metadata' => [
                    'flightOrderId' => $orderId,
                    'source' => $source,
                    'paymentReference' => $reference,
                ],
            ]);

            if (!$transaction->save()) {
                return response([
                    'status' => 'error',
                    'message' => 'Could not save payment record',
                ], 500);
            }

            $this->alatPay->recordEvent(
                $transaction,
                'initiation',
                [
                    'email' => $email,
                    'amount' => $amount,
                    'payment_type' => $type,
                    'source' => $source,
                ],
                'PENDING',
                $reference,
                null,
                $source,
            );

            $widget = $this->alatPay->widgetConfig($transaction, [
                'email' => $email,
                'currency' => 'NGN',
            ]);

            return response([
                'payment_status' => 'Initialize',
                'message' => 'AlatPay initialized successfully',
                'alatpayURL' => $widget['callbackUrl'],
                'reference' => $reference,
                'payment_id' => $transaction->id,
                'widget' => $widget,
            ], 200);
        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
