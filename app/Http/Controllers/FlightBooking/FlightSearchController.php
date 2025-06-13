<?php

namespace App\Http\Controllers\FlightBooking;

use App\Http\Controllers\Controller;
use App\Traits\AmadeusApiTrait;
use App\Traits\HttpResponses;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FlightSearchController extends Controller
{
    use HttpResponses, AmadeusApiTrait;

    public function searchFlights(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'originDestinations' => 'required|array|min:1',
                'originDestinations.*.originLocationCode' => 'required|string|size:3', 
                'originDestinations.*.destinationLocationCode' => 'required|string|size:3', 
                'originDestinations.*.departureDate' => 'required|date|date_format:Y-m-d|after:today', // Date must be in correct format and after today
                'originDestinations.*.returnDate' => 'nullable|date|date_format:Y-m-d|after:departureDate', // Return date, if present, must be after the departure date

                'ADULT' => 'required|integer|min:1|max:9', 
                'CHILD' => 'nullable|integer|min:0|max:8', 
                'INFANT' => 'nullable|integer|min:0', 
                'travelClass' => 'nullable|string|in:ECONOMY,PREMIUM_ECONOMY,BUSINESS,FIRST',
                'includedAirlineCodes' => 'nullable|string|regex:/^([A-Z]{2}(,[A-Z]{2})*)?$/',
                'excludedAirlineCodes' => 'nullable|string|regex:/^([A-Z]{2}(,[A-Z]{2})*)?$/',
                'nonStop' => 'nullable|boolean',
                'currencyCode' => 'nullable|string|size:3',
                'maxPrice' => 'nullable|integer|min:1',
                'max' => 'nullable|integer|min:1|max:250',
            ]);

            $clientRef = 'mymore-' . uniqid('', true);

            // Build the 'originDestinations' array dynamically based on input
            $originDestinations = [];
            foreach ($validatedData['originDestinations'] as $index => $destination) {
                $originDestinations[] = [
                    'id' => (string)($index + 1), // id is 1-based index∏∏∏∏
                    'originLocationCode' => $destination['originLocationCode'],
                    'destinationLocationCode' => $destination['destinationLocationCode'],
                    'departureDateTimeRange' => [
                        'date' => $destination['departureDate'],
                        'time' => '10:00:00', // Default departure time
                    ]
                ];

                // If return date is provided, include it
                if (!empty($destination['returnDate'])) {
                    $originDestinations[] = [
                        'id' => (string)($index + 2), // The return leg gets a new id
                        'originLocationCode' => $destination['destinationLocationCode'], // Reverse origin/destination
                        'destinationLocationCode' => $destination['originLocationCode'],
                        'departureDateTimeRange' => [
                            'date' => $destination['returnDate'],
                            'time' => '10:00:00', // Default return time
                        ]
                    ];
                }
            }


            $travelers = [];
            for ($i = 1; $i <= $validatedData['ADULT']; $i++) {
                $travelers[] = [
                    'id' => (string)$i,
                    'travelerType' => 'ADULT',
                    'fareOptions' => ['STANDARD'],
                ];
            }

            if (isset($validatedData['CHILD']) && $validatedData['CHILD'] > 0) {
                for ($i = 1; $i <= $validatedData['CHILD']; $i++) {
                    $travelers[] = [
                        'id' => (string)($validatedData['ADULT'] + $i),
                        'travelerType' => 'CHILD',
                        'fareOptions' => ['STANDARD'],
                    ];
                }
            }

            $client = new Client();
            $baseUrl = config('services.amadeus.base_url');

            $response = $client->request('POST', "$baseUrl/v2/shopping/flight-offers", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getAccessToken(),
                    'Content-Type' => 'application/json',
                    'ama-client-ref' => $clientRef,
                ],
                'json' => [
                    'currencyCode' => $validatedData['currencyCode'] ?? 'USD',
                    'originDestinations' => $originDestinations,
                    'travelers' => $travelers,
                    'nonStop' => $validatedData['nonStop'] ?? false,
                    'sources' => ['GDS'],
                    'max' => $validatedData['max'] ?? 250,
                    'searchCriteria' => [
                        'maxFlightOffers' => $validatedData['max'] ?? 50, // Adjust as necessary
                        'flightFilters' => [
                            'cabinRestrictions' => [
                                [
                                    'cabin' => $validatedData['travelClass'] ?? 'ECONOMY', // Use the class from validated data
                                    'coverage' => 'MOST_SEGMENTS',
                                    'originDestinationIds' => ['1'], // Adjust based on your requirements
                                ]
                            ],
                            'carrierRestrictions' => [
                                'excludedCarrierCodes' => ['AA', 'TP', 'AZ'], // Example carrier restrictions
                            ],
                        ],
                    ],
                ],
            ]);



            $data = json_decode($response->getBody()->getContents(), true);
            return $this->success($data, 'Flight pricing retrieved successfully');
        } catch (\Exception $e) {
            // Handle errors and return error response
            return $this->error(null, 'Error retrieving flight pricing: ' . $e->getMessage(), 500);

        }
    }
}
