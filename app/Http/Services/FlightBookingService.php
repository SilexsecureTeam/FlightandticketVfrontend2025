<?php

namespace App\Http\Services;

use App\Http\Controllers\Payment\PaystackController;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight\FlightOrder;
use App\Traits\AmadeusApiTrait;
use App\Mail\FlightBookingMail;
use App\Mail\OfflineLineMail;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FlightBookingService
{
    use AmadeusApiTrait;

    /**
     * Validate travelers' information based on their type and age.
     *
     * @param array $travelers
     * @throws ValidationException
     */
    public function validateTravelers(array $travelers)
    {
        foreach ($travelers as $index => $traveler) {
            $birthDate = Carbon::parse($traveler['dateOfBirth']);
            $age = $birthDate->age;
            $type = $traveler['type'];

            if ($type === 'ADULT' && $age < 12) {
                throw ValidationException::withMessages([
                    "travelers.$index.dateOfBirth" => "Adults must be 12 years or older.",
                ]);
            } elseif ($type === 'CHILD' && ($age < 2 || $age > 12)) {
                throw ValidationException::withMessages([
                    "travelers.$index.dateOfBirth" => "Children must be between 2 and 12 years old.",
                ]);
            } elseif ($type === 'HELD_INFANT' && $age >= 2) {
                throw ValidationException::withMessages([
                    "travelers.$index.dateOfBirth" => "Held infants must be under 2 years old.",
                ]);
            }
        }
    }

    /**
     * Find a flight offer from stored search results.
     */
    public function findFlightOffer(string $searchId, string $offerId): ?array
    {
        $filePath = 'flight_searches/' . $searchId . '_offers.json';
        if (!Storage::exists($filePath)) {
            return null;
        }

        $offers = json_decode(Storage::get($filePath), true);
        return collect($offers)->firstWhere('id', $offerId);
    }

    /**
     * Get final pricing for a flight offer from Amadeus.
     */
    public function getFlightPricing(array $flightOffer, ?string $clientRef): array
    {
        $baseUrl = config('services.amadeus.base_url');
        $client = new Client();

        $payload = [
            'data' => [
                'type' => 'flight-offers-pricing',
                'flightOffers' => [$flightOffer],
            ]
        ];

        $response = $client->request('POST', "$baseUrl/v1/shopping/flight-offers/pricing", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
                'ama-client-ref' => $clientRef,
            ],
            'json' => $payload,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['data']['flightOffers'][0] ?? $flightOffer;
    }

    /**
     * Prepare the flight order payload for Amadeus API.
     *
     * @param array $travelers
     * @param array $flightOffer
     * @return array
     */
    public function prepareFlightOrderPayload(array $travelers, array $flightOffer, bool $omitTravelerDocuments = false): array
    {
        $cleanedTravelers = collect($travelers)->map(function ($traveler) use ($omitTravelerDocuments) {
            if (isset($traveler['name'])) {
                if (!empty($traveler['name']['middleName'])) {
                    $traveler['name']['firstName'] = $traveler['name']['firstName'] . ' ' . $traveler['name']['middleName'];
                }
                unset($traveler['name']['middleName']);
            }
            if ($omitTravelerDocuments && isset($traveler['documents'])) {
                unset($traveler['documents']);
            }
            $traveler['gender'] = 'UNSPECIFIED';
            return $traveler;
        })->toArray();

        return [
            'data' => [
                'type' => 'flight-order',
                'flightOffers' => [$flightOffer],
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
            ]
        ];
    }

    /**
     * Send flight order request to Amadeus API.
     *
     * @param array $payload
     * @param string|null $clientRef
     * @return array
     * @throws \Exception
     */
    public function sendFlightOrderRequest(array $payload, ?string $clientRef): array
    {
        $baseUrl = config('services.amadeus.base_url');
        $client = new Client();

        $response = $client->request('POST', "$baseUrl/v1/booking/flight-orders", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
                'ama-client-ref' => $clientRef,
            ],
            'json' => $payload,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        // Save payload for debugging
        Storage::put('flight_result_payload.json', json_encode($data, JSON_PRETTY_PRINT));

        // Enrich response with city and airline details
        if (isset($data['data']['flightOffers'][0])) {
            $flightOffer = &$data['data']['flightOffers'][0];
            foreach ($flightOffer['itineraries'] as &$itinerary) {
                foreach ($itinerary['segments'] as &$segment) {
                    $departureDetails = $this->getCityNameByIata($segment['departure']['iataCode']);
                    $segment['departure'] = array_merge($segment['departure'], $departureDetails);

                    $arrivalDetails = $this->getCityNameByIata($segment['arrival']['iataCode']);
                    $segment['arrival'] = array_merge($segment['arrival'], $arrivalDetails);

                    $airlineDetails = $this->getAirlineDetailsByCode($segment['carrierCode']);
                    $segment['airline'] = [
                        'name' => $airlineDetails['name'],
                        'logo' => $airlineDetails['logo'],
                        'markup' => $airlineDetails['markup'],
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * Save flight order to database.
     *
     * @param array $orderResponse
     * @param array $flightOffer
     * @param array $travelers
     * @param string|null $pnr
     * @return FlightOrder
     */
    public function saveFlightOrder(array $orderResponse, array $flightOffer, array $travelers, ?string $pnr): FlightOrder
    {
        return FlightOrder::create([
            'order_id' => $orderResponse['data']['id'] ?? null,
            'pnr' => $pnr,
            'flight_offers' => $flightOffer,
            'travelers' => $travelers,
            'payment_status' => 'PENDING',
            'order_status' => 'PROCESSING',
        ]);
    }

    /**
     * Initialize payment for the flight order.
     *
     * @param FlightOrder $order
     * @param string $email
     * @param float $amount
     * @return array
     * @throws \Exception
     */
    public function initializePayment(FlightOrder $order, string $email, float $amount): array
    {
        $paystackController = app(PaystackController::class);
        $alatpayController = app(\App\Http\Controllers\Payment\AlatPayController::class);

        $paystackResponse = $paystackController->initializePayment($order->id, $email, $amount, 'Flight');
        $alatpayResponse = $alatpayController->initializePayment($order->id, $email, $amount, 'Flight');

        $paystackData = json_decode($paystackResponse->getContent(), true);
        $alatpayData = json_decode($alatpayResponse->getContent(), true);

        return [
            'paystack_url' => $paystackData['paystackURL'] ?? null,
            'alatpay_url' => $alatpayData['alatpayURL'] ?? null,
            'payment_ids' => [
                'paystack' => $paystackData['payment_id'] ?? null,
                'alatpay' => $alatpayData['payment_id'] ?? null,
            ],
            'status' => 'success'
        ];
    }

    /**
     * Initialize payment record for API bookings (Offline or Pre-approved).
     */
    public function createApiPaymentRecord(FlightOrder $order, float $amount, string $provider = 'Offline'): array
    {
        $timezone = 'Africa/Lagos';
        $reference = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(12));

        // If paystack or alatpay is used, mark as APPROVED
        $status = 'PENDING';
        if (in_array(strtolower($provider), ['paystack', 'alatpay', 'alat pay'])) {
            $status = 'APPROVED';
        }

        $payment = \App\Models\Payment\Payment::create([
            'flight_order_id' => $order->id,
            'email' => $order->travelers[0]['contact']['emailAddress'] ?? '',
            'amount' => $amount,
            'provider' => $provider,
            'payment_type' => 'Flight',
            'payment_status' => $status,
            'payment_date' => now($timezone)->format('Y-m-d H:i:s'),
            'reference' => $reference,
        ]);

        return [
            'payment_id' => $payment->id,
            'reference' => $reference,
            'status' => $status,
        ];
    }

    /**
     * Send booking confirmation emails.
     *
     * @param array $bookingData
     * @param string $email
     */
    public function sendConfirmationEmails(array $bookingData, string $email)
    {
        Mail::to($email)->send(new FlightBookingMail($bookingData));
        Mail::to("reservation@mymoretravel.com")->send(new FlightBookingMail($bookingData));
        Mail::to("csmymoretravels@gmail.com")->send(new FlightBookingMail($bookingData));
    }

    /**
     * Send offline (reserve) confirmation emails.
     */
    public function sendOfflineConfirmationEmails(array $bookingData, string $email)
    {
        Mail::to($email)->send(new OfflineLineMail($bookingData));
        Mail::to("reservation@mymoretravel.com")->send(new OfflineLineMail($bookingData));
        Mail::to("csmymoretravels@gmail.com")->send(new OfflineLineMail($bookingData));
    }

    /**
     * Helper to get airline details by code.
     */
    public function getAirlineDetailsByCode($carrierCode)
    {
        $airline = Airline::where('airline_code', $carrierCode)->first();
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

    /**
     * Helper to get city details by IATA code.
     */
    public function getCityNameByIata($iataCode)
    {
        $airport = Airport::where('airport_code', $iataCode)->first();
        if ($airport) {
            return [
                'city' => $airport->city ?? 'Unknown',
                'country' => $airport->country ?? 'Unknown',
                'airport' => $airport->airport ?? 'Unknown',
                'city_country' => $airport->city_country ?? 'Unknown',
            ];
        }

        return [
            'city' => 'Unknown',
            'country' => 'Unknown',
            'airport' => 'Unknown',
            'city_country' => 'Unknown',
        ];
    }
}
