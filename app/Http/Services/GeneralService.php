<?php

namespace App\Http\Services;

use App\Models\Airline;
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

    public function getAirlineDetailsByCode($carrierCode)
    {
        // Fetch airline details from the database
        $airline = Airline::where('airline_code', $carrierCode)->first();
        if ($airline) {
            return [
                'id' => $airline->id,
                'name' => $airline->airline_name,
                'logo' => $airline->logo ?? null, // Ensure there's a fallback if no logo exists
                'markup' => $airline->markup ?? 0,
                'markstatus' => $airline->markstatus ?? "markup",
            ];
        }

        return [
            'id' => null,
            'name' => 'Unknown',
            'logo' => null,
            'markup' => 0,
            'markstatus' => "markup"
        ];
    }

    public function getCabinClassMarkprice($cabinClass, $airlineId)
    {
        $airline = Airline::findOrFail($airlineId);
        $markprice = null;
        $markstatus = null;
        switch (strtolower($cabinClass)) {
            case 'economy':
                $markprice = $airline->markprice_economy;
                $markstatus = $airline->markstatus_economy;
                break;
            case 'premium_economy':
                $markprice = $airline->markprice_premium_economy;
                $markstatus = $airline->markstatus_premium_economy;
                break;
            case 'business':
                $markprice = $airline->markprice_business;
                $markstatus = $airline->markstatus_business;
                break;
            case 'first':
                $markprice = $airline->markprice_first;
                $markstatus = $airline->markstatus_first;
                break;
            default:
                $markprice = null;
                $markstatus = null;
                break;
        }
        // if($airline->airline_code == 'QR'){
        //     return dd($markprice, $markstatus, $airline);
        // }

        return [
            'markprice' => $markprice,
            'markstatus' => $markstatus,
        ];
    }

    public function addOrReplaceByLowestPrice(array $flights, array $newFlight): array
    {
        $newPrice = (int) str_replace(',', '', $newFlight['price']);
        $stopIndex = $newFlight['numberOfStops'];
        $codeKey = strtolower($newFlight['code']);
        $updated = false;

        foreach ($flights as $index => $flight) {
            if (strtolower($flight['code']) === $codeKey) {
                // If price is not yet an array, convert it
                if (!is_array($flight['price'])) {
                    $flight['price'] = [(int) str_replace(',', '', $flight['price'])];
                }

                // Ensure the array is big enough to handle new stop index
                if (!isset($flight['price'][$stopIndex])) {
                    $flight['price'][$stopIndex] = $newPrice;
                } else {
                    // Replace only if new price is lower
                    if ($newPrice < (int) $flight['price'][$stopIndex]) {
                        $flight['price'][$stopIndex] = $newPrice;
                    }
                }

                // Sort prices array to preserve index order
                ksort($flight['price']);
                $flights[$index]['price'] = $flight['price'];
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            // New flight structure
            $priceArray = ['', '', ''];
            $priceArray[$stopIndex] = $newPrice;

            $new = [
                "name" => $newFlight['name'],
                "logo" => $newFlight['logo'],
                "code" => $newFlight['code'],
                "currency" => $newFlight['currency'],
                "price" => $priceArray
            ];

            $flights[] = $new;
        }

        // Sort final array by lowest price at index 0
        usort($flights, function ($a, $b) {
            $priceA = $a['price'][0] ?? PHP_INT_MAX;
            $priceB = $b['price'][0] ?? PHP_INT_MAX;
            return $priceA <=> $priceB;
        });

        return $flights;
    }
}
