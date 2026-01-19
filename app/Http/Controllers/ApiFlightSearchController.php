<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\FlightSearch;
use Illuminate\Http\Request;
use App\Jobs\FetchFlightsJob;
use App\Livewire\Components\Search;
use App\Http\Controllers\Controller;
use App\Http\Services\GeneralService;

class ApiFlightSearchController extends Controller
{
    public $multiCityFlights = [
        ['from' => '', 'to' => '', 'departureDate' => null, 'fromQuery' => '', 'toQuery' => '']
    ];

    public $airlinesWithNames = [];

    public function popularCities()
    {
        $data = (new GeneralService)->popularCities();

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $data,
        ], 200);
    }

    public function searchAirports(Request $request)
    {
        $data = (new GeneralService)->getAirPort($request->input('query'));

        $dataset = [];
        foreach ($data as &$airport) {
            // Combine city name and country name
            $dataset[] =
                [
                    'city' => $airport['city'],
                    'country' => $airport['country'],
                    'city_country' => $airport['city_country'],
                    'airport' => $airport['airport'],
                    'airport_code' => $airport['airport_code']
                ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $dataset,
        ], 200);
    }

    /**
     * POST: Create flight search (quick response)
     */
    public function search(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'from' => $request->tripType !== 'multi' ? 'required|string|size:3' : 'nullable',
                'to' => $request->tripType !== 'multi' ? 'required|string|size:3' : 'nullable',
                // 'departureDate' => $request->tripType !== 'multi' ? 'required|date' : 'nullable',
                'departureDate' => $request->tripType !== 'multi' ? 'required|date|after:' . Carbon::yesterday()->format('Y-m-d') : 'nullable',
                'returnDate' => $request->tripType === 'round' ? 'required|date|after:departureDate' : 'nullable',
                'passengers.adult' => 'required|integer|min:1|max:9',
                'passengers.child' => 'nullable|integer|min:0|max:8',
                'passengers.infant' => 'nullable|integer|min:0',
                'cabinClass' => 'required|string|in:ECONOMY,PREMIUM_ECONOMY,BUSINESS,FIRST',
                'currencyCode' => 'required|string|size:3',
                'nonStop' => 'boolean',
                'maxPrice' => 'nullable|integer|min:1',
                'max' => 'integer|min:1|max:250',
                'multiCityFlights.*.from' => $request->tripType === 'multi' ? 'required|string|size:3' : 'nullable',
                'multiCityFlights.*.to' => $request->tripType === 'multi' ? 'required|string|size:3' : 'nullable',
                'multiCityFlights.*.departureDate' => $request->tripType === 'multi' ? 'required|date|after:today' : 'nullable',
            ]);

            $clientRef = 'mymore-' . uniqid(bin2hex(random_bytes(6)), true);
            session(['clientRef' => $clientRef]);
            $searchId = 'search_' . uniqid();

            $originDestinations = [];
            $cabinRestrictions = [];

            if ($request->tripType === 'multi') {
                // return dd($request->multiCityFlights);
                foreach ($request->multiCityFlights as $index => $flight) {
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

                if ($request->tripType === 'round' && $validatedData['returnDate']) {
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

            // return dd($request->multiCityFlights);

            $fligtBokked = FlightSearch::create([
                'search_id' => $searchId,
                'trip_type' => $request->tripType,
                'passengers' => $validatedData['passengers'],
                'cabinClass' => $validatedData['cabinClass'],
                'from_city_name' => $request->tripType === 'multi' ? $request->multiCityFlights[0]['fromQuery'] : $request->fromDisplayName,
                'to_city_name' => $request->tripType === 'multi' ? $request->multiCityFlights[0]['toQuery'] :  $request->toDisplayName,
                'from_code' => $request->tripType === 'multi' ? $request->multiCityFlights[0]['from'] :  $request->from,
                'to_code' => $request->tripType === 'multi' ? $request->multiCityFlights[0]['to'] :  $request->to,
                'multicity' => json_encode($request->multiCityFlights),
                'departure_date' => $request->tripType === 'multi' ? $request->multiCityFlights[0]['departureDate'] : $request->departureDate,
                'return_date' => $request->returnDate,
            ]);

            // return dd([$validatedData, $clientRef, $searchId, $fligtBokked->id, $originDestinations, $travelers, $cabinRestrictions]);

            FetchFlightsJob::dispatch($validatedData, $clientRef, $searchId, $request->tripType, $request->passengers, $request->fromDisplayName, $request->toDisplayName, $request->multiCityFlights, $request->from || '', $request->to || '', $request->departureDate || '', $request->returnDate || '', $fligtBokked->id, $originDestinations, $travelers, $cabinRestrictions);

            $searchResult = (new GeneralService)->searchDirect($validatedData, $clientRef, $searchId, $fligtBokked->id, $originDestinations, $travelers, $cabinRestrictions);

            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved successfully',
                'data' => $searchResult,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // dd( $e->errors() );
            return response()->json([
                'status' => 'error',
                // 'message' => $e->errors(),
                'data' => null,
            ], 420);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Get the HTTP response from the exception
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode(); // Get the HTTP status code
                $errorBody = json_decode($response->getBody(), true); // Decode the JSON response

                // Extract error details
                $errorMessage = $errorBody['errors'][0]['detail'] ?? 'Unknown error';
                $errorCode = $errorBody['errors'][0]['code'] ?? 'N/A';


                // Flash the error message to the user
                return response()->json([
                    'status' => 'error',
                    'message' => "Error [$errorCode]: $errorMessage",
                    'data' => null,
                ], 410);
            } else {
                // If no response is available, use the generic exception message
                return response()->json([
                    'status' => 'error',
                    'message' => 'An unexpected error occurred: ' . $e->getMessage(),
                    'data' => null,
                ], 430);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while searching for flights: ' . $e->getMessage(),
                'data' => null,
            ], 440);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data not retrieved',
            'data' => null,
        ], 450);
    }

    public function getSearchResults($searchId)
    {
        // Get search ID from URL parameter
        $searchId = $searchId;
        $flightSearch = FlightSearch::where('search_id', $searchId)->first();

        if ($flightSearch) {

            try {
                // Get data from database
                // $flightSearch = $search->where('expires_at', '>', Carbon::now());

                if ($flightSearch->expires_at < Carbon::now()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Search data expired or not found',
                        'data' => null,
                    ], 400);
                }

                // Handle flight offers as either JSON string or array
                $flightOffers = $flightSearch->flight_offers;
                if (is_string($flightOffers)) {
                    $flightOffers = json_decode($flightOffers, true);
                }

                // Handle dictionaries as either JSON string or array
                $dictionaries = $flightSearch->dictionaries;
                if (is_string($dictionaries)) {
                    $dictionaries = json_decode($dictionaries, true);
                }

                // Handle dictionaries as either JSON string or array
                $cheapestFlight = $flightSearch->cheapest;
                if (is_string($cheapestFlight)) {
                    $cheapestFlight = json_decode($cheapestFlight, true);
                }

                // Process airlines from dictionaries
                $this->processAirlines($dictionaries);

                // $flightOffersArr = [];
                // foreach($flightOffers as $flightOffer){
                //     //if(){
                //         $flightOffersArr[] = $this->filterTravelerPricingsByCabin($flightOffer);
                //     //}
                // }

                // return dd($flightOffersArr);

                // Transform the data back to the expected format
                $searchResults = [
                    'tripType' => $flightSearch->trip_type,
                    'meta' => $flightSearch->meta,
                    'flightOffers' => $flightOffers,
                    'dictionaries' => $dictionaries,
                    'cheapestFlight' => $cheapestFlight,
                    'passengers' => $flightSearch->passengers,
                    'cabinClass' => $flightSearch->cabinClass,
                    'fromCityName' => $flightSearch->from_city_name,
                    'toCityName' => $flightSearch->to_city_name,
                    'departureDate' => $flightSearch->departure_date,
                    'returnDate' => $flightSearch->return_date,
                    'multiCityFlights' => $flightSearch->multicity ? json_decode($flightSearch->multicity, true) : []
                ];
                // Extract search data for edit form
                $searchParams = [
                    'editMode' => true,
                    'from' => $flightSearch->from_code,
                    'to' => $flightSearch->to_code,
                    'fromDisplayName' => $flightSearch->from_city_name,
                    'toDisplayName' => $flightSearch->to_city_name,
                    'departureDate' => $flightSearch->departure_date,
                    'returnDate' => $flightSearch->return_date,
                    'tripType' => $flightSearch->trip_type,
                    'passengers' => $flightSearch->passengers,
                    'cabinClass' => $flightSearch->cabinClass, // Default if not stored
                    'multiCityFlights' => $flightSearch->multicity ? json_decode($flightSearch->multicity, true) : []
                ];

                // If codes are missing, try to extract them from itineraries
                if (empty($searchParams['from']) || empty($searchParams['to'])) {
                    if (!empty($flightOffers) && isset($flightOffers[0]['itineraries'][0]['segments'][0])) {
                        $firstSegment = $flightOffers[0]['itineraries'][0]['segments'][0];
                        if (empty($searchParams['from'])) {
                            $searchParams['from'] = $firstSegment['departure']['iataCode'] ?? '';
                        }
                        if (empty($searchParams['to'])) {
                            $searchParams['to'] = $firstSegment['arrival']['iataCode'] ?? '';
                        }
                    } else {
                        // If no flight offers, try to extract codes from city names
                        if (empty($searchParams['from'])) {
                            $fromParts = explode('(', $flightSearch->from_city_name);
                            if (count($fromParts) > 1) {
                                $searchParams['from'] = trim(str_replace(')', '', $fromParts[count($fromParts) - 1]));
                            }
                        }

                        if (empty($searchParams['to'])) {
                            $toParts = explode('(', $flightSearch->to_city_name);
                            if (count($toParts) > 1) {
                                $searchParams['to'] = trim(str_replace(')', '', $toParts[count($toParts) - 1]));
                            }
                        }
                    }
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'data retrieved successfully',
                    'data' => [
                        'status' => $flightSearch->status,
                        'searchResults' => $searchResults,
                        'searchParams' => $searchParams,
                        'airlinesWithNames' => $this->airlinesWithNames,
                        'filteredFlightOffers' => $flightSearch,
                    ],
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error retrieving search data: ' . $e->getMessage(),
                    'data' => null,
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No search ID provided.',
                'data' => null,
            ], 400);
        }
    }

    protected function processAirlines($dictionaries)
    {
        if (isset($dictionaries['carriers']) && is_array($dictionaries['carriers'])) {
            $this->airlinesWithNames = collect($dictionaries['carriers'])
                ->map(function ($name, $code) {
                    return [
                        'code' => $code,
                        'name' => $name
                    ];
                })
                ->values()
                ->all();
        }
    }
}
