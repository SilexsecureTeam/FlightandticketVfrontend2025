<?php

use App\Http\Controllers\ApiFlightBookingController;
use App\Http\Controllers\ApiFlightSearchController;
use App\Http\Controllers\Authcontroller;
use App\Http\Controllers\FlightBooking\AirportSearchController;
use App\Http\Controllers\FlightBooking\CreateFlightOrderController;
use App\Http\Controllers\FlightBooking\FlightSearchController;
use App\Http\Controllers\FlightBooking\GetPricingController;
use App\Http\Controllers\FlightSelectionAuthController;
use App\Http\Controllers\HotelBooking\HotelBookingController;
use App\Http\Controllers\HotelBooking\HotelListController;
use App\Http\Controllers\HotelBooking\HotelSearchController;
use App\Http\Controllers\HotelBooking\OfferSearchController;
use App\Http\Controllers\Testcontroller;
use App\Traits\AmadeusApiTrait;
use Illuminate\Support\Facades\Route;

use App\Models\Page;
Route::get('/pagetest', function () {
    $page = Page::all();
    return response()->json([
        'success' => true,
        'message' => 'API is working',
        'data' => $page,
    ]);
});

Route::post('register', [Authcontroller::class, 'register']);
Route::post('login', [Authcontroller::class, 'login']);
Route::post('verifyOtp', [Authcontroller::class, 'verifyOtp']);
Route::post('resendOtp', [Authcontroller::class, 'resendOtp']);
Route::post('resetForgetPassword', [Authcontroller::class, 'resetForgetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('profile', [Authcontroller::class, 'getProfile']);
    Route::post('profile/update', [Authcontroller::class, 'updateProfile']);
    Route::post('password/change', [Authcontroller::class, 'changePassword']);

    // User Flight History
    Route::get('flights/orders', [ApiFlightBookingController::class, 'getUserOrders']);
    Route::get('flights/searches', [ApiFlightSearchController::class, 'getUserSearches']);
});

Route::prefix('flights')->group(function () {
    Route::get('/popularCities', [ApiFlightSearchController::class, 'popularCities']);
    Route::post('/searchAirports', [ApiFlightSearchController::class, 'searchAirports']);
    Route::post('/search', [ApiFlightSearchController::class, 'search']);
    Route::get('/search/{searchId}', [ApiFlightSearchController::class, 'getSearchResults']);
    Route::post('/selection/send-otp', [FlightSelectionAuthController::class, 'sendOtp']);
    Route::post('/selection/resend-otp', [FlightSelectionAuthController::class, 'resendOtp']);
    Route::post('/selection/verify-otp', [FlightSelectionAuthController::class, 'verifyOtp']);
    Route::post('/book', [ApiFlightBookingController::class, 'book']);
    Route::post('/reserve', [ApiFlightBookingController::class, 'reserve']);
});

Route::prefix('hotel')->group(function () {

    Route::post('/booking', [HotelBookingController::class, 'bookHotel']);
    Route::get('/list', [HotelListController::class, 'getHotelsByCity']);
    Route::get('/offers', [HotelSearchController::class, 'getHotelOffers']);
    Route::get('/offers/information/{offerId}', [OfferSearchController::class, 'getHotelOfferDetails']);
});



Route::get('/token2', [Testcontroller::class, 'getAccessToken']);
