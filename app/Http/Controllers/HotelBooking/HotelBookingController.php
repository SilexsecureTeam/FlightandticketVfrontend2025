<?php

namespace App\Http\Controllers\HotelBooking;

use App\Http\Controllers\Controller;
use App\Models\Hotel\HotelOrder;
use App\Traits\AmadeusApiTrait;
use App\Traits\HttpResponses;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class HotelBookingController extends Controller
{
    use HttpResponses, AmadeusApiTrait;

    public function bookHotel(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'hotelOfferId' => 'required|string',
                'guests' => 'required|array|min:1',
                'guests.*.tid' => 'required|integer',
                'guests.*.title' => 'required|string|in:MR,MRS,MS,DR',
                'guests.*.firstName' => 'required|string',
                'guests.*.lastName' => 'required|string',
                'guests.*.phone' => 'required|string',
                'guests.*.email' => 'required|email',
            ]);

            // Define travelAgent and payment data in backend
            $travelAgent = [
                'contact' => [
                    'email' => 'travel.agent@email.com', // Backend-defined email
                ]
            ];

            $payment = [
                'method' => 'CREDIT_CARD',
                'paymentCard' => [
                    'paymentCardInfo' => [
                        'vendorCode' => 'VI', // Example vendor code for Visa
                        'cardNumber' => '4151289722471370', // Backend-defined card number
                        'expiryDate' => '2026-08', // Backend-defined expiry date
                        'holderName' => 'TRAVEL AGENT NAME', // Backend-defined cardholder name
                    ]
                ]
            ];

            // Prepare API request body
            $body = [
                'data' => [
                    'type' => 'hotel-order',
                    'guests' => $validatedData['guests'],
                    'travelAgent' => $travelAgent,
                    'roomAssociations' => [
                        [
                            'guestReferences' => collect($validatedData['guests'])->map(function ($guest) {
                                return ['guestReference' => (string)$guest['tid']];
                            })->toArray(),
                            'hotelOfferId' => $validatedData['hotelOfferId'],
                        ]
                    ],
                    'payment' => $payment,
                ]
            ];

            // Fetch access token using AmadeusApiTrait
            $accessToken = $this->getAccessToken();

            // Initialize HTTP client
            $client = new Client();

            // Make POST request to Amadeus Hotel Orders API
            $response = $client->request('POST', 'https://test.api.amadeus.com/v2/booking/hotel-orders', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
            ]);

            // Decode the response
            $hotelData = json_decode($response->getBody()->getContents(), true);

            $HotelOrder =  HotelOrder::create([
                'hotel_id' => $hotelData['data']['id'],
                'hotel_offers' =>  $hotelData,
                'guest' => $validatedData['guests'],
                'payment_status' => 'PENDING',
                'order_status' => 'PROCESSING',
            ]);


            return $this->success( $hotelData['data'], 'Hotel booked successfully');

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Parse the Amadeus API error response
            $response = $e->getResponse();
            $errorBody = json_decode($response->getBody()->getContents(), true);

            $errorDetails = $errorBody['errors'][0] ?? null;
            $errorMessage = $errorDetails['title'] ?? 'An error occurred';
            $errorDescription = $errorDetails['detail'] ?? 'No additional details available';

            return $this->error([
                'error_code' => $errorDetails['code'] ?? 'UNKNOWN_ERROR',
                'error_status' => $errorDetails['status'] ?? 400,
                'error_description' => $errorDescription,
            ], $errorMessage, $errorDetails['status'] ?? 400);

        } catch (\Exception $e) {
            // General error handling
            return $this->error(null, 'Error booking hotel: ' . $e->getMessage(), 500);
        }
    }
}
