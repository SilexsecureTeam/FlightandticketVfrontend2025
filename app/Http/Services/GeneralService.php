<?php

namespace App\Http\Services;

use GuzzleHttp\Client;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\FlightSearch;
use Illuminate\Support\Facades\Storage;
use App\Traits\AmadeusApiTrait;

class GeneralService
{
    use AmadeusApiTrait;
    public $showToSource = "web";

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
            $airlines = Airport::query()
                ->where('visible', '=', 1)
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

    public function getCityNameByIata($iataCode)
    {
        $airline = Airport::query()
            ->where('airport_code', '=', $iataCode)
            ->first();

        if ($airline) {
            return [
                'city' => $airline->city ?? 'Unknown',
                'country' => $airline->country ?? 'Unknown',
                'airport' => $airline->airport ?? 'Unknown',
                'city_country' => $airline->city_country ?? 'Unknown',
            ];
        }

        return [
            'city' => 'Unknown',
            'country' => 'Unknown',
            'airport' => 'Unknown',
            'city_country' => 'Unknown',
        ];
    }

    public function getAirlineDetailsByCode($carrierCode)
    {
        // Fetch airline details from the database
        $airline = Airline::query()
            ->where('airline_code', '=', $carrierCode)
            ->first();
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
        $newPrice  = (int) str_replace(',', '', $newFlight['price']);
        $stopIndex = (int) $newFlight['numberOfStops']; // 0,1,2
        $codeKey   = strtolower($newFlight['code']);
        $updated   = false;

        foreach ($flights as $index => $flight) {
            if (strtolower($flight['code']) === $codeKey) {

                // Normalize price array to 3 stops
                $prices = is_array($flight['price'])
                    ? array_replace([null, null, null], $flight['price'])
                    : [null, null, null];

                // Add or replace if lower
                if ($prices[$stopIndex] === null || $newPrice < $prices[$stopIndex]) {
                    $prices[$stopIndex] = $newPrice;
                }

                $flights[$index]['price'] = $prices;
                $updated = true;
                break;
            }
        }

        // If airline does not exist, add it
        if (!$updated) {
            $priceArray = [null, null, null];
            $priceArray[$stopIndex] = $newPrice;

            $flights[] = [
                "name"     => $newFlight['name'],
                "logo"     => $newFlight['logo'],
                "code"     => $newFlight['code'],
                "currency" => $newFlight['currency'],
                "price"    => $priceArray,
            ];
        }

        /**
         * Sort rules:
         * 1. Lowest price at index 0
         * 2. If equal/missing → index 1
         * 3. If equal/missing → index 2
         */
        usort($flights, function ($a, $b) {
            for ($i = 0; $i < 3; $i++) {
                $priceA = $a['price'][$i] ?? PHP_INT_MAX;
                $priceB = $b['price'][$i] ?? PHP_INT_MAX;

                if ($priceA !== $priceB) {
                    return $priceA <=> $priceB;
                }
            }
            return 0;
        });

        return $flights;
    }

    public function searchDirect($validatedData, $clientRef, $searchId, $fligtBokked, $originDestinations, $travelers, $cabinRestrictions)
    {

        $client = new Client();
        $baseUrl = config('services.amadeus.base_url');

        $excludedAirlineCodes = Airline::query()
            ->where('visible', '=', 0)
            ->pluck('airline_code')
            ->toArray();

        $payload = [
            'currencyCode' => $validatedData['currencyCode'],
            'originDestinations' => $originDestinations,
            'travelers' => $travelers,
            'sources' => ["GDS"], // Can be extended: "LTC","PYTON","EAC"
            // 'includedAirlineCodes' => 'QR', //KQ = Kenya Airways, QR = Qatar Airways
            // 'excludedAirlineCodes' => 'QR', //KQ = Kenya Airways, QR = Qatar Airways
            'searchCriteria' => [
                'maxFlightOffers' => 10,
                'flightFilters' => [
                    'cabinRestrictions' => $cabinRestrictions,
                    // 'connectionRestriction' => [
                    //     'maxNumberOfConnections' => 0 // 0 = direct, 1 = max 1 stop, 2 = max 2 stops
                    // ],
                    'carrierRestrictions' => [
                        'excludedCarrierCodes' => $excludedAirlineCodes ?? ["00"], //KQ = Kenya Airways, QR = Qatar Airways
                        // 'includedCarrierCodes' => ['QR'], //KQ = Kenya Airways, QR = Qatar Airways
                    ],
                ],
                'additionalInformation' => [
                    'chargeableCheckedBags' => false,
                    'brandedFares' => true,
                    'fareRules' => true,
                ],
                'pricingOptions' => [
                    'fareType' => ['PUBLISHED'],
                ],
            ],
        ];

        // return dd($payload);


        $response = $client->request('POST', "$baseUrl/v2/shopping/flight-offers", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
                'ama-client-ref' => $clientRef,
            ],
            'json' => $payload

        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        $flightOffers = $data['data'] ?? [];
        $dictionaries = $data['dictionaries'] ?? [];
        $cheapest = [];

        // return dd(json_encode($payload));
        // return dd(json_encode($flightOffers));
        // return dd(json_encode($dictionaries['carriers']));


        foreach ($flightOffers as &$flightOffer) {
            foreach ($flightOffer['itineraries'] as &$itinerary) {
                $numberOfStops = count($itinerary['segments']) - 1;
                foreach ($itinerary['segments'] as &$segment) {
                    // Replace departure iataCode with city details
                    $departureDetails = $this->getCityNameByIata($segment['departure']['iataCode']);
                    $segment['departure']['city'] = $departureDetails['city'];
                    $segment['departure']['country'] = $departureDetails['country'];
                    $segment['departure']['airport'] = $departureDetails['airport'];
                    $segment['departure']['city_country'] = $departureDetails['city_country'];

                    // Replace arrival iataCode with city details
                    $arrivalDetails = $this->getCityNameByIata($segment['arrival']['iataCode']);
                    $segment['arrival']['city'] = $arrivalDetails['city'];
                    $segment['arrival']['country'] = $arrivalDetails['country'];
                    $segment['arrival']['airport'] = $arrivalDetails['airport'];
                    $segment['arrival']['city_country'] = $arrivalDetails['city_country'];

                    $airlineDetails = (new GeneralService)->getAirlineDetailsByCode($segment['carrierCode']);
                    $segment['airline'] = [
                        'id' => $airlineDetails['id'],
                        'name' => $airlineDetails['name'],
                        'logo' => $airlineDetails['logo'],
                    ];
                    $segment['airline'] = [
                        'id' => $airlineDetails['id'],
                        'name' => $airlineDetails['name'],
                        'logo' => $airlineDetails['logo'],
                    ];

                    $markupValueSign = 0;
                    if (count($travelers) == 1 && $airlineDetails['id']) {
                        $dataClassCabin = (new GeneralService)->getCabinClassMarkprice($validatedData['cabinClass'], $airlineDetails['id']);
                        // Add the fixed markup value as commission
                        $markupValue = $dataClassCabin['markprice']; // Fixed markup value
                        $markupstatus = $dataClassCabin['markstatus']; // Fixed markup value
                        // Add commission to the price array
                        $markupValueSign = $markupstatus == "markdown" ? -abs($markupValue) : +abs($markupValue);
                    }
                    $flightOffer['price']['commission'] = $markupValueSign;
                    // if($segment['carrierCode'] == 'ET' && $flightOffer['price']['commission'] != 0){
                    //     return dd($markupValue, $markupstatus, $segment['carrierCode'], $flightOffer['price']['total'], floatval($flightOffer['price']['total'] + $markupValueSign), $flightOffer['price']['commission']);
                    // }
                    $cheapestNew = [
                        "name" => $airlineDetails['name'],
                        "logo" => $airlineDetails['logo'],
                        "code" => $segment['carrierCode'],
                        "currency" => $flightOffer['price']['currency'],
                        "price" => floatval($flightOffer['price']['total'] + $markupValueSign),
                        "numberOfStops" => $numberOfStops
                    ];
                    // $cheapest = $this->addOrReplaceByLowestPrice($cheapest, $cheapestNew);
                    $cheapest = (new GeneralService)->addOrReplaceByLowestPrice($cheapest, $cheapestNew);
                }
            }
        }

        // return dd($dictionaries);

        // return dd($cheapest);


        // $this->searchId = 'search_' . uniqid();

        // Store large data in files
        $flightOffersFile = 'flight_searches/' . $searchId . '_offers.json';
        $dictionariesFile = 'flight_searches/' . $searchId . '_dictionaries.json';

        // Ensure directory exists
        if (!Storage::exists('flight_searches')) {
            Storage::makeDirectory('flight_searches');
        }

        // Store large data in files
        Storage::put($flightOffersFile, json_encode($flightOffers));
        Storage::put($dictionariesFile, json_encode($dictionaries));
        session(['showToSource' => $this->showToSource]);

        // return dd([$this->showToSource, session('showToSource')]);

        // Create search results array with only essential data in DB
        $categorizedData = [
            'meta' => $data['meta'] ?? [],
            'cheapest' => json_encode($cheapest),
            'flight_offers_path' => $flightOffersFile,
            'dictionaries_path' => $dictionariesFile,
            'expires_at' => now()->addHours(24),
            'status' => 'completed',
        ];

        // Store in database
        $flight = FlightSearch::query()->find($fligtBokked);
        $flight->update($categorizedData);

        // return dd($flightOffers);
        // Redirect with just the search ID
        return [
            'searchId' => $searchId,
            'flight' => $flight->refresh(),
            'flightOffers' => $flightOffers,
            'dictionaries' => $dictionaries,
        ];

        // return redirect()->route('search.results');
    }
}
