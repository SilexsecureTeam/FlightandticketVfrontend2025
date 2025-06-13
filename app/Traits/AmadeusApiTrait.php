<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

trait AmadeusApiTrait
{
    public function getAccessToken()
    {
        try {
            // Fetch Amadeus API credentials from configuration
            $baseUrl = config('services.amadeus.base_url');
            $clientId = config('services.amadeus.client_id');
            $clientSecret = config('services.amadeus.client_secret');
    
            // Request payload for access token
            $data = [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'client_credentials',
            ];
    
            // Make the POST request using the Http facade
            $response = Http::asForm()
            ->post("$baseUrl/v1/security/oauth2/token", $data);
    
            // Decode and retrieve the JSON response
            $responseData = $response->json();
    
            // Check if the response contains an access token
            if ($response->ok() && isset($responseData['access_token'])) {
                return $responseData['access_token'];
            }
    
            // Return the response errors if the request failed
            return [
                'status' => $response->status(),
                'error' => $responseData,
            ];
        } catch (\Exception $e) {
            // Catch and return unexpected errors
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
    



}