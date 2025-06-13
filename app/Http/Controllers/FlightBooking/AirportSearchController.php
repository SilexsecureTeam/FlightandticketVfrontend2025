<?php

namespace App\Http\Controllers\FlightBooking;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use App\Traits\AmadeusApiTrait;
use App\Traits\HttpResponses;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AirportSearchController extends Controller
{
    use HttpResponses;


public function searchAirports(Request $request)
{
    try {
        $query = Airline::query();

        // Apply search filters if provided
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('airport_code', 'LIKE', "%{$search}%")
                  ->orWhere('airport', 'LIKE', "%{$search}%")
                  ->orWhere('city_country', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('country', 'LIKE', "%{$search}%");
        }

        // Paginate the results
        $airlines = $query->paginate(30);

        // Format the data
        $formatted = [
            'data' => $airlines->map(function ($airline) {
                return [
                    'airport_code' => $airline->airport_code,
                    'airport' => $airline->airport,
                    'city_country' => $airline->city_country,
                    'city' => $airline->city,
                    'country' => $airline->country,
                ];
            }),
            'current_page' => $airlines->currentPage(),
            'total' => $airlines->total(),
            'per_page' => $airlines->perPage(),
            'next_page_url' => $airlines->nextPageUrl(),
            'prev_page_url' => $airlines->previousPageUrl(),
        ];

        return $this->success($formatted, 'Airports and cities retrieved successfully');
    } catch (\Exception $e) {
        return $this->error(null, 'Error searching airports: ' . $e->getMessage(), 500);
    }
}

    

}
