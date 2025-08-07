<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FlightSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'search_id',
        'flight_offers_path',
        'dictionaries_path',
        'cheapest',
        'meta',
        'passengers',
        'from_city_name',
        'to_city_name',
        'from_code',
        'to_code',
        'departure_date',
        'return_date',
        'trip_type',
        'expires_at',
        'cabinClass'
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'passengers' => 'array',
            'expires_at' => 'datetime',
            'departure_date' => 'date',
            'return_date' => 'date',
        ];
    }

    // Get flight offers from file storage
    public function getFlightOffersAttribute()
    {
        if (empty($this->flight_offers_path) || !Storage::exists($this->flight_offers_path)) {
            return [];
        }

        try {
            $content = Storage::get($this->flight_offers_path);
            return json_decode($content, true) ?? [];
        } catch (\Exception $e) {
            \Log::error('Error retrieving flight offers: ' . $e->getMessage());
            return [];
        }
    }

    // Get dictionaries from file storage
    public function getDictionariesAttribute()
    {
        if (empty($this->dictionaries_path) || !Storage::exists($this->dictionaries_path)) {
            return [];
        }

        try {
            $content = Storage::get($this->dictionaries_path);
            return json_decode($content, true) ?? [];
        } catch (\Exception $e) {
            \Log::error('Error retrieving dictionaries: ' . $e->getMessage());
            return [];
        }
    }
    
    // Delete the associated files when the model is deleted
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($model) {
            if (!empty($model->flight_offers_path) && Storage::exists($model->flight_offers_path)) {
                Storage::delete($model->flight_offers_path);
            }
            
            if (!empty($model->dictionaries_path) && Storage::exists($model->dictionaries_path)) {
                Storage::delete($model->dictionaries_path);
            }
        });
    }
}
