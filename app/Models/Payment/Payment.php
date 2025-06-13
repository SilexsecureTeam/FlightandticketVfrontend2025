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
        'payment_type',
        'payment_date',
        'payment_status',
        'transaction_id',
        'commission',
        'reference',
    ];


    public function flightOrder()
    {
        return $this->belongsTo(FlightOrder::class, 'flight_order_id');
    }
    public function hotelOrder()
    {
        return $this->belongsTo(HotelOrder::class, 'hotel_order_id');
    }
}
