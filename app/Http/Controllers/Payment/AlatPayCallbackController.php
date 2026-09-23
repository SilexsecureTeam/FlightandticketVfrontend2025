<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Services\AlatPayPaymentService;
use App\Models\Payment\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AlatPayCallbackController extends Controller
{
    public function __construct(protected AlatPayPaymentService $alatPay)
    {
    }

    /**
     * Browser redirect callback after ALATPay widget onTransaction success.
     * @see https://docs.alatpay.ng/setup-callback-url
     */
    public function show(Request $request)
    {
        $reference = $request->query('reference');
        $payment = null;

        if ($reference) {
            $payment = Payment::query()
                ->where('provider', 'alatpay')
                ->where('reference', $reference)
                ->with('flightOrder')
                ->first();
        }

        return view('payments.alatpay-callback', [
            'payment' => $payment,
            'reference' => $reference,
            'status' => $payment?->payment_status ?? 'PENDING',
        ]);
    }

    /**
     * Client-side callback from Alatpay.onTransaction (web + mobile/API apps).
     * Captures payment response even before the official webhook arrives.
     */
    public function client(Request $request)
    {
        $payload = $request->all();
        $extracted = $this->alatPay->extractData($payload);

        if (empty($extracted['reference']) && $request->filled('reference')) {
            $extracted['reference'] = $request->input('reference');
        }

        // Widget often returns { status: true, ... }
        if ($request->boolean('status') === true && empty($extracted['status'])) {
            $extracted['status'] = 'completed';
            $extracted['normalized_status'] = 'SUCCESS';
            $extracted['gateway_ok'] = true;
        }

        $payment = $this->alatPay->findPayment($extracted);

        if (!$payment && $request->filled('reference')) {
            $payment = Payment::query()
                ->where('provider', 'alatpay')
                ->where('reference', $request->input('reference'))
                ->first();
        }

        if (!$payment) {
            $this->alatPay->recordEvent(
                null,
                'client',
                $payload,
                $extracted['normalized_status'] ?? null,
                $extracted['reference'] ?? $request->input('reference'),
                $extracted['alatpay_order_id'] ?? null,
                $request->input('source', 'web'),
                $request->ip(),
                null,
            );

            Log::warning('ALATPay client callback payment not found', $extracted);

            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found',
            ], 404);
        }

        if (empty($extracted['normalized_status']) || $extracted['normalized_status'] === 'PENDING') {
            if ($extracted['gateway_ok']) {
                $extracted['normalized_status'] = 'SUCCESS';
                $extracted['status'] = $extracted['status'] ?: 'completed';
            }
        }

        $payment = $this->alatPay->applyStatusUpdate(
            $payment,
            $extracted,
            $payload,
            'callback',
            $request->ip(),
            null,
        );

        $callbackUrl = rtrim((string) config('services.alatpay.callback_url'), '/') ?: url('/payment/alatpay/callback');
        $redirect = $callbackUrl . (str_contains($callbackUrl, '?') ? '&' : '?') . 'reference=' . urlencode($payment->reference);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment status captured',
            'payment_status' => $payment->payment_status,
            'redirect' => $redirect,
        ]);
    }
}
