<?php

namespace App\Http\Controllers\FlightBooking;

use App\Http\Controllers\Controller;
use App\Traits\AmadeusApiTrait;
use App\Traits\HttpResponses;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class GetPricingController extends Controller
{
    use HttpResponses, AmadeusApiTrait;

    public function getFlightPricing(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'flightOffers' => 'required|array', // Validate flightOffers as an array
            ]);

            // Structure the request payload
            $payload = [
                'data' => [
                    'type' => 'flight-offers-pricing',
                    'flightOffers' => $validatedData['flightOffers'],
                ]
            ];
             $clientRef = 'mymore-' . uniqid('', true);

            // Initialize the HTTP client
            $client = new Client();
            $baseUrl = config('services.amadeus.base_url');

            $response = $client->request('POST', "$baseUrl/v1/shopping/flight-offers/pricing", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getAccessToken(),
                    'Content-Type' => 'application/json',
                    'ama-client-ref' => $clientRef,
                ],
                'json' => $payload,
            ]);

         
            // Decode and return the response as a success
            $data = json_decode($response->getBody()->getContents(), true);
            return $this->success($data, 'Flight pricing retrieved successfully');
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Capture the full error response for more information
            $errorMessage = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            return $this->error(null, 'Error retrieving flight pricing: ' . $errorMessage, 500);
        }
    }
}
