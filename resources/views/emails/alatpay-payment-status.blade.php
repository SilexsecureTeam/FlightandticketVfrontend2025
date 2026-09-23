<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ALATPay Payment Update</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color: #222; line-height: 1.5;">
    <h2 style="margin-bottom: 8px;">ALATPay {{ $eventLabel }}</h2>
    <p style="margin-top: 0;">
        Payment status
        @if ($previousStatus && strtoupper($previousStatus) !== strtoupper((string) $payment->payment_status))
            changed from <strong>{{ strtoupper($previousStatus) }}</strong> to
        @else
            is
        @endif
        <strong>{{ strtoupper((string) $payment->payment_status) }}</strong>.
    </p>

    <h3>Payment details</h3>
    <table cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; max-width: 640px;">
        <tr><td><strong>Reference</strong></td><td>{{ $payment->reference }}</td></tr>
        <tr><td><strong>Transaction ID</strong></td><td>{{ $payment->transaction_id ?: '—' }}</td></tr>
        <tr><td><strong>ALATPay Order ID</strong></td><td>{{ $payment->alatpay_order_id ?: '—' }}</td></tr>
        <tr><td><strong>Amount</strong></td><td>{{ number_format((float) $payment->amount, 2) }}</td></tr>
        <tr><td><strong>Channel</strong></td><td>{{ $payment->channel ?: '—' }}</td></tr>
        <tr><td><strong>Source</strong></td><td>{{ strtoupper((string) ($payment->source ?: 'n/a')) }}</td></tr>
        <tr><td><strong>Customer email</strong></td><td>{{ $payment->email }}</td></tr>
        <tr><td><strong>Gateway status</strong></td><td>{{ $payment->gateway_status ?: '—' }}</td></tr>
        <tr><td><strong>Payment date</strong></td><td>{{ $payment->payment_date }}</td></tr>
    </table>

    <h3>Client / booking details</h3>
    <table cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; max-width: 640px;">
        <tr><td><strong>PNR</strong></td><td>{{ $bookingSummary['pnr'] ?? '—' }}</td></tr>
        <tr><td><strong>Flight order ID</strong></td><td>{{ $bookingSummary['order_id'] ?? '—' }}</td></tr>
        <tr><td><strong>Amadeus order ID</strong></td><td>{{ $bookingSummary['amadeus_order_id'] ?? '—' }}</td></tr>
        <tr><td><strong>Primary traveler</strong></td><td>{{ $bookingSummary['traveler_name'] ?? '—' }}</td></tr>
        <tr><td><strong>Contact email</strong></td><td>{{ $bookingSummary['contact_email'] ?? '—' }}</td></tr>
        <tr><td><strong>Contact phone</strong></td><td>{{ $bookingSummary['contact_phone'] ?? '—' }}</td></tr>
        <tr><td><strong>Travelers</strong></td><td>{{ $bookingSummary['travelers_count'] ?? '—' }}</td></tr>
        <tr><td><strong>Booking payment status</strong></td><td>{{ $bookingSummary['payment_status'] ?? '—' }}</td></tr>
        <tr><td><strong>Booking order status</strong></td><td>{{ $bookingSummary['order_status'] ?? '—' }}</td></tr>
    </table>

    <h3>Flight details</h3>
    @forelse (($flightSummary['segments'] ?? []) as $index => $segment)
        <p style="margin-bottom: 4px;"><strong>Segment {{ $index + 1 }}</strong></p>
        <table cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; max-width: 640px; margin-bottom: 12px;">
            <tr><td><strong>Airline</strong></td><td>{{ $segment['airline'] ?? '—' }} ({{ $segment['carrier'] ?? '—' }})</td></tr>
            <tr><td><strong>Flight</strong></td><td>{{ $segment['flight_number'] ?? '—' }}</td></tr>
            <tr><td><strong>From</strong></td><td>{{ $segment['from'] ?? '—' }} @ {{ $segment['departure'] ?? '—' }}</td></tr>
            <tr><td><strong>To</strong></td><td>{{ $segment['to'] ?? '—' }} @ {{ $segment['arrival'] ?? '—' }}</td></tr>
            <tr><td><strong>Cabin / class</strong></td><td>{{ $segment['cabin'] ?? '—' }}</td></tr>
        </table>
    @empty
        <p>No flight segment details available.</p>
    @endforelse

    @if (!empty($flightSummary['total']))
        <p><strong>Quoted total:</strong> {{ $flightSummary['currency'] ?? '' }} {{ $flightSummary['total'] }}</p>
    @endif

    <p style="color: #666; font-size: 12px; margin-top: 24px;">
        This notification was sent automatically from {{ config('app.name') }} for ALATPay booking payments.
    </p>
</body>
</html>
