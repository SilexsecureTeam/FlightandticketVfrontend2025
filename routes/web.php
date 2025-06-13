<?php

use App\Livewire\Auth\Login;
use App\Livewire\Booking\BookingPage;
use App\Livewire\Booking\PaymentBooking;
use App\Livewire\Flight\OfflineSuccessPage;
use App\Livewire\Flight\SearchFlightPage;
use App\Livewire\Landing;
use App\Livewire\MailTest;
use App\Livewire\Page\AboutPage;
use App\Livewire\Page\Customers;
use App\Livewire\Page\Dashboard;
use App\Livewire\Page\FlightResult;
use App\Livewire\Page\Payment;
use App\Livewire\Page\Settings;
use Illuminate\Support\Facades\Route;

use function Laravel\Prompts\search;

Route::get('/', Landing::class)->name('landing');


// Route::middleware('guest')->group(function () {
    Route::get('/mail', MailTest::class);

    Route::get('/login', Login::class)->name('login');

    Route::get('/search', FlightResult::class)->name('search.results');
    Route::get('/booking', BookingPage::class)->name('booking.page');
    Route::get('/payment', PaymentBooking::class)->name('booking.payment');

    Route::prefix('payment')->group(function () {
        Route::get('/offline/{orderResponse}', OfflineSuccessPage::class)->name('payment.offline');
    });
    
    // Route::get('/login', LoginPage::class)->name('login')->name('login');
// });

Route::middleware('auth')->group(function () {
    Route::get('/logout', function(){
        auth()->logout();
        return redirect('/');
    });
   
    Route::prefix('flight')->group( function () {
     
    });

    
    
    

});


