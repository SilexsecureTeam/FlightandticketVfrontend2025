<?php

namespace App\Http\Controllers;

use App\Http\Services\FlightBookingService;
use App\Models\FlightSearch;
use App\Models\User;
use App\Models\Flight\FlightOrder;
use App\Mail\WelcomeUserMail;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
                'itineraries' => $orderResponse['data']['flightOffers'][0]['itineraries'] ?? [],
                'price' => $orderResponse['data']['flightOffers'][0]['price'] ?? [],
                'commission' => $this->selectedFlight['price']['commission'] ?? 0,
                'travelType' => $orderResponse['data']['flightOffers'][0]['travelerPricings'] ?? [],
                'remarks' => $orderResponse['data']['remarks']['general'] ?? [],
            ];

            $this->bookingService->sendConfirmationEmails($bookingData, $email);

            // Link booking to user account (create if not exists)
            $this->handleUserAccount($email, $orderResponse['data']['travelers'][0], $order, $request->search_id);

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
                    'bookingData' => $bookingData,
                    'orderResponse' => $orderResponse,
                    'email' => $email,
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
        // $result = $this->processBooking($request);
        // if ($result instanceof \Illuminate\Http\JsonResponse) {
        //     return $result;
        // }

        // $order = $result['order'];
        // $email = $result['email'];
        // $grandTotal = $result['grandTotal'];
        // $pricedOffer = $result['pricedOffer'];
        // $orderResponse = $result['orderResponse'];
        // $pnr = $result['pnr'];

        // try {
        //     // Step 7: Initialize Payment Record

        $bookingData = $request->bookingData;
        $orderResponse = $bookingData['orderResponse'];
        $email = $bookingData['email'];

        $order = FlightOrder::query()
            ->where('pnr', '=', $bookingData['pnr'])
            ->firstOrFail();

        $paymentMethod = $request->input('payment_method', 'Offline');
        $paymentData = $this->bookingService->createApiPaymentRecord($order, $request->grandTotal, $paymentMethod);
        if ($paymentMethod !== 'Offline') {
            $order->update([
                'payment_status' => 'APPROVED',
            ]);
        }


        // Step 8: Send confirmation emails
        // $bookingData = [
        //     'travelers' => $orderResponse['data']['travelers'],
        //     'pnr' => $pnr,
        //     'itineraries' => $orderResponse['data']['flightOffers'][0]['itineraries'] ?? [],
        //     'price' => $orderResponse['data']['flightOffers'][0]['price'] ?? [],
        //     'commission' => $this->selectedFlight['price']['commission'] ?? 0,
        //     'travelType' => $orderResponse['data']['flightOffers'][0]['travelerPricings'] ?? [],
        //     'remarks' => $orderResponse['data']['remarks']['general'] ?? [],
        // ];


        $this->bookingService->sendOfflineConfirmationEmails($bookingData, $email);

        // Link booking to user account (create if not exists)
        $this->handleUserAccount($email, $orderResponse['data']['travelers'][0], $order, $request->search_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Flight reservation created successfully (Offline Payment)',
            'data' => [
                'order_id' => $order->id,
                'pnr' => $order->pnr,
                'reference' => $paymentData['reference'],
                'payment_id' => $paymentData['payment_id'],
                'priced_offer' => $request->grandTotal,
            ],
        ], 201);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'An unexpected error occurred during reservation: ' . $e->getMessage(),
        //     ], 500);
        // }
    }

    /**
     * Shared booking logic (common to both book and reserve)
     */
    protected function processBooking(Request $request)
    {
        $flightSearch = FlightSearch::query()
            ->where('search_id', '=', $request->search_id)
            ->first();
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
                'travelers.*.documents' => 'nullable|array|min:1',
                'travelers.*.documents.*.documentType' => 'nullable|string|in:PASSPORT,ID_CARD',
                'travelers.*.documents.*.birthPlace' => 'nullable|string|max:255',
                'travelers.*.documents.*.issuanceLocation' => 'nullable|string|max:255',
                'travelers.*.documents.*.number' => 'nullable|string|max:255',
                'travelers.*.documents.*.expiryDate' => 'nullable|date|after:today',
                'travelers.*.documents.*.issuanceCountry' => 'nullable|string',
                'travelers.*.documents.*.validityCountry' => 'nullable|string',
                'travelers.*.documents.*.nationality' => 'nullable|string',
                'travelers.*.documents.*.holder' => 'nullable|boolean',
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
    /**
     * Handle user account creation and linking
     */
    private function handleUserAccount($email, $traveler, $order, $searchId)
    {
        try {
            $user = User::query()
                ->where('email', '=', $email)
                ->first();

            if (!$user) {
                // Create user if not present
                $firstName = $traveler['name']['firstName'] ?? '';
                $lastName = $traveler['name']['lastName'] ?? '';
                $name = trim($firstName . ' ' . $lastName);
                $phone = $traveler['contact']['phones'][0]['number'] ?? null;

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt(Str::random(12)), // Random password
                    'phone' => $phone,
                    'status' => 'active', // Assuming default status
                ]);

                // Send welcome email
                Mail::to($email)->send(new WelcomeUserMail($name));
            }

            // Update flight order with user_id
            $order->update(['user_id' => $user->id]);

            // Update flight search with user_id
            $flightSearch = FlightSearch::query()
                ->where('search_id', '=', $searchId)
                ->first();
            if ($flightSearch) {
                $flightSearch->update(['user_id' => $user->id]);
            }
        } catch (\Exception $e) {
            Log::error('Error handling user account: ' . $e->getMessage());
            // We don't throw exception here to avoid breaking the booking flow if user creation fails
        }
    }

    /**
     * GET: Retrieve flight orders for the authenticated user
     */
    public function getUserOrders(Request $request)
    {
        $user = $request->user();
        $orders = FlightOrder::query()
            ->where('user_id', '=', $user->id)
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => 'success',
            'message' => 'Orders retrieved successfully',
            'data' => $orders,
        ], 200);
    }
}
