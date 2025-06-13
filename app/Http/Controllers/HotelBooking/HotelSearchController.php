<?php

namespace App\Http\Controllers\HotelBooking;

use App\Http\Controllers\Controller;
use App\Traits\AmadeusApiTrait;
use App\Traits\HttpResponses;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class HotelSearchController extends Controller
{
    use HttpResponses, AmadeusApiTrait;



    public function getHotelOffers(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'hotelIds' => 'required|array|min:1',
                'hotelIds.*' => 'string|size:8',
                'adults' => 'nullable|integer|min:1|max:9',
                'checkInDate' => 'nullable|string',
                'checkOutDate' => 'nullable|string',
                'countryOfResidence' => 'nullable|string|size:2',
                'roomQuantity' => 'nullable|integer|min:1|max:9',
                'priceRange' => 'nullable|string|regex:/^\d+(-\d+)?$/',
                'currency' => 'nullable|string|size:3',
                'paymentPolicy' => 'nullable|string|in:GUARANTEE,DEPOSIT,NONE',
                'boardType' => 'nullable|string|in:ROOM_ONLY,BREAKFAST,HALF_BOARD,FULL_BOARD,ALL_INCLUSIVE',
                'includeClosed' => 'nullable|boolean',
                'bestRateOnly' => 'nullable|boolean',
                'lang' => 'nullable|string|min:2|max:5',
            ]);

            // Prepare query parameters
            $params = array_filter([
                'hotelIds' => implode(',', $validatedData['hotelIds']),
                'adults' => $validatedData['adults'] ?? 1,
                'checkInDate' => $validatedData['checkInDate'] ?? null,
                'checkOutDate' => $validatedData['checkOutDate'] ?? null,
                'countryOfResidence' => $validatedData['countryOfResidence'] ?? null,
                'roomQuantity' => $validatedData['roomQuantity'] ?? 1,
                'priceRange' => $validatedData['priceRange'] ?? null,
                'currency' => $validatedData['currency'] ?? 'USD',
                'paymentPolicy' => $validatedData['paymentPolicy'] ?? null,
                'boardType' => $validatedData['boardType'] ?? null,
                'includeClosed' => $validatedData['includeClosed'] ?? false,
                'bestRateOnly' => $validatedData['bestRateOnly'] ?? true,
                'lang' => $validatedData['lang'] ?? 'en',
            ]);

            // Fetch access token
            $accessToken = $this->getAccessToken();

            // Initialize HTTP client
            $client = new Client();

            // Make API request
            $response = $client->request('GET', 'https://test.api.amadeus.com/v3/shopping/hotel-offers', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => $params,
            ]);

            // Decode response data
            $data = json_decode($response->getBody()->getContents(), true);

            return $this->success($data['data'], 'Hotel offers retrieved successfully');

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
            return $this->error(null, 'Error retrieving hotel offers: ' . $e->getMessage(), 500);
        }
    }
}


    // public function getHotelOffers(Request $request)
    // {
    //     try {
    //         // Validate input
    //         $validatedData = $request->validate([
    //             'hotelIds' => 'required|array|min:1', // List of hotel IDs (e.g., ["MCLONGHM"])
    //             'hotelIds.*' => 'string|size:8', // Each hotel ID is 8 chars long
    //             'adults' => 'nullable|integer|min:1|max:9', // Number of adults per room
    //             'checkInDate' => 'nullable|string', // Check-in date (>= today)
    //             'checkOutDate' => 'nullable|string', // Check-out date (> checkInDate)
    //             'countryOfResidence' => 'nullable|string|size:2', // ISO 3166-1 country code
    //             'roomQuantity' => 'nullable|integer|min:1|max:9', // Number of rooms
    //             'priceRange' => 'nullable|string|regex:/^\d+(-\d+)?$/', // e.g., "100-200" or "300-"
    //             'currency' => 'nullable|string|size:3', // ISO currency code (e.g., USD)
    //             'paymentPolicy' => 'nullable|string|in:GUARANTEE,DEPOSIT,NONE', // Payment type filter
    //             'boardType' => 'nullable|string|in:ROOM_ONLY,BREAKFAST,HALF_BOARD,FULL_BOARD,ALL_INCLUSIVE', // Meal type
    //             'includeClosed' => 'nullable|boolean', // Include sold-out properties
    //             'bestRateOnly' => 'nullable|boolean', // Only return the cheapest offer
    //             'lang' => 'nullable|string|min:2|max:5', // Language code (e.g., en, fr-FR)
    //         ]);

    //         // Prepare query parameters
    //         $params = [
    //             'hotelIds' => implode(',', $validatedData['hotelIds']),
    //             'adults' => $validatedData['adults'] ?? 1,
    //             'checkInDate' => $validatedData['checkInDate'] ?? null,
    //             'checkOutDate' => $validatedData['checkOutDate'] ?? null,
    //             'countryOfResidence' => $validatedData['countryOfResidence'] ?? null,
    //             'roomQuantity' => $validatedData['roomQuantity'] ?? 1,
    //             'priceRange' => $validatedData['priceRange'] ?? null,
    //             'currency' => $validatedData['currency'] ?? null,
    //             'paymentPolicy' => $validatedData['paymentPolicy'] ?? null,
    //             'boardType' => $validatedData['boardType'] ?? null,
    //             'includeClosed' => $validatedData['includeClosed'] ?? false,
    //             'bestRateOnly' => $validatedData['bestRateOnly'] ?? true,
    //             'lang' => $validatedData['lang'] ?? 'en',
    //         ];

    //         // Remove null values
    //         $params = array_filter($params, fn($value) => $value !== null);

    //         // Fetch access token using AmadeusApiTrait
    //         $accessToken = $this->getAccessToken();

    //         // Initialize HTTP client
    //         $client = new Client();

    //         // Make GET request to Amadeus Hotel Offers Search API
    //         $response = $client->request('GET', 'https://test.api.amadeus.com/v3/shopping/hotel-offers', [
    //             'headers' => [
    //                 'Authorization' => 'Bearer ' . $accessToken,
    //                 'Content-Type' => 'application/json',
    //             ],
    //             'query' => $params,
    //         ]);

    //         // Decode the response
    //         $data = json_decode($response->getBody()->getContents(), true);

    //         return $this->success($data['data'], 'Hotel offers retrieved successfully');
    //     } catch (\Exception $e) {
    //         return $this->error(null, 'Error retrieving hotel offers: ' . $e->getMessage(), 500);
    //     }
    // }
// }
