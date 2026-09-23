<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Status — ALATPay</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background: #f6f7f9; margin: 0; padding: 24px; color: #1f2937; }
        .card { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 12px; padding: 28px; box-shadow: 0 8px 24px rgba(0,0,0,.06); }
        .ok { color: #067647; }
        .pending { color: #b54708; }
        .failed { color: #b42318; }
        a { color: #b42318; }
    </style>
</head>
<body>
    <div class="card">
        <h1 style="margin-top:0;">ALATPay Payment</h1>
        @php
            $status = strtoupper((string) $status);
            $class = match (true) {
                $status === 'SUCCESS' => 'ok',
                in_array($status, ['FAILED', 'CANCELED'], true) => 'failed',
                default => 'pending',
            };
        @endphp
        <p class="{{ $class }}"><strong>Status:</strong> {{ $status }}</p>
        <p><strong>Reference:</strong> {{ $reference ?: ($payment->reference ?? '—') }}</p>
        @if ($payment)
            <p><strong>Amount:</strong> {{ number_format((float) $payment->amount, 2) }}</p>
            <p><strong>Email:</strong> {{ $payment->email }}</p>
            @if ($payment->flightOrder)
                <p><strong>PNR:</strong> {{ $payment->flightOrder->pnr ?: '—' }}</p>
            @endif
        @else
            <p>We could not match this callback to a stored payment yet. If you just paid, the webhook may still be processing.</p>
        @endif
        <p style="margin-top:24px;"><a href="{{ url('/') }}">Return to homepage</a></p>
    </div>
</body>
</html>
