<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'hotel_offers',
        'guest',
        'payment_status',
        'order_status',
    ];

    protected function casts(): array
    {
        return [
            'hotel_offers' => 'array',
            'guest' => 'array',
        ];
    }
}
