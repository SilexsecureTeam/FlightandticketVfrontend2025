<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    use HasFactory;

    protected $fillable = [
        'airline_code',
        'airline_name',
        'logo',
        'markprice',
        'markstatus',
        'markup',
        'markprice_economy',
        'markstatus_economy',
        'markprice_premium_economy',
        'markstatus_premium_economy',
        'markprice_first',
        'markstatus_first',
        'markprice_business',
        'markstatus_business',
        'markdown',
        'visible',
    ];

    protected function casts(): array
    {
        return [
            'visible' => 'boolean',
        ];
    }
}
