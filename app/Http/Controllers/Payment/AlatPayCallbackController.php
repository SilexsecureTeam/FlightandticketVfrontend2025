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
     * Mobile app report of the Alatpay.onTransaction response.
     * The payment status is only changed by the verified webhook, since this endpoint
     * is public and the client payload cannot be trusted.
     */
    public function mobile(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $payload = $request->all();
        $extracted = $this->alatPay->extractData($payload);

        $payment = Payment::query()
            ->where('provider', 'alatpay')
            ->where('reference', $request->input('reference'))
            ->first();

        $this->alatPay->recordEvent(
            $payment,
            'client',
            $payload,
            $extracted['normalized_status'] ?? null,
            $request->input('reference'),
            $extracted['alatpay_order_id'] ?? null,
            'api',
            $request->ip(),
            null,
        );

        if (!$payment) {
            Log::warning('ALATPay mobile callback payment not found', ['reference' => $request->input('reference')]);

            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found',
            ], 404);
        }

        $updates = array_filter([
            'transaction_id' => $payment->transaction_id ?: ($extracted['transaction_id'] ?? null),
            'alatpay_order_id' => $payment->alatpay_order_id ?: ($extracted['alatpay_order_id'] ?? null),
            'channel' => $payment->channel ?: ($extracted['channel'] ?? null),
        ]);
        if (!empty($updates)) {
            $payment->update($updates);
        }

        $payment->loadMissing('flightOrder');

        return response()->json([
            'status' => 'success',
            'message' => 'Payment response received',
            'data' => [
                'reference' => $payment->reference,
                'payment_status' => $payment->payment_status,
                'amount' => (float) $payment->amount,
                'pnr' => $payment->flightOrder?->pnr,
                'order_id' => $payment->flight_order_id,
            ],
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
