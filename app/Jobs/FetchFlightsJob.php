<?php

namespace App\Jobs;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\FlightSearch;
use App\Traits\AmadeusApiTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Throwable;

class FetchFlightsJob implements ShouldQueue
{
    use Queueable;
    use AmadeusApiTrait;

    public $validatedData;
    public $clientRef;
    public $searchId;
    public $tripType;
    public $passengers;
    public $fromDisplayName;
    public $toDisplayName;
    public $multiCityFlights;
    public $from;
    public $to;
    public $departureDate;
    public $returnDate;
    public $flightBookedId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $validatedData, string $clientRef, string $searchId, string $tripType, array $passengers, string $fromDisplayName, string $toDisplayName, array $multiCityFlights = [], string $from, string $to, string $departureDate, string $returnDate, int $flightBookedId)
    {
        $this->validatedData = $validatedData;
        $this->clientRef = $clientRef;
        $this->searchId = $searchId;
        $this->tripType = $tripType;
        $this->passengers = $passengers;
        $this->fromDisplayName = $fromDisplayName;
        $this->toDisplayName = $toDisplayName;
        $this->multiCityFlights = $multiCityFlights;
        $this->from = $from;
        $this->to = $to;
        $this->departureDate = $departureDate;
        $this->returnDate = $returnDate;
        $this->flightBookedId = $flightBookedId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $validatedData = $this->validatedData;
        $clientRef = $this->clientRef;
        $originDestinations = [];
        $cabinRestrictions = [];

        if ($this->tripType === 'multi') {
            foreach ($this->multiCityFlights as $index => $flight) {
                // Ensure date is in ISO 8601 format (YYYY-MM-DD)
                $departureDate = Carbon::parse($flight['departureDate'])->format('Y-m-d');

                $originDestinations[] = [
                    'id' => (string)($index + 1),
                    'originLocationCode' => $flight['from'],
                    'destinationLocationCode' => $flight['to'],
                    'departureDateTimeRange' => [
                        'date' => $departureDate
                    ]
                ];

                $cabinRestrictions[] = [
                    'cabin' => $validatedData['cabinClass'],
                    'coverage' => 'ALL_SEGMENTS',
                    'originDestinationIds' => [(string)($index + 1)],
                ];
            }
        } else {
            // Ensure date is in ISO 8601 format (YYYY-MM-DD)
            $departureDate = Carbon::parse($validatedData['departureDate'])->format('Y-m-d');

            $originDestinations[] = [
                'id' => '1',
                'originLocationCode' => $validatedData['from'],
                'destinationLocationCode' => $validatedData['to'],
                'departureDateTimeRange' => [
                    'date' => $departureDate
                ]
            ];

            $cabinRestrictions[] = [
                'cabin' => $validatedData['cabinClass'],
                'coverage' => 'ALL_SEGMENTS',
                'originDestinationIds' => ["1"],
            ];

            if ($this->tripType === 'round' && $validatedData['returnDate']) {
                // Ensure date is in ISO 8601 format (YYYY-MM-DD)
                $returnDate = Carbon::parse($validatedData['returnDate'])->format('Y-m-d');

                $originDestinations[] = [
                    'id' => '2',
                    'originLocationCode' => $validatedData['to'],
                    'destinationLocationCode' => $validatedData['from'],
                    'departureDateTimeRange' => [
                        'date' => $returnDate
                    ]
                ];

                $cabinRestrictions[] = [
                    'cabin' => $validatedData['cabinClass'],
                    'coverage' => 'ALL_SEGMENTS',
                    'originDestinationIds' => ["2"],
                ];
            }
        }
        $travelers = [];

        // Handle Adult Passengers
        for ($i = 1; $i <= $validatedData['passengers']['adult']; $i++) {
            $travelers[] = [
                'id' => (string)$i,
                'travelerType' => 'ADULT',
                'fareOptions' => ['STANDARD'],
            ];
        }

        // Handle Child Passengers
        if (!empty($validatedData['passengers']['child'])) {
            for ($i = 1; $i <= $validatedData['passengers']['child']; $i++) {
                $travelers[] = [
                    'id' => (string)(count($travelers) + 1), // Ensure unique IDs
                    'travelerType' => 'CHILD',
                    'fareOptions' => ['STANDARD'],
                ];
            }
        }

        // Handle Infant Passengers (Each Infant Must be Associated with an Adult)
        if (!empty($validatedData['passengers']['infant'])) {
            for ($i = 1; $i <= $validatedData['passengers']['infant']; $i++) {
                // Find an adult to associate the infant with (first available adult)
                $associatedAdultId = (string)$i; // Linking to first few adults in order

                $travelers[] = [
                    'id' => (string)(count($travelers) + 1), // Ensure unique IDs
                    'travelerType' => 'HELD_INFANT',
                    'associatedAdultId' => $associatedAdultId, // REQUIRED by Amadeus API
                    'fareOptions' => ['STANDARD'],
                ];
            }
        }

        $client = new Client();
        $baseUrl = config('services.amadeus.base_url');

        $payload = [
            'currencyCode' => $validatedData['currencyCode'],
            'originDestinations' => $originDestinations,
            'travelers' => $travelers,
            'sources' => ["GDS"], // Can be extended: "LTC","PYTON","EAC"

            'searchCriteria' => [
                'maxFlightOffers' => $validatedData['max'],
                'flightFilters' => [
                    'cabinRestrictions' => $cabinRestrictions,
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
                $numberOfStops = count($itinerary['segments']) > 1 ? count($itinerary['segments']) - 1 : 1;
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

                    $airlineDetails = $this->getAirlineDetailsByCode($segment['carrierCode']);
                    $segment['airline'] = [
                        'name' => $airlineDetails['name'],
                        'logo' => $airlineDetails['logo'],
                    ];
                    $segment['airline'] = [
                        'name' => $airlineDetails['name'],
                        'logo' => $airlineDetails['logo'],
                    ];

                    // Add the fixed markup value as commission
                    $markupValue = $airlineDetails['markup']; // Fixed markup value
                    $markupstatus = $airlineDetails['markstatus']; // Fixed markup value

                    // Add commission to the price array
                    $markupValueSign = $markupstatus == "markdown" ? -abs($markupValue) : +abs($markupValue);
                    $flightOffer['price']['commission'] = $markupValueSign;
                    $cheapestNew = [
                        "name" => $airlineDetails['name'],
                        "logo" => $airlineDetails['logo'],
                        "code" => $segment['carrierCode'],
                        "currency" => $flightOffer['price']['currency'],
                        "price" => floatval($flightOffer['price']['total'] + $markupValueSign),
                        "numberOfStops" => $numberOfStops
                    ];
                    $cheapest = $this->addOrReplaceByLowestPrice($cheapest, $cheapestNew);
                }
            }
        }

        // return dd($cheapest);

        // Store large data in files
        $flightOffersFile = 'flight_searches/' . $this->searchId . '_offers.json';
        $dictionariesFile = 'flight_searches/' . $this->searchId . '_dictionaries.json';

        // Ensure directory exists
        if (!Storage::exists('flight_searches')) {
            Storage::makeDirectory('flight_searches');
        }

        // Store large data in files
        Storage::put($flightOffersFile, json_encode($flightOffers));
        Storage::put($dictionariesFile, json_encode($dictionaries));

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
        FlightSearch::find($this->flightBookedId)->update($categorizedData);
    }

    public function failed(Throwable $exception): void
    {
        FlightSearch::where('search_id', $this->searchId)->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(), // requires column in DB
        ]);
    }

    public function getCityNameByIata($iataCode)
    {
        $airline = Airport::where('airport_code', $iataCode)->first();

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
        $airline = Airline::where('airline_code', $carrierCode)->first();
        if ($airline) {
            return [
                'name' => $airline->airline_name,
                'logo' => $airline->logo ?? null, // Ensure there's a fallback if no logo exists
                'markup' => $airline->markup ?? 0,
                'markstatus' => $airline->markstatus ?? "markup",
            ];
        }

        return [
            'name' => 'Unknown',
            'logo' => null,
            'markup' => 0,
            'markstatus' => "markup"
        ];
    }

    private function addOrReplaceByLowestPrice(array $flights, array $newFlight): array
    {
        $newPrice = (int) str_replace(',', '', $newFlight['price']);
        $stopIndex = $newFlight['numberOfStops'] - 1;
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
            $priceArray = [];
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
