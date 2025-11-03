<?php

namespace App\Livewire\Flight;

use App\Models\Airline;
use App\Models\Airport;
use App\Traits\AmadeusApiTrait;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class SearchFlightPageCopy extends Component
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
    public $searchQueryFrom = '';
    public $searchQueryTo = '';
    public $fromDisplayName = '';
    public $toDisplayName = '';
    public $fromAirlines = [];
    public $toAirlines = [];
    public $showFromDropdown = false;
    public $showToDropdown = false;
    public $multiCityFlights = [
        ['from' => '', 'to' => '', 'departureDate' => null, 'returnDate' => null],
        ['from' => '', 'to' => '', 'departureDate' => null, 'returnDate' => null]
    ];

    public $label;
    public $selectedDate;
    public $formattedDate;
    public $minDate;


    public function mount()
    {
        // Ensure arrays are initialized
        $this->fromAirlines = [];
        $this->toAirlines = [];
        $this->fromDisplayName = '';
        $this->toDisplayName = '';
    }
    
    
    #[On('updateTrip')]
    public function trip($type)
    {
        $this->tripType = $type;
    }

    public function dateSelected($date, $type)
    {
        
        // Ensure the date is formatted as YYYY-MM-DD
        $this->selectedDate = Carbon::parse($date)->format('Y-m-d'); 
        $this->formattedDate = Carbon::parse($date)->format('D, d M Y'); 
    
        // Logic to update dates based on type
        if ($type === 'departure') {
            $this->departureDate = $this->selectedDate;
        } elseif ($type === 'return') {
            $this->returnDate = $this->selectedDate;
        }
    }

    

    public function updateTripType($type)
    {
        $this->tripType = $type;
    
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
        $this->showFromDropdown = false;
        $this->showToDropdown = false;
    
        if ($type === 'multi') {
            $this->multiCityFlights = [
                ['from' => '', 'to' => '', 'departureDate' => null, 'returnDate' => null],
                ['from' => '', 'to' => '', 'departureDate' => null, 'returnDate' => null]
            ];
        } else {
            $this->multiCityFlights = [];
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
        
        if (strlen($query) >= 2) {
            $airlines = Airport::where('visible', true)
                ->where(function($q) use ($query) {
                    $q->where('airport', 'like', "%$query%")
                      ->orWhere('airport_code', 'like', "%$query%")
                      ->orWhere('city', 'like', "%$query%")
                      ->orWhere('country', 'like', "%$query%")
                      ->orWhere('city_country', 'like', "%$query%");
                })
                ->limit(10)
                ->get();
    
            if ($type === 'from') {
                $this->fromAirlines = $airlines->toArray();
            } else {
                $this->toAirlines = $airlines->toArray();
            }
        } else {
            if ($type === 'from') {
                $this->fromAirlines = [];
            } else {
                $this->toAirlines = [];
            }
        }
    }
    public function setAirportCode($airportCode, $displayName, $field)
    {
        if ($field === 'from') {
            $this->from = $airportCode;
            $this->fromDisplayName = $displayName;
            $this->searchQueryFrom = $displayName;
            $this->fromAirlines = [];
            $this->showFromDropdown = false;
        } elseif ($field === 'to') {
            $this->to = $airportCode;
            $this->toDisplayName = $displayName;
            $this->searchQueryTo = $displayName;
            $this->toAirlines = [];
            $this->showToDropdown = false;
        }
    }
    public function search()
    {
      

        try {

            $validatedData = $this->validate([
                'from' => 'required|string|size:3',
                'to' => 'required|string|size:3',
                'departureDate' => 'required|date|after:today',
                'returnDate' => 'nullable|date|after:departureDate',
                'passengers.adult' => 'required|integer|min:1|max:9',
                'passengers.child' => 'nullable|integer|min:0|max:8',
                'passengers.infant' => 'nullable|integer|min:0',
                'cabinClass' => 'required|string|in:ECONOMY,PREMIUM_ECONOMY,BUSINESS,FIRST',
                'currencyCode' => 'required|string|size:3',
                'nonStop' => 'boolean',
                'maxPrice' => 'nullable|integer|min:1',
                'max' => 'integer|min:1|max:250',
         // Apply rules to multiCityFlights only if tripType is 'multi'
                'multiCityFlights.*.from' => $this->tripType === 'multi' ? 'required|string|size:3' : 'nullable',
                'multiCityFlights.*.to' => $this->tripType === 'multi' ? 'required|string|size:3' : 'nullable',
                'multiCityFlights.*.departureDate' => $this->tripType === 'multi' ? 'required|date|after:today' : 'nullable',
                ]);

    
                $clientRef = 'mymore-' . uniqid(bin2hex(random_bytes(6)), true);

            session(['clientRef' => $clientRef]);



            $originDestinations = [
                [
                    'id' => '1',
                    'originLocationCode' => $validatedData['from'],
                    'destinationLocationCode' => $validatedData['to'],
                    'departureDateTimeRange' => [
                        'date' => $validatedData['departureDate'],
                   'time' => Carbon::now()->format('H:i:s'),
                    ]
                ]
            ];

            if ($this->tripType === 'round' && $validatedData['returnDate']) {
                $originDestinations[] = [
                    'id' => '2',
                    'originLocationCode' => $validatedData['to'],
                    'destinationLocationCode' => $validatedData['from'],
                    'departureDateTimeRange' => [
                        'date' => $validatedData['returnDate'],
                     'time' => Carbon::now()->format('H:i:s'),
                    ]
                ];
            } elseif ($this->tripType === 'multi') {
                foreach ($this->multiCityFlights as $index => $flight) {
                    $originDestinations[] = [
                        'id' => (string)($index + 2),
                        'originLocationCode' => $flight['from'],
                        'destinationLocationCode' => $flight['to'],
                        'departureDateTimeRange' => [
                            'date' => $flight['departureDate'],
                            'time' => Carbon::now()->format('H:i:s'),
                        ]
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

            $payload =   [
                'currencyCode' => $validatedData['currencyCode'],
                'originDestinations' => $originDestinations,
                'travelers' => $travelers,
                'nonStop' => $validatedData['nonStop'],
                'sources' => ["GDS"],
                'searchCriteria' => [
                    'maxFlightOffers' => 10,
                    'flightFilters' => [
                        'cabinRestrictions' => [
                            [
                                'cabin' => $validatedData['cabinClass'],
                                'coverage' => 'MOST_SEGMENTS',
                                'originDestinationIds' => ['1'],
                            ]
                        ],
                        'carrierRestrictions' => [
                            'excludedCarrierCodes' => [  "AA",
                    "TP",
                    "AZ"],
                        ],
                    ],
                ],
            ];
            // dd($payload);

            $response = $client->request('POST', "$baseUrl/v2/shopping/flight-offers", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getAccessToken(),
                    'Content-Type' => 'application/json',
                    'ama-client-ref' => $clientRef,
                ],
                'json' =>$payload
               
            ]);
        //     $filePath = 'flight_result.json'; 
        //     $data = json_decode($response->getBody()->getContents(), true);
        //      Storage::put($filePath, json_encode($data, JSON_PRETTY_PRINT));
        // //   dd( $data);
            $data = json_decode($response->getBody()->getContents(), true);
            $flightOffers = $data['data']?? [];
            $dictionaries = $data['dictionaries'] ?? [];

            foreach ($flightOffers as &$flightOffer) {
                foreach ($flightOffer['itineraries'] as &$itinerary) {
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

                          // Add the fixed markup value as commission
            $markupValue = $airlineDetails['markup']; // Fixed markup value

            // Add commission to the price array
            $flightOffer['price']['commission'] = $markupValue;
                    }
                }
            }


            $categorizedData = [
                'tripType' => $this->tripType,
                'meta' => $data['meta'] ?? [],
                'flightOffers' =>      $flightOffers,
                'dictionaries' =>   $dictionaries,
                'passengers' => $validatedData['passengers'],
                // 'fromCityName' => $this->fromDisplayName,
                // 'toCityName' => $this->toDisplayName,
                'departureDate' => $this->departureDate,
                'returnDate' => $this->returnDate,
            ];
                   $filePath = 'search_result_new.json'; 

             Storage::put($filePath, json_encode($categorizedData, JSON_PRETTY_PRINT));

    //    dd($flightOffers);
       

            // Add categorized data to session
            session()->put('search_results', $categorizedData);
            return redirect()->route('search.results');
          
        }catch (\Illuminate\Validation\ValidationException $e) {
            // dd( $e->errors() );
            session()->flash('errors', $e->errors());
        }
        catch (\GuzzleHttp\Exception\RequestException $e) {
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
        }
        
        catch (\Exception $e) {
            // dd( $e->getMessage() );
            session()->flash('error', 'An error occurred while searching for flights: ' . $e->getMessage());
        }
    }

   // Assuming you have an Airline model



   public function getCityNameByIata($iataCode) {
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

   public function getAirlineDetailsByCode($carrierCode) {
    // Fetch airline details from the database
    $airline = Airline::where('airline_code', $carrierCode)->first();
    if ($airline) {
        return [
            'name' => $airline->airline_name,
            'logo' => $airline->logo ?? null, // Ensure there's a fallback if no logo exists
            'markup' => $airline->markup ?? 0,
        ];
    }

    return [
        'name' => 'Unknown',
        'logo' => null,
        'markup' => 0,
    ];
}

    // public function getCityNameByIata($iataCode, $dictionaries) {
    //     if (isset($dictionaries['locations']) && isset($dictionaries['locations'][$iataCode])) {
    //         $location = $dictionaries['locations'][$iataCode];
    //         return $location['cityCode'] ?? 'Unknown'; // Fallback to 'Unknown' if cityCode is missing.
    //     }
    //     return 'Unknown';
    // }

    public function render()
    {
        return view('livewire.flight.search-flight-page');
    }
}

