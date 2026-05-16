<?php

namespace App\Livewire\Booking;

use App\Http\Controllers\Payment\PaystackController;
use App\Http\Services\GeneralService;
use App\Mail\FlightBookingMail;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight\FlightOrder;
use App\Models\Markup;
use App\Traits\AmadeusApiTrait;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Mail\WelcomeUserMail;
use App\Models\FlightSearch;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

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
    public $showToSource = 'web';
    public $markUp;
    public $paystackUrl;

    public $searchResultsId;

    /**
     * First itinerary: departure country and arrival country are both Nigeria (domestic).
     */
    public function isNigeriaDomesticFlight(): bool
    {
        $itineraries = $this->selectedFlight['itineraries'] ?? [];
        $segments = $itineraries[0]['segments'] ?? [];
        if ($segments === []) {
            return false;
        }

        $first = $segments[0];
        $last = $segments[count($segments) - 1];
        $from = strtolower((string) ($first['departure']['city_country'] ?? ''));
        $to = strtolower((string) ($last['arrival']['city_country'] ?? ''));

        return str_contains($from, 'nigeria') && str_contains($to, 'nigeria');
    }

    protected function rules(): array
    {
        $base = [
            'travelers' => 'required|array|min:1',
            'travelers.*.id' => 'required|string',
            'travelers.*.dateOfBirth' => 'required|date|before:today',
            'travelers.*.name' => 'required|array',
            'travelers.*.name.firstName' => 'required|string|max:255',
            'travelers.*.name.middleName' => 'nullable|string|max:255',
            'travelers.*.name.lastName' => 'required|string|max:255',
            'travelers.*.gender' => 'nullable|string',
            'travelers.*.contact' => 'required|array',
            'travelers.*.contact.emailAddress' => 'required|email',
            'travelers.*.contact.phones' => 'required|array|min:1',
            'travelers.*.contact.phones.*.deviceType' => 'required|string|in:MOBILE,LANDLINE',
            'travelers.*.contact.phones.*.countryCallingCode' => 'required|string',
            'travelers.*.contact.phones.*.number' => 'required|string|min:10',
        ];

        if ($this->isNigeriaDomesticFlight()) {
            return $base;
        }

        return array_merge($base, [
            'travelers.*.documents' => 'required|array|min:1',
            'travelers.*.documents.*.documentType' => 'required|string|in:PASSPORT,ID_CARD',
            'travelers.*.documents.*.birthPlace' => 'required|string|max:255',
            'travelers.*.documents.*.issuanceLocation' => 'required|string|max:255',
            'travelers.*.documents.*.number' => 'required|string|max:255',
            'travelers.*.documents.*.expiryDate' => 'required|date|after:today',
            'travelers.*.documents.*.issuanceCountry' => 'required|string',
            'travelers.*.documents.*.validityCountry' => 'required|string',
            'travelers.*.documents.*.nationality' => 'required|string',
            'travelers.*.documents.*.holder' => 'required|boolean',
        ]);
    }

    protected $messages = [
        'travelers.*.dateOfBirth.before' => 'Date of birth must be in the past',
        'travelers.*.name.firstName.required' => 'First name is required',
        'travelers.*.name.lastName.required' => 'Last name is required',
        // 'travelers.*.gender.in' => 'Gender requires',
        // 'travelers.*.title.in' => 'Title requires',
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
        $this->searchResultsId = session('searchResultsId');
        // dd( $this->selectedFlight['price']['commission']);
        $this->showToSource = session('showToSource') ?? "web";

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
            $this->dispatch('getPricing');
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
                'gender' => 'UNSPECIFIED',
                // 'title' => '',
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
                        // 'issuanceDate' => '',
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
        app(\App\Http\Services\FlightBookingService::class)->validateTravelers($this->travelers);
    }

    public function createFlightOrder()
    {
        $this->validateTravelers();

        try {
            $service = app(\App\Http\Services\FlightBookingService::class);

            $flightOrderPayload = $service->prepareFlightOrderPayload(
                $this->travelers,
                $this->flight_offer_booking,
                $this->isNigeriaDomesticFlight()
            );

            $orderResponse = $service->sendFlightOrderRequest($flightOrderPayload, session('clientRef'));

            $pnr = $orderResponse['data']['associatedRecords'][0]['reference'] ?? null;
            $order = $service->saveFlightOrder($orderResponse, $this->flight_offer_booking, $this->travelers, $pnr);

            // Special logic for web: set session and send emails
            $email = $this->travelers[0]['contact']['emailAddress'] ?? null;
            $bookingData = [
                'travelers' => $orderResponse['data']['travelers'],
                'pnr' => $pnr,
                'itineraries' => $orderResponse['data']['flightOffers'][0]['itineraries'] ?? [],
                'price' => $orderResponse['data']['flightOffers'][0]['price'] ?? [],
                'commission' => $this->selectedFlight['price']['commission'] ?? 0,
                'travelType' => $orderResponse['data']['flightOffers'][0]['travelerPricings'] ?? [],
                'remarks' => $orderResponse['data']['remarks']['general'] ?? [],
            ];

            session()->put('booking_details', [
                'data' => $bookingData,
                'email' => $email,
            ]);

            FlightOrder::query()
                ->where('id', '=', $order->id)
                ->update([
                    'amount' => $bookingData['price']['total'] + $bookingData['commission'],
                ]);

            $service->sendConfirmationEmails($bookingData, $email);

            $this->handleUserAccount($email, $orderResponse['data']['travelers'][0], $order, $this->searchResultsId);

            $this->initializePayment($order);
        } catch (ValidationException $e) {
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






    private function initializePayment(FlightOrder $order)
    {
        try {
            $service = app(\App\Http\Services\FlightBookingService::class);
            $amount = $this->flight_offer_price['grandTotal'];

            $paymentData = $service->initializePayment($order, $this->travelers[0]['contact']['emailAddress'], $amount);

            // $this->paystackUrl = $paymentData['paystackURL'];
            $this->paystackUrl = $paymentData['paystack_url'];

            // Set session values
            session()->put('paystackUrl', $this->paystackUrl);
            session()->put('orderResponse', $order->id);
            session()->put('payment_information', $this->flight_offer_price);
            session(['showToSource' => $this->showToSource]);

            return redirect()->route('booking.payment');
        } catch (\Exception $e) {
            $this->alert('error', 'Payment Error: ' . $e->getMessage(), [
                'position' => 'center',
                'timer' => 8000,
                'toast' => true,
            ]);
        }
    }



    #[On('getPricing')]
    public function getFlightPricing()
    {
        // dd($this->flight_offer);
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
            $markupValueSign = 0;
            if (count($this->travelers) == 1) {
                // Add the fixed markup value as commission
                // $markupValue = $airlineDetails['markup']; // Fixed markup value
                // $markupstatus = $airlineDetails['markstatus']; // Fixed markup value
                // // Add commission to the price array
                // $markupValueSign = $markupstatus == "markdown" ? -abs($markupValue) : +abs($markupValue);

                $dataClassCabin = (new GeneralService)->getCabinClassMarkprice($flightOffer['travelerPricings'][0]['fareDetailsBySegment'][0]['cabin'], $airlineDetails['id']);
                // Add the fixed markup value as commission
                $markupValue = $dataClassCabin['markprice']; // Fixed markup value
                $markupstatus = $dataClassCabin['markstatus']; // Fixed markup value
                // Add commission to the price array
                $markupValueSign = $markupstatus == "markdown" ? -abs($markupValue) : +abs($markupValue);
            }
            $price = $flightOffer['price'];
            $price['commission'] = $markupValueSign;
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
                'id' => $airline->id,
                'name' => $airline->airline_name,
                'logo' => $airline->logo ?? null,
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
    /**
     * Handle user account creation and linking
     */
    private function handleUserAccount($email, $traveler, $order, $searchId)
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                // Create user if not present
                $firstName = $traveler['name']['firstName'] ?? '';
                $lastName = $traveler['name']['lastName'] ?? '';
                $name = trim($firstName . ' ' . $lastName);
                $phone = $traveler['contact']['phones'][0]['number'] ?? null;

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt(Str::random(12)), // Random password
                    'phone' => $phone,
                    'status' => 'active', // Assuming default status
                ]);

                // Send welcome email
                Mail::to($email)->send(new WelcomeUserMail($name));
            }

            // Update flight order with user_id
            $order->update(['user_id' => $user->id]);

            // Update flight search with user_id
            $flightSearch = FlightSearch::where('search_id', $searchId)->first();
            if ($flightSearch) {
                $flightSearch->update(['user_id' => $user->id]);
            }
        } catch (\Exception $e) {
            Log::error('Error handling user account: ' . $e->getMessage());
            // We don't throw exception here to avoid breaking the booking flow if user creation fails
        }
    }
}
