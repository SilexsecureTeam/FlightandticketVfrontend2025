<?php

namespace App\Models\Flight;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'pnr',
        'flight_offers',
        'travelers',
        'payment_status',
        'order_status',
    ];

    protected function casts(): array
    {
        return [
            'flight_offers' => 'array',
            'travelers' => 'array',
        ];
    }
}
