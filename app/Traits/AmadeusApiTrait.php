<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

trait AmadeusApiTrait
{
    protected function amadeusHttpVerifyOption(): array
    {
        if (config('services.amadeus.verify_ssl', true)) {
            return [];
        }

        return ['verify' => false];
    }

    public function getAccessToken(): string
    {
        $baseUrl = config('services.amadeus.base_url');
        $clientId = config('services.amadeus.client_id');
        $clientSecret = config('services.amadeus.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            throw new \RuntimeException('Amadeus API credentials are not configured.');
        }

        try {
            $response = Http::withOptions($this->amadeusHttpVerifyOption())
                ->asForm()
                ->post("$baseUrl/v1/security/oauth2/token", [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'grant_type' => 'client_credentials',
                ]);

            $responseData = $response->json();

            if ($response->ok() && !empty($responseData['access_token'])) {
                return $responseData['access_token'];
            }

            $error = $responseData['error_description']
                ?? $responseData['error']
                ?? json_encode($responseData);

            throw new \RuntimeException("Failed to obtain Amadeus access token (HTTP {$response->status()}): {$error}");
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to obtain Amadeus access token: ' . $e->getMessage(), 0, $e);
        }
    }
    



}