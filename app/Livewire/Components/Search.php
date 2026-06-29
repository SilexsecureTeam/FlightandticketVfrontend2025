<?php

namespace App\Livewire\Components;

use GuzzleHttp\Client;
use App\Models\Airline;
use App\Models\Airport;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\FlightSearch;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Jobs\FetchFlightsJob;
use Illuminate\Support\Carbon;
use App\Traits\AmadeusApiTrait;
use App\Http\Services\GeneralService;
use Illuminate\Support\Facades\Storage;

class Search extends Component
{
    use AmadeusApiTrait;


    public $tripType = 'round';
    public $cabinClass = 'ECONOMY';
    public $isFlexible = false;
    public $passengers = [
        'adult' => 1,
        'child' => 0,
        'infant' => 0
    ];
    public $from;
    public $to;
    public $departureDate;
    public $returnDate;
    public $currencyCode = 'NGN';
    public $nonStop = false;
    public $maxPrice;
    public $max = 250;
    #[Url]
    public $searchId;
    public $searchQueryFrom = '';
    public $searchQueryTo = '';
    public $fromDisplayName = '';
    public $toDisplayName = '';
    public $fromAirlines = [];
    public $toAirlines = [];
    public $showFromDropdown = null;
    public $showToDropdown = null;
    public $showToSource = "web";

    #[On('updateTrip')]
    public function trip($type)
    {
        $this->tripType = $type;
        if ($type === 'multi') {
            // Initialize with two flights for multi-city
            $this->multiCityFlights = [
                [
                    'from' => '',
                    'to' => '',
                    'departureDate' => null,
                    'fromQuery' => '',
                    'toQuery' => ''
                ],
                [
                    'from' => '',
                    'to' => '',
                    'departureDate' => null,
                    'fromQuery' => '',
                    'toQuery' => ''
                ]
            ];
        }
    }

    public $popularCities;
    // [
    //     [
    //         'city' => 'Abuja',
    //         'country' => 'Nigeria ',
    //         'city_country' => 'Abuja, Nigeria',
    //         'airport' => 'Nnamdi Azikiwe International Airport',
    //         'airport_code' => 'ABV'
    //     ],
    //     [
    //         'city' => 'Lagos',
    //         'country' => 'Nigeria ',
    //         'city_country' => 'Lagos, Nigeria',
    //         'airport' => 'Murtala Muhammed International Airport',
    //         'airport_code' => 'LOS'
    //     ],
    //     [
    //         'city' => 'New York',
    //         'country' => 'United States',
    //         'city_country' => 'New York, USA',
    //         'airport' => 'John F. Kennedy International Airport',
    //         'airport_code' => 'JFK'
    //     ],
    //     [
    //         'city' => 'London',
    //         'country' => 'United Kingdom',
    //         'city_country' => 'London, UK',
    //         'airport' => 'Heathrow Airport',
    //         'airport_code' => 'LHR'
    //     ],
    //     [
    //         'city' => 'Paris',
    //         'country' => 'France',
    //         'city_country' => 'Paris, France',
    //         'airport' => 'Charles de Gaulle Airport',
    //         'airport_code' => 'CDG'
    //     ],
    //     [
    //         'city' => 'Dubai',
    //         'country' => 'UAE',
    //         'city_country' => 'Dubai, UAE',
    //         'airport' => 'Dubai International Airport',
    //         'airport_code' => 'DXB'
    //     ],
    //     [
    //         'city' => 'Tokyo',
    //         'country' => 'Japan',
    //         'city_country' => 'Tokyo, Japan',
    //         'airport' => 'Narita International Airport',
    //         'airport_code' => 'NRT'
    //     ],
    //     [
    //         'city' => 'Singapore',
    //         'country' => 'Singapore',
    //         'city_country' => 'Singapore, Singapore',
    //         'airport' => 'Singapore Changi Airport',
    //         'airport_code' => 'SIN'
    //     ],
    //     [
    //         'city' => 'Sydney',
    //         'country' => 'Australia',
    //         'city_country' => 'Sydney, Australia',
    //         'airport' => 'Sydney Airport',
    //         'airport_code' => 'SYD'
    //     ],
    //     [
    //         'city' => 'Hong Kong',
    //         'country' => 'China',
    //         'city_country' => 'Hong Kong, China',
    //         'airport' => 'Hong Kong International Airport',
    //         'airport_code' => 'HKG'
    //     ],
    //     [
    //         'city' => 'Amsterdam',
    //         'country' => 'Netherlands',
    //         'city_country' => 'Amsterdam, Netherlands',
    //         'airport' => 'Amsterdam Airport Schiphol',
    //         'airport_code' => 'AMS'
    //     ],
    // ];

    public $multiCityFlights = [
        ['from' => '', 'to' => '', 'departureDate' => null, 'fromQuery' => '', 'toQuery' => '']
    ];

    public $label;
    public $selectedDate;
    public $formattedDate;
    public $minDate;


    public function mount(
        $editMode = false,
        $from = null,
        $to = null,
        $fromDisplayName = null,
        $toDisplayName = null,
        $departureDate = null,
        $returnDate = null,
        $tripType = 'round',
        $initialPassengers = null,
        $cabinClass = 'ECONOMY',
        $multiCityFlights = [],
        $source = "web"
    ) {
        $this->showToSource = $source ?? session('showToSource');
        // Enable debugging for this method
        // \Log::info('Search component mount called', [
        //     'editMode' => $editMode,
        //     'from' => $from,
        //     'to' => $to,
        //     'fromDisplayName' => $fromDisplayName,
        //     'toDisplayName' => $toDisplayName
        // ]);

        // Ensure arrays are initialized
        $this->fromAirlines = [];
        $this->toAirlines = [];

        $this->popularCities = (new GeneralService)->popularCities();

        // Set values if in edit mode
        if ($editMode) {
            // Store values directly in properties
            $this->from = $from;
            $this->to = $to;
            $this->fromDisplayName = $fromDisplayName;
            $this->toDisplayName = $toDisplayName;
            $this->departureDate = $departureDate;
            $this->returnDate = $returnDate;
            $this->tripType = $tripType;
            $this->cabinClass = $cabinClass;
            $this->multiCityFlights = $multiCityFlights;

            // Initialize passengers from provided values or defaults
            if ($initialPassengers) {
                $this->passengers = [
                    'adult' => $initialPassengers['adult'] ?? 1,
                    'child' => $initialPassengers['child'] ?? 0,
                    'infant' => $initialPassengers['infant'] ?? 0
                ];
            }

            // IMPORTANT: Set search queries for the input fields
            $this->searchQueryFrom = $fromDisplayName;
            $this->searchQueryTo = $toDisplayName;

            // Log the state after setting values
            \Log::info('Search component initialized with edit values', [
                'from' => $this->from,
                'to' => $this->to,
                'fromDisplayName' => $this->fromDisplayName,
                'toDisplayName' => $this->toDisplayName,
                'searchQueryFrom' => $this->searchQueryFrom,
                'searchQueryTo' => $this->searchQueryTo
            ]);
        }
    }






    public function dateSelected($date, $type)
    {
        // Ensure the date is formatted as YYYY-MM-DD (ISO 8601 format)
        $this->selectedDate = Carbon::parse($date)->format('Y-m-d');
        $this->formattedDate = Carbon::parse($date)->format('D, d M Y');

        // Logic to update dates based on type
        if ($type === 'departure') {
            $this->departureDate = $this->selectedDate;
        } elseif ($type === 'return') {
            $this->returnDate = $this->selectedDate;
        }
    }



    public function updateTripType()
    {
        // Reset all properties to their default state
        $this->cabinClass = 'ECONOMY';
        $this->isFlexible = false;
        $this->passengers = [
            'adult' => 1,
            'child' => 0,
            'infant' => 0
        ];
        $this->from = null;
        $this->to = null;
        $this->departureDate = null;
        $this->returnDate = null;
        $this->currencyCode = 'NGN';
        $this->nonStop = false;
        $this->maxPrice = null;
        $this->searchQueryFrom = '';
        $this->searchQueryTo = '';
        $this->fromDisplayName = '';
        $this->toDisplayName = '';
        $this->fromAirlines = [];
        $this->toAirlines = [];
        $this->showFromDropdown = null;
        $this->showToDropdown = null;

        if ($this->tripType === 'multi') {
            // Initialize with two flights for multi-city
            $this->multiCityFlights = [
                [
                    'from' => '',
                    'to' => '',
                    'departureDate' => null,
                    'fromQuery' => '',
                    'toQuery' => ''
                ],
                [
                    'from' => '',
                    'to' => '',
                    'departureDate' => null,
                    'fromQuery' => '',
                    'toQuery' => ''
                ]
            ];
        } else {
            $this->multiCityFlights = [];
        }
    }


    public function updatedMultiCityFlights($value, $key)
    {
        // Parse the key to get the index and field (e.g., "0.fromQuery")
        $parts = explode('.', $key);
        if (count($parts) === 2 && ($parts[1] === 'fromQuery' || $parts[1] === 'toQuery')) {
            $index = $parts[0];
            $field = $parts[1];
            $query = $value;

            // Always show dropdown when typing
            if ($field === 'fromQuery') {
                $this->showFromDropdown = $index;
            } else {
                $this->showToDropdown = $index;
            }

            // Search if query is 2 or more characters
            if (strlen($query) >= 2) {
                $this->searchAirlines($query, $field === 'fromQuery' ? 'from' : 'to');
            } else {
                // Clear airlines array if query is too short
                if ($field === 'fromQuery') {
                    $this->fromAirlines = [];
                } else {
                    $this->toAirlines = [];
                }
            }
        }
    }

    public function clearMultiCityInput($field, $index)
    {
        if ($field === 'from') {
            $this->multiCityFlights[$index]['from'] = '';
            $this->multiCityFlights[$index]['fromQuery'] = '';
            $this->fromAirlines = [];
            $this->showFromDropdown = null;
        } elseif ($field === 'to') {
            $this->multiCityFlights[$index]['to'] = '';
            $this->multiCityFlights[$index]['toQuery'] = '';
            $this->toAirlines = [];
            $this->showToDropdown = null;
        }
    }

    public function updatedSearchQueryFrom($query)
    {
        if (empty($query)) {
            $this->from = '';
            $this->fromDisplayName = '';
        }
        $this->showFromDropdown = true;
        $this->searchAirlines($query, 'from');
    }

    public function updatedSearchQueryTo($query)
    {
        if (empty($query)) {
            $this->to = '';
            $this->toDisplayName = '';
        }
        $this->showToDropdown = true;
        $this->searchAirlines($query, 'to');
    }

    public function searchAirlines($query, $type)
    {
        // Initialize to empty array by default
        if ($type === 'from') {
            $this->fromAirlines = [];
        } else {
            $this->toAirlines = [];
        }
        $airlines = (new GeneralService)->getAirPort($query);
        if ($type === 'from') {
            $this->fromAirlines = $airlines;
        } else {
            $this->toAirlines = $airlines;
        }
    }



    public function setAirportCode($airportCode, $displayName, $city, $field)
    {
        if ($field === 'from') {
            $this->from = $airportCode;
            $this->fromDisplayName = $displayName;
            $this->searchQueryFrom = $displayName;
            $this->fromcity = $city;
            $this->fromAirlines = [];
            $this->showFromDropdown = false;
        } elseif ($field === 'to') {
            $this->to = $airportCode;
            $this->toDisplayName = $displayName;
            $this->tocity = $city;
            $this->searchQueryTo = $displayName;
            $this->toAirlines = [];
            $this->showToDropdown = false;
        }
    }

    public function addFlight()
    {
        $this->multiCityFlights[] = [
            'from' => '',
            'to' => '',
            'departureDate' => null,
            'fromQuery' => '',
            'toQuery' => ''
        ];
    }

    public function removeFlight($index)
    {
        if (count($this->multiCityFlights) > 1) {
            unset($this->multiCityFlights[$index]);
            $this->multiCityFlights = array_values($this->multiCityFlights);
        }
    }

    public function multiCityDateSelected($date, $index)
    {
        // return dd([$date, $index]);
        try {
            // Convert the date from MM/DD/YYYY to YYYY-MM-DD format (ISO 8601)
            $formattedDate = Carbon::parse($date)->format('Y-m-d');

            // Convert index to integer if it's a string
            $index = (int) $index;

            // Ensure the index exists in the array
            if (!isset($this->multiCityFlights[$index])) {
                return;
            }

            // Set the date in the multiCityFlights array
            $this->multiCityFlights[$index]['departureDate'] = $formattedDate;

            // If this is not the last flight, update the next flight's from field
            if (isset($this->multiCityFlights[$index + 1])) {
                $this->multiCityFlights[$index + 1]['from'] = $this->multiCityFlights[$index]['to'];
                $this->multiCityFlights[$index + 1]['fromQuery'] = $this->multiCityFlights[$index]['toQuery'];
            }
        } catch (\Exception $e) {
            // Log the error or handle it appropriately
            session()->flash('error', 'Error setting date: ' . $e->getMessage());
        }
    }

    public function setMultiCityAirportCode($airportCode, $displayName, $city, $field, $index)
    {
        if ($field === 'from') {
            $this->multiCityFlights[$index]['from'] = $airportCode;
            $this->multiCityFlights[$index]['fromQuery'] = $displayName;
            $this->fromAirlines = [];
            $this->showFromDropdown = false;
        } elseif ($field === 'to') {
            $this->multiCityFlights[$index]['to'] = $airportCode;
            $this->multiCityFlights[$index]['toQuery'] = $displayName;
            $this->toAirlines = [];
            $this->showToDropdown = false;

            // Auto-fill the next flight's from field if it exists
            if (isset($this->multiCityFlights[$index + 1])) {
                $this->multiCityFlights[$index + 1]['from'] = $airportCode;
                $this->multiCityFlights[$index + 1]['fromQuery'] = $displayName;
            }
        }
    }

    public function search()
    {
        try {
            $validatedData = $this->validate([
                'from' => $this->tripType !== 'multi' ? 'required|string|size:3' : 'nullable',
                'to' => $this->tripType !== 'multi' ? 'required|string|size:3' : 'nullable',
                // 'departureDate' => $this->tripType !== 'multi' ? 'required|date' : 'nullable',
                'departureDate' => $this->tripType !== 'multi' ? 'required|date|after:' . Carbon::yesterday()->format('Y-m-d') : 'nullable',
                'returnDate' => $this->tripType === 'round' ? 'required|date|after:departureDate' : 'nullable',
                'passengers.adult' => 'required|integer|min:1|max:9',
                'passengers.child' => 'nullable|integer|min:0|max:8',
                'passengers.infant' => 'nullable|integer|min:0',
                'cabinClass' => 'required|string|in:ECONOMY,PREMIUM_ECONOMY,BUSINESS,FIRST',
                'currencyCode' => 'required|string|size:3',
                'nonStop' => 'boolean',
                'maxPrice' => 'nullable|integer|min:1',
                'max' => 'integer|min:1|max:250',
                'multiCityFlights.*.from' => $this->tripType === 'multi' ? 'required|string|size:3' : 'nullable',
                'multiCityFlights.*.to' => $this->tripType === 'multi' ? 'required|string|size:3' : 'nullable',
                'multiCityFlights.*.departureDate' => $this->tripType === 'multi' ? 'required|date|after:today' : 'nullable',
            ]);

            $clientRef = 'mymore-' . uniqid(bin2hex(random_bytes(6)), true);
            session(['clientRef' => $clientRef]);
            $searchId = 'search_' . uniqid();

            $originDestinations = [];
            $cabinRestrictions = [];

            if ($this->tripType === 'multi') {
                // return dd($this->multiCityFlights);
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

            $fligtBokked = FlightSearch::create([
                'search_id' => $searchId,
                'clientRef' => $clientRef,
                'trip_type' => $this->tripType,
                'passengers' => $validatedData['passengers'],
                'cabinClass' => $validatedData['cabinClass'],
                'from_city_name' => $this->tripType === 'multi' ? $this->multiCityFlights[0]['fromQuery'] : $this->fromDisplayName,
                'to_city_name' => $this->tripType === 'multi' ? $this->multiCityFlights[0]['toQuery'] :  $this->toDisplayName,
                'from_code' => $this->tripType === 'multi' ? $this->multiCityFlights[0]['from'] :  $this->from,
                'to_code' => $this->tripType === 'multi' ? $this->multiCityFlights[0]['to'] :  $this->to,
                'multicity' => json_encode($this->multiCityFlights),
                'departure_date' => $this->tripType === 'multi' ? $this->multiCityFlights[0]['departureDate'] : $this->departureDate,
                'return_date' => $this->returnDate,
            ]);

            // return dd([$validatedData, $clientRef, $searchId, $fligtBokked->id, $originDestinations, $travelers, $cabinRestrictions]);

            $initialMax = 50;
            $validatedData['max'] = $validatedData['max'] ?? $this->max;

            $searchResult = (new GeneralService)->searchDirect(
                $validatedData,
                $clientRef,
                $searchId,
                $fligtBokked->id,
                $originDestinations,
                $travelers,
                $cabinRestrictions,
                $initialMax
            );

            $this->searchJob($validatedData, $clientRef, $searchId, $fligtBokked->id, $originDestinations, $travelers, $cabinRestrictions);

            session(['showToSource' => $this->showToSource]);

            return redirect()->route('search.results', ['id' => $searchResult['searchId']]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // dd( $e->errors() );
            session()->flash('errors', $e->errors());
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
                session()->flash('error', "Error [$errorCode]: $errorMessage");
            } else {
                // If no response is available, use the generic exception message
                session()->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            // dd( $e->getMessage() );
            session()->flash('error', 'An error occurred while searching for flights: ' . $e->getMessage());
        }
    }

    public function searchJob($validatedData, $clientRef, $searchId, $fligtBokked, $originDestinations, $travelers, $cabinRestrictions)
    {

        // Stage 1: Queue fetch
        FetchFlightsJob::dispatch($validatedData, $clientRef, $searchId, $this->tripType, $this->passengers, $this->fromDisplayName, $this->toDisplayName, $this->multiCityFlights, $this->from || '', $this->to || '', $this->departureDate || '', $this->returnDate || '', $fligtBokked, $originDestinations, $travelers, $cabinRestrictions);

        // Redirect with just the search ID
        // return redirect()->route('search.results', ['id' => $searchId]);

        // return redirect()->route('search.results');
    }

    public function searchDirect($validatedData, $clientRef, $searchId, $fligtBokked, $originDestinations, $travelers, $cabinRestrictions)
    {

        $client = new Client();
        $baseUrl = config('services.amadeus.base_url');

        $payload = [
            'currencyCode' => $validatedData['currencyCode'],
            'originDestinations' => $originDestinations,
            'travelers' => $travelers,
            'sources' => ["GDS"], // Can be extended: "LTC","PYTON","EAC"
            // 'includedAirlineCodes' => 'QR', //KQ = Kenya Airways, QR = Qatar Airways
            'searchCriteria' => [
                'maxFlightOffers' => 10,
                'flightFilters' => [
                    'cabinRestrictions' => $cabinRestrictions,
                    // 'connectionRestriction' => [
                    //     'maxNumberOfConnections' => 0 // 0 = direct, 1 = max 1 stop, 2 = max 2 stops
                    // ],
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
        FlightSearch::find($fligtBokked)->update($categorizedData);

        // return dd($flightOffers);
        // Redirect with just the search ID
        return redirect()->route('search.results', ['id' => $searchId]);

        // return redirect()->route('search.results');
    }

    // public function searchOld()
    // {
    //     try {
    //         $validatedData = $this->validate([
    //             'from' => $this->tripType !== 'multi' ? 'required|string|size:3' : 'nullable',
    //             'to' => $this->tripType !== 'multi' ? 'required|string|size:3' : 'nullable',
    //             'departureDate' => $this->tripType !== 'multi' ? 'required|date|after:' . Carbon::yesterday()->format('Y-m-d') : 'nullable',
    //             'returnDate' => $this->tripType === 'round' ? 'required|date|after:departureDate' : 'nullable',
    //             'passengers.adult' => 'required|integer|min:1|max:9',
    //             'passengers.child' => 'nullable|integer|min:0|max:8',
    //             'passengers.infant' => 'nullable|integer|min:0',
    //             'cabinClass' => 'required|string|in:ECONOMY,PREMIUM_ECONOMY,BUSINESS,FIRST',
    //             'currencyCode' => 'required|string|size:3',
    //             'nonStop' => 'boolean',
    //             'maxPrice' => 'nullable|integer|min:1',
    //             'max' => 'integer|min:1|max:250',
    //             'multiCityFlights.*.from' => $this->tripType === 'multi' ? 'required|string|size:3' : 'nullable',
    //             'multiCityFlights.*.to' => $this->tripType === 'multi' ? 'required|string|size:3' : 'nullable',
    //             'multiCityFlights.*.departureDate' => $this->tripType === 'multi' ? 'required|date|after:today' : 'nullable',
    //         ]);

    //         $clientRef = 'mymore-' . uniqid(bin2hex(random_bytes(6)), true);

    //         session(['clientRef' => $clientRef]);



    //         $originDestinations = [];
    //         $cabinRestrictions = [];

    //         if ($this->tripType === 'multi') {
    //             foreach ($this->multiCityFlights as $index => $flight) {
    //                 // Ensure date is in ISO 8601 format (YYYY-MM-DD)
    //                 $departureDate = Carbon::parse($flight['departureDate'])->format('Y-m-d');

    //                 $originDestinations[] = [
    //                     'id' => (string)($index + 1),
    //                     'originLocationCode' => $flight['from'],
    //                     'destinationLocationCode' => $flight['to'],
    //                     'departureDateTimeRange' => [
    //                         'date' => $departureDate
    //                     ]
    //                 ];

    //                 $cabinRestrictions[] = [
    //                     'cabin' => $validatedData['cabinClass'],
    //                     'coverage' => 'ALL_SEGMENTS',
    //                     'originDestinationIds' => [(string)($index + 1)],
    //                 ];
    //             }
    //         } else {
    //             // Ensure date is in ISO 8601 format (YYYY-MM-DD)
    //             $departureDate = Carbon::parse($validatedData['departureDate'])->format('Y-m-d');

    //             $originDestinations[] = [
    //                 'id' => '1',
    //                 'originLocationCode' => $validatedData['from'],
    //                 'destinationLocationCode' => $validatedData['to'],
    //                 'departureDateTimeRange' => [
    //                     'date' => $departureDate
    //                 ]
    //             ];

    //             $cabinRestrictions[] = [
    //                 'cabin' => $validatedData['cabinClass'],
    //                 'coverage' => 'ALL_SEGMENTS',
    //                 'originDestinationIds' => ["1"],
    //             ];

    //             if ($this->tripType === 'round' && $validatedData['returnDate']) {
    //                 // Ensure date is in ISO 8601 format (YYYY-MM-DD)
    //                 $returnDate = Carbon::parse($validatedData['returnDate'])->format('Y-m-d');

    //                 $originDestinations[] = [
    //                     'id' => '2',
    //                     'originLocationCode' => $validatedData['to'],
    //                     'destinationLocationCode' => $validatedData['from'],
    //                     'departureDateTimeRange' => [
    //                         'date' => $returnDate
    //                     ]
    //                 ];

    //                 $cabinRestrictions[] = [
    //                     'cabin' => $validatedData['cabinClass'],
    //                     'coverage' => 'ALL_SEGMENTS',
    //                     'originDestinationIds' => ["2"],
    //                 ];
    //             }
    //         }
    //         $travelers = [];

    //         // Handle Adult Passengers
    //         for ($i = 1; $i <= $validatedData['passengers']['adult']; $i++) {
    //             $travelers[] = [
    //                 'id' => (string)$i,
    //                 'travelerType' => 'ADULT',
    //                 'fareOptions' => ['STANDARD'],
    //             ];
    //         }

    //         // Handle Child Passengers
    //         if (!empty($validatedData['passengers']['child'])) {
    //             for ($i = 1; $i <= $validatedData['passengers']['child']; $i++) {
    //                 $travelers[] = [
    //                     'id' => (string)(count($travelers) + 1), // Ensure unique IDs
    //                     'travelerType' => 'CHILD',
    //                     'fareOptions' => ['STANDARD'],
    //                 ];
    //             }
    //         }

    //         // Handle Infant Passengers (Each Infant Must be Associated with an Adult)
    //         if (!empty($validatedData['passengers']['infant'])) {
    //             for ($i = 1; $i <= $validatedData['passengers']['infant']; $i++) {
    //                 // Find an adult to associate the infant with (first available adult)
    //                 $associatedAdultId = (string)$i; // Linking to first few adults in order

    //                 $travelers[] = [
    //                     'id' => (string)(count($travelers) + 1), // Ensure unique IDs
    //                     'travelerType' => 'HELD_INFANT',
    //                     'associatedAdultId' => $associatedAdultId, // REQUIRED by Amadeus API
    //                     'fareOptions' => ['STANDARD'],
    //                 ];
    //             }
    //         }

    //         $client = new Client();
    //         $baseUrl = config('services.amadeus.base_url');

    //         $payload = [
    //             'currencyCode' => $validatedData['currencyCode'],
    //             'originDestinations' => $originDestinations,
    //             'travelers' => $travelers,
    //             'sources' => ["GDS"], // Can be extended: "LTC","PYTON","EAC"

    //             'searchCriteria' => [
    //                 'maxFlightOffers' => $validatedData['max'],
    //                 'flightFilters' => [
    //                     'cabinRestrictions' => $cabinRestrictions,
    //                 ],
    //                 'additionalInformation' => [
    //                     'chargeableCheckedBags' => false,
    //                     'brandedFares' => true,
    //                     'fareRules' => true,
    //                 ],
    //                 'pricingOptions' => [
    //                     'fareType' => ['PUBLISHED'],
    //                 ],
    //             ],
    //         ];


    //         $response = $client->request('POST', "$baseUrl/v2/shopping/flight-offers", [
    //             'headers' => [
    //                 'Authorization' => 'Bearer ' . $this->getAccessToken(),
    //                 'Content-Type' => 'application/json',
    //                 'ama-client-ref' => $clientRef,
    //             ],
    //             'json' => $payload

    //         ]);

    //         $data = json_decode($response->getBody()->getContents(), true);
    //         $flightOffers = $data['data'] ?? [];
    //         $dictionaries = $data['dictionaries'] ?? [];
    //         $cheapest = [];

    //         // return dd(json_encode($payload));
    //         // return dd(json_encode($flightOffers));
    //         // return dd(json_encode($dictionaries['carriers']));


    //         foreach ($flightOffers as &$flightOffer) {
    //             foreach ($flightOffer['itineraries'] as &$itinerary) {
    //                 $numberOfStops = count($itinerary['segments']) - 1;
    //                 foreach ($itinerary['segments'] as &$segment) {
    //                     // Replace departure iataCode with city details
    //                     $departureDetails = $this->getCityNameByIata($segment['departure']['iataCode']);
    //                     $segment['departure']['city'] = $departureDetails['city'];
    //                     $segment['departure']['country'] = $departureDetails['country'];
    //                     $segment['departure']['airport'] = $departureDetails['airport'];
    //                     $segment['departure']['city_country'] = $departureDetails['city_country'];

    //                     // Replace arrival iataCode with city details
    //                     $arrivalDetails = $this->getCityNameByIata($segment['arrival']['iataCode']);
    //                     $segment['arrival']['city'] = $arrivalDetails['city'];
    //                     $segment['arrival']['country'] = $arrivalDetails['country'];
    //                     $segment['arrival']['airport'] = $arrivalDetails['airport'];
    //                     $segment['arrival']['city_country'] = $arrivalDetails['city_country'];

    //                     $airlineDetails = $this->getAirlineDetailsByCode($segment['carrierCode']);
    //                     $segment['airline'] = [
    //                         'name' => $airlineDetails['name'],
    //                         'logo' => $airlineDetails['logo'],
    //                     ];
    //                     $segment['airline'] = [
    //                         'name' => $airlineDetails['name'],
    //                         'logo' => $airlineDetails['logo'],
    //                     ];

    //                     // Add the fixed markup value as commission
    //                     $markupValue = $airlineDetails['markup']; // Fixed markup value
    //                     $markupstatus = $airlineDetails['markstatus']; // Fixed markup value

    //                     // Add commission to the price array
    //                     $markupValueSign = $markupstatus == "markdown" ? -abs($markupValue) : +abs($markupValue);
    //                     $flightOffer['price']['commission'] = $markupValueSign;
    //                     $cheapestNew = [
    //                         "name" => $airlineDetails['name'],
    //                         "logo" => $airlineDetails['logo'],
    //                         "code" => $segment['carrierCode'],
    //                         "currency" => $flightOffer['price']['currency'],
    //                         "price" => floatval($flightOffer['price']['total'] + $markupValueSign),
    //                         "numberOfStops" => $numberOfStops
    //                     ];
    //                     $cheapest = $this->addOrReplaceByLowestPrice($cheapest, $cheapestNew);
    //                 }
    //             }
    //         }

    //         // return dd($cheapest);


    //         $this->searchId = 'search_' . uniqid();

    //         // Store large data in files
    //         $flightOffersFile = 'flight_searches/' . $this->searchId . '_offers.json';
    //         $dictionariesFile = 'flight_searches/' . $this->searchId . '_dictionaries.json';

    //         // Ensure directory exists
    //         if (!Storage::exists('flight_searches')) {
    //             Storage::makeDirectory('flight_searches');
    //         }

    //         // Store large data in files
    //         Storage::put($flightOffersFile, json_encode($flightOffers));
    //         Storage::put($dictionariesFile, json_encode($dictionaries));

    //         // Create search results array with only essential data in DB
    //         $categorizedData = [
    //             'search_id' => $this->searchId,
    //             'trip_type' => $this->tripType,
    //             'meta' => $data['meta'] ?? [],
    //             'cheapest' => json_encode($cheapest),
    //             'flight_offers_path' => $flightOffersFile,
    //             'dictionaries_path' => $dictionariesFile,
    //             'passengers' => $validatedData['passengers'],
    //             'cabinClass' => $validatedData['cabinClass'],
    //             'from_city_name' => $this->fromDisplayName,
    //             'to_city_name' => $this->toDisplayName,
    //             'from_code' => $this->from,
    //             'to_code' => $this->to,
    //             'departure_date' => $this->departureDate,
    //             'return_date' => $this->returnDate,
    //             'expires_at' => now()->addHours(24)
    //         ];

    //         // Store in database
    //         FlightSearch::create($categorizedData);

    //         // Redirect with just the search ID
    //         return redirect()->route('search.results', ['id' => $this->searchId]);

    //         // return redirect()->route('search.results');

    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         // dd( $e->errors() );
    //         session()->flash('errors', $e->errors());
    //     } catch (\GuzzleHttp\Exception\RequestException $e) {
    //         // Get the HTTP response from the exception
    //         if ($e->hasResponse()) {
    //             $response = $e->getResponse();
    //             $statusCode = $response->getStatusCode(); // Get the HTTP status code
    //             $errorBody = json_decode($response->getBody(), true); // Decode the JSON response

    //             // Extract error details
    //             $errorMessage = $errorBody['errors'][0]['detail'] ?? 'Unknown error';
    //             $errorCode = $errorBody['errors'][0]['code'] ?? 'N/A';


    //             // Flash the error message to the user
    //             session()->flash('error', "Error [$errorCode]: $errorMessage");
    //         } else {
    //             // If no response is available, use the generic exception message
    //             session()->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
    //         }
    //     } catch (\Exception $e) {
    //         // dd( $e->getMessage() );
    //         session()->flash('error', 'An error occurred while searching for flights: ' . $e->getMessage());
    //     }
    // }

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



    public function render()
    {

        return view('livewire.components.search');
    }

    protected function rules()
    {
        $rules = [
            'tripType' => 'required|in:oneway,roundtrip,multi',
            'fromQuery' => 'required|min:2',
            'toQuery' => 'required|min:2',
            'from' => 'required|size:3',
            'to' => 'required|size:3',
            'departureDate' => 'required|date|after_or_equal:today',
            'returnDate' => 'required_if:tripType,roundtrip|date|after_or_equal:departureDate',
            'adults' => 'required|integer|min:1|max:9',
            'children' => 'required|integer|min:0|max:9',
            'infants' => 'required|integer|min:0|max:9',
            'cabinClass' => 'required|in:ECONOMY,PREMIUM_ECONOMY,BUSINESS,FIRST',
            'multiCityFlights' => 'required_if:tripType,multi|array|min:1',
            'multiCityFlights.*.from' => 'required|size:3',
            'multiCityFlights.*.to' => 'required|size:3',
            'multiCityFlights.*.departureDate' => 'required|date|after_or_equal:today',
        ];

        return $rules;
    }

    private function addOrReplaceByLowestPrice(array $flights, array $newFlight): array
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
