<?php

namespace App\Http\Controllers\HotelBooking;

use App\Http\Controllers\Controller;
use App\Traits\AmadeusApiTrait;
use App\Traits\HttpResponses;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class HotelListController extends Controller
{
    use HttpResponses, AmadeusApiTrait;

    public function getHotelsByCity(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'cityCode' => 'required|string|size:3', // City IATA code (e.g., PAR for Paris)
                'radius' => 'nullable|integer|min:1|max:50', // Radius in km, max 50 km
                'radiusUnit' => 'nullable|string|in:KM,MILE', // Unit for radius: KM or MILE
                'chainCodes' => 'nullable|array', // List of hotel chain codes
                'amenities' => 'nullable|array', // List of amenities
                'ratings' => 'nullable|array', // List of hotel ratings (1-5 stars)
                'hotelSource' => 'nullable|string|in:BEDBANK,DIRECTCHAIN,ALL', // Source type
            ]);

            // Prepare the query parameters
            $params = [
                'cityCode' => $validatedData['cityCode'],
                'radius' => $validatedData['radius'] ?? 5, // Default radius 5 km
                'radiusUnit' => $validatedData['radiusUnit'] ?? 'KM', // Default unit is KM
                'chainCodes' => isset($validatedData['chainCodes']) ? implode(',', $validatedData['chainCodes']) : null,
                'amenities' => isset($validatedData['amenities']) ? implode(',', $validatedData['amenities']) : null,
                'ratings' => isset($validatedData['ratings']) ? implode(',', $validatedData['ratings']) : null,
                'hotelSource' => $validatedData['hotelSource'] ?? 'ALL', // Default is ALL
            ];

            // Remove null values
            $params = array_filter($params);

            // Fetch access token using AmadeusApiTrait
            $accessToken = $this->getAccessToken();

            // Initialize HTTP client
            $client = new Client();

            // Make GET request to Amadeus Hotel List API
            $response = $client->request('GET', 'https://test.api.amadeus.com/v1/reference-data/locations/hotels/by-city', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => $params,
            ]);

            // Decode the response
            $data = json_decode($response->getBody()->getContents(), true);

            return $this->success($data['data'], 'Hotels retrieved successfully');
        } catch (\Exception $e) {
            return $this->error(null, 'Error retrieving hotels: ' . $e->getMessage(), 500);
        }
    }
}
