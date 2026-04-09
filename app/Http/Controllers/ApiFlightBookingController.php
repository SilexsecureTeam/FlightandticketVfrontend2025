<?php

namespace App\Http\Controllers;

use App\Http\Services\FlightBookingService;
use App\Models\FlightSearch;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ApiFlightBookingController extends Controller
{
    protected $bookingService;

    public function __construct(FlightBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * POST: Create flight booking
     */
    /**
     * POST: Create flight booking (Paystack / AlatPay)
     */
    public function book(Request $request)
    {
        $result = $this->processBooking($request);
        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        $order = $result['order'];
        $email = $result['email'];
        $grandTotal = $result['grandTotal'];
        $pricedOffer = $result['pricedOffer'];
        $orderResponse = $result['orderResponse'];
        $pnr = $result['pnr'];
        
        try {
            // Step 7: Initialize payments
            $paymentData = $this->bookingService->initializePayment($order, $email, $grandTotal);

            // Step 8: Send emails (Confirmation)
            $bookingData = [
                'travelers' => $orderResponse['data']['travelers'],
                'pnr' => $pnr,
                'itineraries' => $pricedOffer['itineraries'] ?? [],
                'price' => $pricedOffer['price'] ?? [],
                'commission' => 0, 
                'travelType' => $pricedOffer['travelerPricings'] ?? [],
                'remarks' => $orderResponse['data']['remarks']['general'] ?? [],
            ];
            // $this->bookingService->sendConfirmationEmails($bookingData, $email);

            return response()->json([
                'status' => 'success',
                'message' => 'Flight order created and payment initialized successfully',
                'data' => [
                    'order_id' => $order->id,
                    'pnr' => $pnr,
                    'paystack_url' => $paymentData['paystack_url'],
                    'alatpay_url' => $paymentData['alatpay_url'],
                    'payment_ids' => $paymentData['payment_ids'],
                    'priced_offer' => $pricedOffer,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred during payment initialization: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST: Reserve and Pay Later (Offline)
     */
    public function reserve(Request $request)
    {
        $result = $this->processBooking($request);
        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        $order = $result['order'];
        $email = $result['email'];
        $grandTotal = $result['grandTotal'];
        $pricedOffer = $result['pricedOffer'];
        $orderResponse = $result['orderResponse'];
        $pnr = $result['pnr'];

        try {
            // Step 7: Initialize Payment Record
            $paymentMethod = $request->input('payment_method', 'Offline');
            $paymentData = $this->bookingService->createApiPaymentRecord($order, $grandTotal, $paymentMethod);
            if($paymentMethod !== 'Offline'){
                $order->update([
                    'payment_status' => 'APPROVED',
                ]);
            }
            
            
            // Step 8: Send confirmation emails
            $bookingData = [
                'travelers' => $orderResponse['data']['travelers'],
                'pnr' => $pnr,
                'itineraries' => $pricedOffer['itineraries'] ?? [],
                'price' => $pricedOffer['price'] ?? [],
                'commission' => 0, 
                'travelType' => $pricedOffer['travelerPricings'] ?? [],
                'remarks' => $orderResponse['data']['remarks']['general'] ?? [],
            ];
            $this->bookingService->sendOfflineConfirmationEmails($bookingData, $email);

            return response()->json([
                'status' => 'success',
                'message' => 'Flight reservation created successfully (Offline Payment)',
                'data' => [
                    'order_id' => $order->id,
                    'pnr' => $pnr,
                    'reference' => $paymentData['reference'],
                    'payment_id' => $paymentData['payment_id'],
                    'priced_offer' => $pricedOffer,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred during reservation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Shared booking logic (common to both book and reserve)
     */
    protected function processBooking(Request $request)
    {
        $flightSearch = FlightSearch::where('search_id', $request->search_id)->first();
        if (!$flightSearch) {
            return response()->json([
                'status' => 'error',
                'message' => 'Search session not found or expired',
            ], 404);
        }

        try {
            $validatedData = $request->validate([
                'search_id' => 'required|string|exists:flight_searches,search_id',
                'flight_offer' => 'required|string',
                'payment_method' => 'nullable|string', // NEW: Added payment_method
                'travelers' => 'required|array|min:1',
                'travelers.*.type' => 'required|string|in:ADULT,CHILD,HELD_INFANT',
                'travelers.*.id' => 'required|string',
                'travelers.*.dateOfBirth' => 'required|date|before:today',
                'travelers.*.name' => 'required|array',
                'travelers.*.name.firstName' => 'required|string|max:255',
                'travelers.*.name.middleName' => 'nullable|string|max:255',
                'travelers.*.name.lastName' => 'required|string|max:255',
                // 'travelers.*.gender' => 'required|string',
                'travelers.*.contact' => 'required|array',
                'travelers.*.contact.emailAddress' => 'required|email',
                'travelers.*.contact.phones' => 'required|array|min:1',
                'travelers.*.contact.phones.*.deviceType' => 'required|string|in:MOBILE,LANDLINE',
                'travelers.*.contact.phones.*.countryCallingCode' => 'required|string',
                'travelers.*.contact.phones.*.number' => 'required|string|min:10',
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


            $offerId = $validatedData['flight_offer'];
            $clientRef = $flightSearch->clientRef ?? 'API-' . uniqid();

            // Step 1: Find full flight offer
            $fullOffer = $this->bookingService->findFlightOffer($request->search_id, $offerId);
            if (!$fullOffer) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Flight offer with ID [$offerId] not found in search results",
                ], 404);
            }

            // Step 2: Get Final Pricing
            $pricedOffer = $this->bookingService->getFlightPricing($fullOffer, $clientRef);

            // Step 3: Validate age/traveler type
            $this->bookingService->validateTravelers($validatedData['travelers']);

            // Step 4: Prepare payload
            $payload = $this->bookingService->prepareFlightOrderPayload(
                $validatedData['travelers'],
                $pricedOffer
            );

            // Step 5: Send request to Amadeus
            $orderResponse = $this->bookingService->sendFlightOrderRequest($payload, $clientRef);

            $pnr = $orderResponse['data']['associatedRecords'][0]['reference'] ?? null;
            $email = $validatedData['travelers'][0]['contact']['emailAddress'];
            $grandTotal = $pricedOffer['price']['grandTotal'] ?? $pricedOffer['price']['total'];

            // Step 6: Save order
            $order = $this->bookingService->saveFlightOrder(
                $orderResponse,
                $pricedOffer,
                $validatedData['travelers'],
                $pnr
            );

            return [
                'order' => $order,
                'email' => $email,
                'grandTotal' => $grandTotal,
                'pricedOffer' => $pricedOffer,
                'orderResponse' => $orderResponse,
                'pnr' => $pnr,
            ];

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);
            return response()->json([
                'status' => 'error',
                'message' => 'Amadeus API error',
                'errors' => $response['errors'] ?? [$e->getMessage()],
            ], $e->getResponse()->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
