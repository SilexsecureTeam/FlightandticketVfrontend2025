<?php

namespace App\Livewire\Booking;

use App\Http\Controllers\Payment\PaystackController;
use App\Mail\FlightBookingMail;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight\FlightOrder;
use App\Models\Markup;
use App\Traits\AmadeusApiTrait;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\json;

class BookingPage extends Component
{
    use AmadeusApiTrait,  LivewireAlert;
    public $selectedFlight;
    public $flight_offer;
    public $passengerTypes = [];
    public $flight_offer_price;
    public $flight_offer_booking;
    public $pnr;
    public $travelers = [];
    public $passengers = [];
    public $orderResponse;

    public $markUp;
    public $paystackUrl;

    protected $rules = [
        'travelers' => 'required|array|min:1',
        'travelers.*.id' => 'required|string',
        'travelers.*.dateOfBirth' => 'required|date|before:today',
        'travelers.*.name' => 'required|array',
        'travelers.*.name.firstName' => 'required|string|max:255',
        'travelers.*.name.middleName' => 'nullable|string|max:255',
        'travelers.*.name.lastName' => 'required|string|max:255',
        'travelers.*.gender' => 'required|in:MALE,FEMALE,OTHER',
        'travelers.*.contact' => 'required|array',
        'travelers.*.contact.emailAddress' => 'required|email',
        'travelers.*.contact.phones' => 'required|array|min:1',
        'travelers.*.contact.phones.*.deviceType' => 'required|string|in:MOBILE,LANDLINE',
        'travelers.*.contact.phones.*.countryCallingCode' => 'required|string',
        'travelers.*.contact.phones.*.number' => 'required|string|min:10',
        'travelers.*.documents' => 'required|array|min:1',
        'travelers.*.documents.*.documentType' => 'required|string|in:PASSPORT,ID_CARD',
        'travelers.*.documents.*.birthPlace' => 'required|string|max:255',
        'travelers.*.documents.*.issuanceLocation' => 'required|string|max:255',
        'travelers.*.documents.*.issuanceDate' => 'required|date|before:today',
        'travelers.*.documents.*.number' => 'required|string|max:255',
        'travelers.*.documents.*.expiryDate' => 'required|date|after:today',
        'travelers.*.documents.*.issuanceCountry' => 'required|string',
        'travelers.*.documents.*.validityCountry' => 'required|string',
        'travelers.*.documents.*.nationality' => 'required|string',
        'travelers.*.documents.*.holder' => 'required|boolean',
    ];

    protected $messages = [
        'travelers.*.dateOfBirth.before' => 'Date of birth must be in the past',
        'travelers.*.name.firstName.required' => 'First name is required',
        'travelers.*.name.lastName.required' => 'Last name is required',
        'travelers.*.gender.in' => 'Gender must be MALE, FEMALE',
        'travelers.*.contact.emailAddress.email' => 'Please enter a valid email address',
        'travelers.*.contact.phones.*.number.min' => 'Phone number must be at least 10 digits',
        'travelers.*.documents.*.documentType.in' => 'Document type must be either PASSPORT or ID_CARD',
        'travelers.*.documents.*.expiryDate.after' => 'Document expiry date must be in the future',
        'travelers.*.documents.*.issuanceCountry.size' => 'Country code must be 2 characters',
    ];


    public function clearError()
    {
        session()->forget('error_details');
    }

    public function mount()
    {

        $this->selectedFlight = session('selected_flight', []);
        // dd( $this->selectedFlight['price']['commission']);

        $markup = Markup::first(); // Get a single record

        if ($markup) {
            $this->markUp = $markup->mark_up;
        } else {
            $this->markUp = 0; // Default value if no markup exists
        }
        if (empty($this->selectedFlight)) {
            return redirect()->to('/');
        }

        try {

            $this->extractTravelerTypes();
            $this->initializePassengers();
            $this->initializeTravelers();
            $this->getFlightPricing();
            // $this->dispatch('getPricing');
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to initialize booking: ' . $e->getMessage());
            return redirect()->route('/');
        }

    }


    public function extractTravelerTypes()
    {
        // Loop through the travelerPricings to get the travelerType
        if (isset($this->selectedFlight['travelerPricings'])) {
            foreach ($this->selectedFlight['travelerPricings'] as $travelerPricing) {
                // Get travelerType (ADULT, CHILD, etc.)
                $this->passengerTypes[] = $travelerPricing['travelerType'];
            }
        }
        // dd($this->passengerTypes);

    }

    public function initializePassengers()
    {
        // Create an array for each passenger type
        foreach ($this->passengerTypes as $type) {
            $this->passengers[] = ['type' => $type, 'isOpen' => true];
        }
    }

    public function initializeTravelers()
    {
        $this->travelers = collect($this->passengerTypes)->map(function ($type, $index) {
            return [
                'id' => (string)($index + 1),
                'type' => $type,
                'dateOfBirth' => '',
                'name' => [
                    'firstName' => '',
                    'middleName' => '',
                    'lastName' => ''
                ],
                'gender' => 'MALE',
                'contact' => [
                    'emailAddress' => $index === 0 ? '' : ($this->travelers[0]['contact']['emailAddress'] ?? ''),
                    'phones' => [
                        [
                            'deviceType' => 'MOBILE',
                            'countryCallingCode' => '234', // Default to Nigeria
                            'number' => $index === 0 ? '' : ($this->travelers[0]['contact']['phones'][0]['number'] ?? ''),
                        ]
                    ]
                ],
                'documents' => [
                    [
                        'documentType' => 'PASSPORT',
                        'birthPlace' => 'NG',
                        'issuanceLocation' => 'NG',
                        'issuanceDate' => '',
                        'number' => '',
                        'expiryDate' => '',
                        'issuanceCountry' => 'NG',
                        'validityCountry' => 'NG',
                        'nationality' => 'NG',
                        'holder' => true
                    ]
                ],
                'isOpen' => true
            ];
        })->toArray();
    }

    #[On('syncContactInfo')]
    public function syncContactInfo()
    {

        foreach ($this->travelers as $index => $traveler) {
            if ($index > 0) {
                $this->travelers[$index]['contact']['emailAddress'] = $this->travelers[0]['contact']['emailAddress'];
                $this->travelers[$index]['contact']['phones'][0]['number'] = $this->travelers[0]['contact']['phones'][0]['number'];
            }
        }
    }


    public function toggleTraveler($index)
    {
        $this->travelers[$index]['isOpen'] = !$this->travelers[$index]['isOpen'];
    }


    public function validateTravelers()
    {

        $this->validate();

        foreach ($this->travelers as $index => $traveler) {
            $birthDate = Carbon::parse($traveler['dateOfBirth']);
            $age = $birthDate->age;
            $type = $traveler['type'];

            if ($type === 'ADULT' && $age < 12) {
                throw ValidationException::withMessages([
                    "travelers.$index.dateOfBirth" => "Adults must be 12 years or older.",
                ]);
            } elseif ($type === 'CHILD' && ($age < 2 || $age > 10)) {
                throw ValidationException::withMessages([
                    "travelers.$index.dateOfBirth" => "Children must be between 2 and 10 years old.",
                ]);
            } elseif ($type === 'HELD_INFANT' && $age >= 2) {
                throw ValidationException::withMessages([
                    "travelers.$index.dateOfBirth" => "Held infants must be under 2 years old.",
                ]);
            }
        }
    }

    public function createFlightOrder()
    {


        // // dd('Travelers Data:', $this->travelers);
        $this->validateTravelers();
        // dd($validatedData['travelers']);


        // // Debug the validated data
        // dd('Validated Data:', $validatedData);
        try {

            // $flightOrderPayload = $this->prepareFlightOrderPayload($this->travelers);
            // $orderResponse = $this->sendFlightOrderRequest($flightOrderPayload);
            // $order = $this->saveFlightOrder($orderResponse, $this->travelers);
            // $this->initializePayment($order, $this->travelers);

            $flightOrderPayload = $this->prepareFlightOrderPayload();
            // return dd(json_encode($flightOrderPayload));
            $orderResponse = $this->sendFlightOrderRequest($flightOrderPayload);
            // return dd($orderResponse);

            $order = $this->saveFlightOrder($orderResponse);

            $this->initializePayment($order);
        } catch (ValidationException $e) {
            // $this->alert('error', 'Please check the form for errors.'. $e->getMessage() );

            $this->alert('error', 'Validation Error: ' .  $e->getMessage(), [
                'position' => 'bottom-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to create flight order: ' .  $e->getMessage(), [
                'position' => 'bottom-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        }
    }

    private function prepareFlightOrderPayload(): array
    {
        // Clean up travelers array to handle optional middleName
        $cleanedTravelers = collect($this->travelers)->map(function ($traveler) {
            // Clean up name array
            if (isset($traveler['name'])) {
                // Combine firstName and middleName if middleName exists
                if (!empty($traveler['name']['middleName'])) {
                    $traveler['name']['firstName'] = $traveler['name']['firstName'] . ' ' . $traveler['name']['middleName'];
                }
                // Remove middleName as it's now combined with firstName
                unset($traveler['name']['middleName']);
            }
            return $traveler;
        })->toArray();

        // return dd($this->flight_offer_booking);

        return [
            'data' => [
                'type' => 'flight-order',
                'flightOffers' => [$this->flight_offer_booking],
                'travelers' => $cleanedTravelers,
                'remarks' => [
                    'general' => [
                        [
                            'subType' => 'GENERAL_MISCELLANEOUS',
                            'text' => 'ONLINE BOOKING FROM MYMORE',
                        ]
                    ]
                ],
                'ticketingAgreement' => [
                    'option' => 'DELAY_TO_CANCEL',
                    'delay' => '3D',
                ],
                'contacts' => [
                    [
                        'addresseeName' => [
                            'firstName' => 'PABLO',
                            'lastName' => 'RODRIGUEZ',
                        ],
                        'companyName' => 'INCREIBLE VIAJES',
                        'purpose' => 'STANDARD',
                        'phones' => [
                            [
                                'deviceType' => 'LANDLINE',
                                'countryCallingCode' => '34',
                                'number' => '480080071',
                            ],
                            [
                                'deviceType' => 'MOBILE',
                                'countryCallingCode' => '33',
                                'number' => '480080072',
                            ]
                        ],
                        'emailAddress' => 'support@increibleviajes.es',
                        'address' => [
                            'lines' => ['Calle Prado, 16'],
                            'postalCode' => '28014',
                            'cityName' => 'Madrid',
                            'countryCode' => 'ES',
                        ]
                    ]
                ]
            ]
        ];
    }

    private function sendFlightOrderRequest(array $payload): array
    {

        try {
            $baseUrl = config('services.amadeus.base_url');
            $clientRef = session('clientRef');

            // return dd(json_encode($payload));
            $client = new Client();
            $response = $client->request('POST', "$baseUrl/v1/booking/flight-orders", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getAccessToken(),
                    'Content-Type' => 'application/json',
                    'ama-client-ref' => $clientRef,
                ],
                'json' => $payload,
            ]);
            // Get full path for downloading
            $filePath = 'flight_result_payload.json';
            $data = json_decode($response->getBody()->getContents(), true);
            Storage::put($filePath, json_encode($data, JSON_PRETTY_PRINT));

            $travelers = $data['data']['travelers'];
            $firstTraveler = $travelers[0];
            $email = $firstTraveler['contact']['emailAddress'] ?? null;
            $this->pnr = $data['data']['associatedRecords'][0]['reference'] ?? null;
            $flightOffer = $data['data']['flightOffers'][0] ?? null;

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
                        'markup' => $airlineDetails['markup'],
                    ];
                }
            }


            $bookingData = [
                'travelers' => $travelers, // Travelers array
                'pnr' => $this->pnr,
                'itineraries' => $flightOffer['itineraries'] ?? [], // Fix itineraries key
                'price' => $flightOffer['price'] ?? [],
                'commission' => $this->selectedFlight['price']['commission'] ?? 0,
                'travelType' => $flightOffer['travelerPricings'] ?? [],
                'remarks' => $data['data']['remarks']['general'] ?? [],
            ];


            // Pass this $bookingData to your email template


            session()->put('booking_details', [
                'data' => $bookingData,
                'email' => $email,
            ]);

            Mail::to($email)->send(new FlightBookingMail($bookingData));


            return $data;
        } catch (\Exception $e) {
            // Get the response from the exception
            $message = $e->getMessage();

            // If it's a GuzzleHttp exception, we can get the full response
            if ($e instanceof \GuzzleHttp\Exception\ClientException) {
                $response = $e->getResponse();
                $fullBody = $response->getBody()->getContents();
                $decodedBody = json_decode($fullBody, true);
                // Print full formatted error
                session()->put('error_details', [
                    'errors' => $decodedBody['errors'] ?? [],
                    'status_code' => $response->getStatusCode(),
                    'message' => $message,
                ]);


                // Flash the error message to the user
                $this->alert('error', 'An error occurred: ' . ($decodedBody['errors'][0]['detail'] ?? 'Invalid data'), [
                    'position' => 'bottom-end',
                    'timer' => 10000, // 10 seconds
                    'toast' => true,
                ]);
            } else {
                // For other exceptions, dump the full message
                session()->put('error_details', [
                    'error_message' => $message,
                    'error_type' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }

            // $this->alert('error', 'Failed to create flight order: ' . $message, [
            //     'position' => 'bottom-end',
            //     'timer' => 3000,
            //     'toast' => true,
            // ]);
            return [];
        }
    }


    private function saveFlightOrder(array $orderResponse): FlightOrder
    {

        try {

            $Flight =    FlightOrder::create([
                'order_id' => $orderResponse['data']['id'],
                'pnr' => $this->pnr,
                'flight_offers' => $this->flight_offer_booking,
                'travelers' => $this->travelers,
                'payment_status' => 'PENDING',
                'order_status' => 'PROCESSING',
            ]);
            $this->orderResponse =  $Flight->id;
            session()->put('orderResponse', $this->orderResponse);
            return $Flight;
        } catch (\Illuminate\Database\QueryException $e) {

            $this->alert('error', 'Database error while saving your order: ' . $e->getMessage(), [
                'position' => 'center',
                'timer' => 3000,
                'toast' => true,
            ]);
            throw $e; // Re-throw the exception after alerting

        } catch (\Exception $e) {
            $this->alert('error', 'Failed to save your flight order: ' . $e->getMessage(), [
                'position' => 'center',
                'timer' => 3000,
                'toast' => true,
            ]);
            throw $e; // Re-throw the exception after alerting
        }
    }


    private function initializePayment(FlightOrder $order)
    {
        try {
            $type = "Flight";

            $amount = $this->flight_offer_price['grandTotal'];

            // Initialize payment via PaystackController
            $paystackResponse = app(PaystackController::class)
                ->initializePayment($order->id, $this->travelers[0]['contact']['emailAddress'], $amount, $type);
            $paymentResponseData = json_decode($paystackResponse->getContent(), true);


            if (isset($paymentResponseData['paystackURL'])) {
                $this->paystackUrl = $paymentResponseData['paystackURL'];

                // Set session values
                session()->put('paystackUrl', $this->paystackUrl);
                session()->put('payment_information', $this->flight_offer_price);




                // Ensure session data is set correctly
                if (!session()->has('paystackUrl') || !session()->has('payment_information')) {
                    $this->alert('error', 'Failed to set session data. Please try again.', [
                        'position' => 'center',
                        'timer' => 8000,
                        'toast' => true,
                    ]);
                    return;
                }

                return redirect()->route('booking.payment');
            }

            throw new \Exception('Failed to initialize payment');
        } catch (\Exception $e) {
            // Handle error with alert
            $this->alert('error', 'Database error while saving your order: ' . $e->getMessage(), [
                'position' => 'center',
                'timer' => 8000,
                'toast' => true,
            ]);
            return;
        }
    }



    #[On('getPricing')]
    public function getFlightPricing()
    {
        // return dd($this->selectedFlight);
        try {
            // Structure the request payload
            $payload = [
                'data' => [
                    'type' => 'flight-offers-pricing',
                    'flightOffers' => [$this->selectedFlight],
                ]
            ];
            //  dd( $payload);
            $clientRef = session('clientRef');

            //   dd( $clientRef);
            // Initialize the HTTP client
            $client = new Client();

            $headers = [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
                'ama-client-ref' => $clientRef,
            ];
            $baseUrl = config('services.amadeus.base_url');

            $includes = (array) session('selected_includes', []);
            $includes = array_filter($includes, function ($value) {
                return !empty(trim($value));
            });


            $includeQuery = !empty($includes) ? '?include=' . implode(',', $includes) : '';

            $response = $client->request('POST', "$baseUrl/v1/shopping/flight-offers/pricing" . ($includeQuery ? $includeQuery : ''), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getAccessToken(),
                    'Content-Type' => 'application/json',
                    'ama-client-ref' => $clientRef,
                ],
                'json' => $payload,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            $flightOffer = $data['data']['flightOffers'][0];

            // Fetch airline details and calculate commission
            $carrierCode = $flightOffer['itineraries'][0]['segments'][0]['carrierCode'];
            $airlineDetails = $this->getAirlineDetailsByCode($carrierCode);
            $markupValue = $airlineDetails['markup'] ?? 0; // Fixed markup value


            $price = $flightOffer['price'];
            $price['commission'] = $markupValue;


            $this->flight_offer_price = $price;
            $this->flight_offer_booking = $flightOffer;
            // dd( $this->flight_offer_price );


        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Capture the full error response for more information

            $errorMessage = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            // dd($errorMessage);

        }
    }


    public function getAirlineDetailsByCode($carrierCode)
    {
        // Fetch airline details from the database
        $airline = Airline::where('airline_code', $carrierCode)->first();
        // dd( $airline);
        if ($airline) {
            return [
                'name' => $airline->airline_name,
                'logo' => $airline->logo ?? null,
                'markup' => $airline->markup ?? 0,
            ];
        }

        return [
            'name' => 'Unknown',
            'logo' => null,
            'markup' => 0,
        ];
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



    public function render()
    {
        return view('livewire.booking.booking-page', [
            'passengers' => $this->passengers,
        ]);
    }
}
