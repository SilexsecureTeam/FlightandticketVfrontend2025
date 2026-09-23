<?php

namespace App\Models\Payment;

use App\Models\Flight\FlightOrder;
use App\Models\Hotel\HotelOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_order_id',
        'hotel_order_id',
        'payment_method',
        'amount',
        'email',
        'channel',
        'provider',
        'source',
        'payment_type',
        'payment_date',
        'payment_status',
        'gateway_status',
        'transaction_id',
        'alatpay_order_id',
        'commission',
        'reference',
        'metadata',
        'gateway_payload',
        'status_notified_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'gateway_payload' => 'array',
        'status_notified_at' => 'datetime',
    ];

    public function flightOrder()
    {
        return $this->belongsTo(FlightOrder::class, 'flight_order_id');
    }

    public function hotelOrder()
    {
        return $this->belongsTo(HotelOrder::class, 'hotel_order_id');
    }

    public function alatPayEvents()
    {
        return $this->hasMany(AlatPayPaymentEvent::class, 'payment_id');
    }
}
