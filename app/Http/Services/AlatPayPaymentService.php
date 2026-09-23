<?php

namespace App\Http\Services;

use App\Mail\AlatPayPaymentStatusMail;
use App\Models\Payment\AlatPayPaymentEvent;
use App\Models\Payment\Payment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlatPayPaymentService
{
    public function widgetConfig(Payment $payment, array $customer = []): array
    {
        $callbackUrl = rtrim((string) config('services.alatpay.callback_url'), '/');
        if ($callbackUrl === '') {
            $callbackUrl = url('/payment/alatpay/callback');
        }

        $callbackUrl .= (str_contains($callbackUrl, '?') ? '&' : '?') . 'reference=' . urlencode($payment->reference);

        return [
            'apiKey' => config('services.alatpay.api_key'),
            'businessId' => config('services.alatpay.business_id'),
            'email' => $customer['email'] ?? $payment->email,
            'phone' => $customer['phone'] ?? '',
            'firstName' => $customer['firstName'] ?? '',
            'lastName' => $customer['lastName'] ?? '',
            'currency' => $customer['currency'] ?? 'NGN',
            'amount' => (float) $payment->amount,
            'metadata' => [
                'paymentReference' => $payment->reference,
                'paymentId' => $payment->id,
                'flightOrderId' => $payment->flight_order_id,
                'source' => $payment->source ?? 'web',
            ],
            'callbackUrl' => $callbackUrl,
            'jsUrl' => config('services.alatpay.js_url'),
            'reference' => $payment->reference,
        ];
    }

    public function validateSignature(string $rawPayload, ?string $receivedSignature): bool
    {
        $secret = (string) config('services.alatpay.webhook_secret');
        if ($secret === '' || $receivedSignature === null || $receivedSignature === '') {
            return false;
        }

        $computed = base64_encode(hash_hmac('sha256', $rawPayload, $secret, true));

        return hash_equals($computed, $receivedSignature);
    }

    public function isWhitelistedIp(?string $ip): bool
    {
        $allowed = config('services.alatpay.whitelist_ips', []);
        if (empty($allowed)) {
            // No IPs configured: allow only when webhook secret is also unset (local/dev).
            return blank(config('services.alatpay.webhook_secret'));
        }

        return $ip && in_array($ip, $allowed, true);
    }

    public function extractData(array $payload): array
    {
        $data = Arr::get($payload, 'Value.Data', Arr::get($payload, 'data', $payload));

        if (!is_array($data)) {
            $data = [];
        }

        $metadataRaw = Arr::get($data, 'Customer.Metadata', Arr::get($data, 'metadata'));
        $metadata = [];
        if (is_string($metadataRaw) && $metadataRaw !== '') {
            $decoded = json_decode($metadataRaw, true);
            $metadata = is_array($decoded) ? $decoded : ['raw' => $metadataRaw];
        } elseif (is_array($metadataRaw)) {
            $metadata = $metadataRaw;
        }

        $status = (string) (
            Arr::get($data, 'Status')
            ?? Arr::get($payload, 'status')
            ?? Arr::get($payload, 'Status')
            ?? ''
        );

        return [
            'data' => $data,
            'amount' => Arr::get($data, 'Amount'),
            'email' => Arr::get($data, 'Customer.Email') ?? Arr::get($data, 'email'),
            'channel' => Arr::get($data, 'Channel'),
            'alatpay_order_id' => Arr::get($data, 'OrderId') ?? Arr::get($data, 'Id'),
            'transaction_id' => Arr::get($data, 'Customer.TransactionId')
                ?? Arr::get($data, 'Id')
                ?? Arr::get($data, 'TransactionId'),
            'status' => $status,
            'normalized_status' => $this->normalizeStatus($status),
            'reference' => $metadata['paymentReference']
                ?? Arr::get($payload, 'reference')
                ?? Arr::get($data, 'Reference'),
            'payment_id' => $metadata['paymentId'] ?? null,
            'flight_order_id' => $metadata['flightOrderId'] ?? null,
            'metadata' => $metadata,
            'gateway_ok' => (bool) (
                Arr::get($payload, 'Value.Status')
                ?? Arr::get($payload, 'status')
                ?? ($status !== '' && $this->normalizeStatus($status) === 'SUCCESS')
            ),
        ];
    }

    public function normalizeStatus(?string $status): string
    {
        $value = strtolower(trim((string) $status));

        return match (true) {
            in_array($value, ['completed', 'complete', 'success', 'successful', 'paid', 'approved', 'true', '1'], true) => 'SUCCESS',
            in_array($value, ['failed', 'failure', 'declined', 'error'], true) => 'FAILED',
            in_array($value, ['canceled', 'cancelled', 'abandoned'], true) => 'CANCELED',
            in_array($value, ['pending', 'processing', 'initiated', 'initialize'], true) => 'PENDING',
            default => $value !== '' ? strtoupper($value) : 'PENDING',
        };
    }

    public function findPayment(array $extracted): ?Payment
    {
        if (!empty($extracted['payment_id'])) {
            $payment = Payment::query()
                ->where('id', $extracted['payment_id'])
                ->where('provider', 'alatpay')
                ->first();
            if ($payment) {
                return $payment;
            }
        }

        if (!empty($extracted['reference'])) {
            $payment = Payment::query()
                ->where('reference', $extracted['reference'])
                ->where('provider', 'alatpay')
                ->first();
            if ($payment) {
                return $payment;
            }
        }

        if (!empty($extracted['alatpay_order_id'])) {
            $payment = Payment::query()
                ->where('alatpay_order_id', $extracted['alatpay_order_id'])
                ->where('provider', 'alatpay')
                ->first();
            if ($payment) {
                return $payment;
            }
        }

        if (!empty($extracted['email']) && isset($extracted['amount'])) {
            return Payment::query()
                ->where('provider', 'alatpay')
                ->where('email', $extracted['email'])
                ->where('amount', $extracted['amount'])
                ->whereIn('payment_status', ['PENDING', 'pending'])
                ->latest('id')
                ->first();
        }

        return null;
    }

    public function recordEvent(
        ?Payment $payment,
        string $eventType,
        array $payload,
        ?string $status = null,
        ?string $reference = null,
        ?string $alatpayOrderId = null,
        ?string $source = null,
        ?string $ip = null,
        ?bool $signatureValid = null,
    ): AlatPayPaymentEvent {
        return AlatPayPaymentEvent::create([
            'payment_id' => $payment?->id,
            'event_type' => $eventType,
            'source' => $source ?? $payment?->source,
            'reference' => $reference ?? $payment?->reference,
            'alatpay_order_id' => $alatpayOrderId,
            'status' => $status,
            'payload' => $payload,
            'ip_address' => $ip,
            'signature_valid' => $signatureValid,
        ]);
    }

    public function applyStatusUpdate(
        Payment $payment,
        array $extracted,
        array $payload,
        string $eventType,
        ?string $ip = null,
        ?bool $signatureValid = null,
    ): Payment {
        $previousStatus = (string) $payment->payment_status;
        $newStatus = $extracted['normalized_status'] ?: $previousStatus;

        $payment->fill([
            'payment_status' => $newStatus,
            'gateway_status' => $extracted['status'] ?: $payment->gateway_status,
            'channel' => $extracted['channel'] ?: $payment->channel,
            'transaction_id' => $extracted['transaction_id'] ?: $payment->transaction_id,
            'alatpay_order_id' => $extracted['alatpay_order_id'] ?: $payment->alatpay_order_id,
            'gateway_payload' => $payload,
            'payment_date' => now('Africa/Lagos')->format('Y-m-d H:i:s'),
        ]);
        $payment->save();

        if ($payment->flightOrder) {
            $orderPaymentStatus = $newStatus === 'SUCCESS' ? 'PAID' : $newStatus;
            $payment->flightOrder->update([
                'payment_status' => $orderPaymentStatus,
                'order_status' => $newStatus === 'SUCCESS' ? 'CONFIRMED' : $payment->flightOrder->order_status,
            ]);
        }

        $this->recordEvent(
            $payment,
            $eventType,
            $payload,
            $newStatus,
            $payment->reference,
            $extracted['alatpay_order_id'] ?? null,
            $payment->source,
            $ip,
            $signatureValid,
        );

        $statusChanged = strtoupper($previousStatus) !== strtoupper($newStatus);
        $isSuccessfulPayment = $newStatus === 'SUCCESS';

        if ($statusChanged || ($isSuccessfulPayment && !$payment->status_notified_at)) {
            $this->sendStatusMail($payment, $previousStatus, $isSuccessfulPayment && strtoupper($previousStatus) !== 'SUCCESS'
                ? 'Payment Received'
                : 'Status Update');
            $payment->update(['status_notified_at' => now()]);
        }

        return $payment->fresh(['flightOrder']);
    }

    public function sendStatusMail(Payment $payment, string $previousStatus, string $eventLabel): void
    {
        $payment->loadMissing('flightOrder');
        [$bookingSummary, $flightSummary] = $this->buildBookingSummaries($payment);

        $to = (string) config('services.alatpay.notify_email', 'mymoretravels@gmail.com');

        try {
            Mail::to($to)->send(new AlatPayPaymentStatusMail(
                payment: $payment,
                previousStatus: $previousStatus,
                eventLabel: $eventLabel,
                bookingSummary: $bookingSummary,
                flightSummary: $flightSummary,
            ));
        } catch (\Throwable $e) {
            Log::error('Failed to send ALATPay status mail', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function buildBookingSummaries(Payment $payment): array
    {
        $order = $payment->flightOrder;
        $travelers = is_array($order?->travelers) ? $order->travelers : [];
        $offers = is_array($order?->flight_offers) ? $order->flight_offers : [];

        $firstTraveler = $travelers[0] ?? [];
        $name = trim(
            ($firstTraveler['name']['firstName'] ?? '') . ' ' .
            ($firstTraveler['name']['lastName'] ?? '')
        );

        $bookingSummary = [
            'pnr' => $order?->pnr,
            'order_id' => $order?->id,
            'amadeus_order_id' => $order?->order_id,
            'traveler_name' => $name !== '' ? $name : null,
            'contact_email' => $firstTraveler['contact']['emailAddress'] ?? $payment->email,
            'contact_phone' => $firstTraveler['contact']['phones'][0]['number'] ?? null,
            'travelers_count' => count($travelers),
            'payment_status' => $order?->payment_status,
            'order_status' => $order?->order_status,
        ];

        $segments = [];
        $itineraries = $offers['itineraries']
            ?? $offers[0]['itineraries']
            ?? Arr::get($offers, 'data.flightOffers.0.itineraries', []);

        foreach ((array) $itineraries as $itinerary) {
            foreach ((array) ($itinerary['segments'] ?? []) as $segment) {
                $segments[] = [
                    'airline' => $segment['carrierCode'] ?? null,
                    'carrier' => $segment['carrierCode'] ?? null,
                    'flight_number' => trim(($segment['carrierCode'] ?? '') . ' ' . ($segment['number'] ?? '')),
                    'from' => $segment['departure']['iataCode'] ?? null,
                    'to' => $segment['arrival']['iataCode'] ?? null,
                    'departure' => $segment['departure']['at'] ?? null,
                    'arrival' => $segment['arrival']['at'] ?? null,
                    'cabin' => $segment['cabin'] ?? ($segment['blacklistedInEU'] ?? null),
                ];
            }
        }

        $price = $offers['price']
            ?? $offers[0]['price']
            ?? [];

        $flightSummary = [
            'segments' => $segments,
            'total' => $price['grandTotal'] ?? $price['total'] ?? $payment->amount,
            'currency' => $price['currency'] ?? 'NGN',
        ];

        return [$bookingSummary, $flightSummary];
    }
}
