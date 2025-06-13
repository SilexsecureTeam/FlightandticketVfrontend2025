<?php

namespace App\Http\Controllers\FlightBooking;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\PaystackController;
use App\Models\Flight\FlightOrder;
use App\Traits\AmadeusApiTrait;
use App\Traits\HttpResponses;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class CreateFlightOrderController extends Controller
{
    use HttpResponses, AmadeusApiTrait;

    public function createFlightOrder(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'flightOffers' => 'required|array',
                'travelers' => 'required|array|min:1',
                'travelers.*.id' => 'required|string',
                'travelers.*.dateOfBirth' => 'required|date|before:today',
                'travelers.*.name' => 'required|array',
                'travelers.*.name.firstName' => 'required|string|max:255',
                'travelers.*.name.lastName' => 'required|string|max:255',
                'travelers.*.gender' => 'required|in:MALE,FEMALE,OTHER',
                'travelers.*.contact' => 'required|array',
                'travelers.*.contact.emailAddress' => 'required|email',
                'travelers.*.contact.phones' => 'required|array|min:1',
                'travelers.*.contact.phones.*.deviceType' => 'required|string|in:MOBILE,LANDLINE',
                'travelers.*.contact.phones.*.countryCallingCode' => 'required|string',
                'travelers.*.contact.phones.*.number' => 'required|string',
                'travelers.*.documents' => 'sometimes|array|min:1',
                'travelers.*.documents.*.documentType' => 'required|string|in:PASSPORT,ID_CARD',
                'travelers.*.documents.*.birthPlace' => 'required|string|max:255',
                'travelers.*.documents.*.issuanceLocation' => 'required|string|max:255',
                'travelers.*.documents.*.issuanceDate' => 'required|date|before:today',
                'travelers.*.documents.*.number' => 'required|string|max:255',
                'travelers.*.documents.*.expiryDate' => 'required|date|after:today',
                'travelers.*.documents.*.issuanceCountry' => 'required|string|size:2',
                'travelers.*.documents.*.validityCountry' => 'required|string|size:2',
                'travelers.*.documents.*.nationality' => 'required|string|size:2',
                'travelers.*.documents.*.holder' => 'required|boolean',
            ]);

            $flightOrder = [
                'data' => [
                    'type' => 'flight-order',
                    'flightOffers' => $validatedData['flightOffers'],
                    'travelers' => $validatedData['travelers'],
                    'remarks' => [
                        'general' => [
                            [
                                'subType' => 'GENERAL_MISCELLANEOUS',
                                'text' => 'ONLINE BOOKING FROM INCREIBLE VIAJES',
                            ]
                        ]
                    ],
                    'ticketingAgreement' => [
                        'option' => 'DELAY_TO_CANCEL',
                        'delay' => '6D',
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
            $baseUrl = config('services.amadeus.base_url');
            $response = Http::withToken($this->getAccessToken())

            ->post("$baseUrl/v1/booking/flight-orders", $flightOrder);


         
        if ($response->successful()) {
            $orderData = $response->json();

            $flightOrder =  FlightOrder::create([
                'order_id' => $orderData['data']['id'],
                'flight_offers' => $validatedData['flightOffers'],  // No json_encode() here
                'travelers' => $validatedData['travelers'],
                'payment_status' => 'PENDING',
                'order_status' => 'PROCESSING',
            ]);
             // Get amount for payment initialization
             $amount = $orderData['data']['flightOffers'][0]['price']['grandTotal'];
                $type ="Flight";
             // Initialize payment with Paystack
             $paystackResponse = app(PaystackController::class)
                 ->initializePayment($request, $flightOrder->id, $validatedData['travelers'][0]['contact']['emailAddress'], $amount, $type);

             $paymentResponseData = json_decode($paystackResponse->getContent(), true);


             // Ensure payment initialization URL is received
             if (isset($paymentResponseData['paystackURL'])) {
                 return $this->success([
                     'payment_url' => $paymentResponseData['paystackURL'],
                     'order' => $orderData,
                 ], 'Flight order created and payment initialized successfully.');
             }

             // Handle case where payment URL is not returned
             return $this->error([
                'error_details' => $paymentResponseData // Return full error details here
            ], 'Payment initialization failed. Please try again.', 400);
         }

         return $this->error(null, 'Error creating flight order: ' . $response->body(), $response->status());
     } catch (ValidationException $e) {
         return $this->error($e->errors(), 'Validation Error', 422);
     } catch (\Exception $e) {
         return $this->error(null, 'Server Error: ' . $e->getMessage(), 500);
     }
 }
}
