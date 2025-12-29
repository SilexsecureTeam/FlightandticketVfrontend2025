<?php

namespace App\Http\Services;

use App\Models\Airport;



class GeneralService
{

    public function popularCities()
    {
        return [
            [
                'city' => 'Abuja',
                'country' => 'Nigeria ',
                'city_country' => 'Abuja, Nigeria',
                'airport' => 'Nnamdi Azikiwe International Airport',
                'airport_code' => 'ABV'
            ],
            [
                'city' => 'Lagos',
                'country' => 'Nigeria ',
                'city_country' => 'Lagos, Nigeria',
                'airport' => 'Murtala Muhammed International Airport',
                'airport_code' => 'LOS'
            ],
            [
                'city' => 'New York',
                'country' => 'United States',
                'city_country' => 'New York, USA',
                'airport' => 'John F. Kennedy International Airport',
                'airport_code' => 'JFK'
            ],
            [
                'city' => 'London',
                'country' => 'United Kingdom',
                'city_country' => 'London, UK',
                'airport' => 'Heathrow Airport',
                'airport_code' => 'LHR'
            ],
            [
                'city' => 'Paris',
                'country' => 'France',
                'city_country' => 'Paris, France',
                'airport' => 'Charles de Gaulle Airport',
                'airport_code' => 'CDG'
            ],
            [
                'city' => 'Dubai',
                'country' => 'UAE',
                'city_country' => 'Dubai, UAE',
                'airport' => 'Dubai International Airport',
                'airport_code' => 'DXB'
            ],
            [
                'city' => 'Tokyo',
                'country' => 'Japan',
                'city_country' => 'Tokyo, Japan',
                'airport' => 'Narita International Airport',
                'airport_code' => 'NRT'
            ],
            [
                'city' => 'Singapore',
                'country' => 'Singapore',
                'city_country' => 'Singapore, Singapore',
                'airport' => 'Singapore Changi Airport',
                'airport_code' => 'SIN'
            ],
            [
                'city' => 'Sydney',
                'country' => 'Australia',
                'city_country' => 'Sydney, Australia',
                'airport' => 'Sydney Airport',
                'airport_code' => 'SYD'
            ],
            [
                'city' => 'Hong Kong',
                'country' => 'China',
                'city_country' => 'Hong Kong, China',
                'airport' => 'Hong Kong International Airport',
                'airport_code' => 'HKG'
            ],
            [
                'city' => 'Amsterdam',
                'country' => 'Netherlands',
                'city_country' => 'Amsterdam, Netherlands',
                'airport' => 'Amsterdam Airport Schiphol',
                'airport_code' => 'AMS'
            ],
        ];
    }

    public function getAirPort($query)
    {
        if (strlen($query) >= 2) {
            $airlines = Airport::where('visible', true)
                ->where(function ($q) use ($query) {
                    $q->where('airport', 'like', "%$query%")
                        ->orWhere('airport_code', 'like', "%$query%")
                        ->orWhere('city', 'like', "%$query%")
                        ->orWhere('city_country', 'like', "%$query%")
                        ->orWhere('country', 'like', "%$query%");
                })
                ->orderByRaw("
                    CASE
                        WHEN airport_code LIKE ? THEN 1
                        WHEN airport LIKE ? THEN 2
                        WHEN city LIKE ? THEN 3
                        WHEN city_country LIKE ? THEN 4
                        WHEN country LIKE ? THEN 5
                        ELSE 6
                    END
                ", ["$query%", "$query%", "$query%", "$query%", "$query%"])
                ->limit(10)
                ->get();

            return $airlines->toArray();
        }
        return [];
    }
}
