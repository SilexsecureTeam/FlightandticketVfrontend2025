<?php

namespace App\Models\Payment;

use App\Models\Payment\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlatPayPaymentEvent extends Model
{
    protected $table = 'alatpay_payment_events';

    protected $fillable = [
        'payment_id',
        'event_type',
        'source',
        'reference',
        'alatpay_order_id',
        'status',
        'payload',
        'ip_address',
        'signature_valid',
    ];

    protected $casts = [
        'payload' => 'array',
        'signature_valid' => 'boolean',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
