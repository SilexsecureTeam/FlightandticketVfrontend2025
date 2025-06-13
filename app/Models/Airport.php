<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{

    use HasFactory;


    protected $fillable = [
        'airport_code',
        'airport',
        'city_country',
        'city',
        'visible',
        'country',  
    ];

    protected function casts(): array
    {
        return [
          
            'visible' => 'boolean',
        ];
    }

}
