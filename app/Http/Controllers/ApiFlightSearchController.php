<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Services\GeneralService;
use Illuminate\Http\Request;

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
}