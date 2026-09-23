<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Services\AlatPayPaymentService;
use App\Models\Payment\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AlatPayWebhookController extends Controller
{
    public function __construct(protected AlatPayPaymentService $alatPay)
    {
    }

    /**
     * Official ALATPay webhook endpoint.
     * Configure this URL on the ALATPay merchant dashboard (Settings > Business).
     * @see https://docs.alatpay.ng/setup-webhook-url
     * @see https://docs.alatpay.ng/webhook-validation
     */
    public function __invoke(Request $request)
    {
        $raw = $request->getContent();
        $signature = $request->header('X-Signature') ?? $request->header('x-signature');
        $ip = $request->ip();

        $signatureValid = $this->alatPay->validateSignature($raw, $signature);
        $ipAllowed = $this->alatPay->isWhitelistedIp($ip);
        $secretConfigured = filled(config('services.alatpay.webhook_secret'));

        // Prefer HMAC validation when secret is set; otherwise fall back to IP whitelist.
        $authorized = $secretConfigured
            ? ($signatureValid || $ipAllowed)
            : $ipAllowed;

        if (!$authorized) {
            Log::warning('ALATPay webhook rejected', [
                'ip' => $ip,
                'has_signature' => (bool) $signature,
                'secret_configured' => $secretConfigured,
            ]);

            return response('Invalid signature', 401);
        }

        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            $payload = $request->all();
        }

        $extracted = $this->alatPay->extractData($payload);
        $payment = $this->alatPay->findPayment($extracted);

        if (!$payment) {
            $this->alatPay->recordEvent(
                null,
                'webhook',
                $payload,
                $extracted['normalized_status'] ?? null,
                $extracted['reference'] ?? null,
                $extracted['alatpay_order_id'] ?? null,
                'alatpay',
                $ip,
                $signatureValid,
            );

            Log::warning('ALATPay webhook payment not found', $extracted);

            // Acknowledge so ALATPay does not keep retrying forever for unknown refs.
            return response('OK', 200);
        }

        $this->alatPay->applyStatusUpdate(
            $payment,
            $extracted,
            $payload,
            'webhook',
            $ip,
            $signatureValid,
        );

        return response('OK', 200);
    }
}
