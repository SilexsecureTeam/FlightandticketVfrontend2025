<?php

namespace App\Http\Controllers\HotelBooking;

use App\Http\Controllers\Controller;
use App\Traits\AmadeusApiTrait;
use App\Traits\HttpResponses;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class OfferSearchController extends Controller
{
    use HttpResponses, AmadeusApiTrait;
    public function getHotelOfferDetails(Request $request, $hotelOfferId)
    {
        try {
            // Validate the hotelOfferId parameter
          

            // Fetch access token using AmadeusApiTrait
            $accessToken = $this->getAccessToken();

            // Initialize HTTP client
            $client = new Client();

            // Make GET request to Amadeus Hotel Offer Details API
            $response = $client->request('GET', "https://test.api.amadeus.com/v3/shopping/hotel-offers/{$hotelOfferId}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Decode the response
            $data = json_decode($response->getBody()->getContents(), true);

            return $this->success($data, 'Hotel offer details retrieved successfully');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle client errors (e.g., 4xx responses)
            $response = $e->getResponse();
            $responseBody = json_decode($response->getBody()->getContents(), true);

            return $this->error(
                $responseBody['errors'] ?? null,
                'Client error: ' . ($responseBody['errors'][0]['detail'] ?? 'An error occurred.'),
                $response->getStatusCode()
            );
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Handle server errors (e.g., 5xx responses)
            $response = $e->getResponse();
            $responseBody = json_decode($response->getBody()->getContents(), true);

            return $this->error(
                $responseBody['errors'] ?? null,
                'Server error: ' . ($responseBody['errors'][0]['detail'] ?? 'An error occurred.'),
                $response->getStatusCode()
            );
        } catch (\Exception $e) {
            // Handle other exceptions
            return $this->error(
                null,
                'Error retrieving hotel offer details: ' . $e->getMessage(),
                500
            );
        }
    }

}
