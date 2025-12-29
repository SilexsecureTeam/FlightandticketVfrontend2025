<?php

use App\Traits\AmadeusApiTrait;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Testcontroller;
use App\Http\Controllers\ApiFlightSearchController;
use App\Http\Controllers\Authcontroller\Authcontroller;
use App\Http\Controllers\HotelBooking\HotelListController;
use App\Http\Controllers\FlightBooking\GetPricingController;
use App\Http\Controllers\HotelBooking\HotelSearchController;
use App\Http\Controllers\HotelBooking\OfferSearchController;
use App\Http\Controllers\HotelBooking\HotelBookingController;
use App\Http\Controllers\FlightBooking\FlightSearchController;
use App\Http\Controllers\FlightBooking\AirportSearchController;
use App\Http\Controllers\FlightBooking\CreateFlightOrderController;

Route::post('register', [Authcontroller::class, 'register']);
Route::post('login', [Authcontroller::class, 'login']);
Route::post('verifyOtp', [Authcontroller::class, 'verifyOtp']);
Route::post('resendOtp', [Authcontroller::class, 'resendOtp']);
Route::post('resetForgetPassword', [Authcontroller::class, 'resetForgetPassword']);

Route::prefix('flights')->group(function () {
    Route::get('/popularCities', [ApiFlightSearchController::class, 'popularCities']);
    Route::post('/searchAirports', [ApiFlightSearchController::class, 'searchAirports']);

    // Route::post('/search', [FlightSearchController::class, 'searchFlights']);
    // Route::get('/search-airports', [AirportSearchController::class, 'searchAirports']);
    // Route::post('/get-pricing', [GetPricingController::class, 'getFlightPricing']);
    // Route::post('/create-order', [CreateFlightOrderController::class, 'createFlightOrder']);
});

Route::prefix('hotel')->group(function () {

    Route::post('/booking', [HotelBookingController::class, 'bookHotel']);
    Route::get('/list', [HotelListController::class, 'getHotelsByCity']);
    Route::get('/offers', [HotelSearchController::class, 'getHotelOffers']);
    Route::get('/offers/information/{offerId}', [OfferSearchController::class, 'getHotelOfferDetails']);
});



Route::get('/token2', [Testcontroller::class, 'getAccessToken']);
