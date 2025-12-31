<?php

namespace App\Http\Controllers;

use App\Models\FlightSearch;
use Illuminate\Http\Request;
use App\Livewire\Components\Search;
use App\Http\Controllers\Controller;
use App\Http\Services\GeneralService;

class ApiFlightSearchController extends Controller
{
    public function popularCities()
    {
        $data = (new GeneralService)->popularCities();

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $data,
        ], 200);
    }

    public function searchAirports(Request $request)
    {
        $data = (new GeneralService)->getAirPort($request->input('query'));

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $data,
        ], 200);
    }

    /**
     * POST: Create flight search (quick response)
     */
    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
            'departureDate' => 'required|date|after:yesterday',
            'returnDate' => 'nullable|date|after:departureDate',
            'tripType' => 'required|in:oneway,round',
            'passengers.adult' => 'required|integer|min:1|max:9',
            'passengers.child' => 'nullable|integer|min:0',
            'passengers.infant' => 'nullable|integer|min:0',
            'cabinClass' => 'required|string',
            'currencyCode' => 'required|string|size:3',
        ]);

        $clientRef = 'mymore-' . uniqid(bin2hex(random_bytes(6)), true);
        session(['clientRef' => $clientRef]);
        $searchId = 'search_' . uniqid();

        // Save initial search
        $search = FlightSearch::create([
            'search_id' => $searchId,
            'trip_type' => $validatedData['tripType'],
            'passengers' => $validatedData['passengers'],
            'cabinClass' => $validatedData['cabinClass'],
            'from_code' => $validatedData['from'],
            'to_code' => $validatedData['to'],
            'departure_date' => $validatedData['departureDate'],
            'return_date' => $validatedData['returnDate'] ?? null,
            'status' => 'pending',
        ]);

        (new Search)->searchJob($validatedData, $clientRef, $searchId, $fligtBokked->id, $originDestinations, $travelers, $cabinRestrictions);
        (new Search)->searchDirect($validatedData, $clientRef, $searchId, $fligtBokked->id, $originDestinations, $travelers, $cabinRestrictions);
 

        return response()->json([
            'status' => 'pending',
            'searchId' => $searchId,
            'preview' => $quickResult['flightOffers'][0] ?? null,
        ]);
    }

    /**
     * GET: Check search status
     */
    public function status($searchId)
    {
        $search = FlightSearch::where('search_id', $searchId)->first();

        if (!$search) {
            return response()->json(['error' => 'Search not found'], 404);
        }

        return response()->json([
            'searchId' => $searchId,
            'status' => $search->status,
            'expiresAt' => $search->expires_at,
        ]);
    }

    /**
     * GET: First flight only
     */
    public function preview($searchId)
    {
        $search = FlightSearch::where('search_id', $searchId)->firstOrFail();

        if ($search->status === 'pending') {
            return response()->json(['message' => 'Search still processing'], 202);
        }

        $offers = is_string($search->flight_offers)
            ? json_decode($search->flight_offers, true)
            : $search->flight_offers;

        return response()->json([
            'searchId' => $searchId,
            'firstOffer' => $offers[0] ?? null,
        ]);
    }

    /**
     * GET: Full results
     */
    public function results($searchId)
    {
        $search = FlightSearch::where('search_id', $searchId)->first();

        if (!$search) {
            return response()->json(['error' => 'Search not found'], 404);
        }

        if ($search->status !== 'completed') {
            return response()->json([
                'status' => $search->status,
                'message' => 'Results not ready yet',
            ], 202);
        }

        return response()->json([
            'tripType' => $search->trip_type,
            'meta' => $search->meta,
            'flightOffers' => json_decode($search->flight_offers, true),
            'dictionaries' => json_decode($search->dictionaries, true),
            'cheapestFlight' => json_decode($search->cheapest, true),
        ]);
    }

}