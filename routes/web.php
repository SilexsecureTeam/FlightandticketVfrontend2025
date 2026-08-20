<?php

use App\Livewire\Landing;
use App\Livewire\MailTest;
use App\Livewire\Auth\Login;
use App\Livewire\Page\Payment;
use App\Livewire\Page\Settings;
use App\Livewire\Page\AboutPage;
use App\Livewire\Page\Customers;
use App\Livewire\Page\Dashboard;
use App\Livewire\Page\FlightResult;
use App\Livewire\Flight\AirlinePage;
use App\Livewire\Flight\AirportPage;
use function Laravel\Prompts\search;
use App\Livewire\Booking\BookingPage;
use Illuminate\Support\Facades\Route;

use App\Livewire\Page\PaymentSettings;
use App\Livewire\Booking\PaymentBooking;
use App\Livewire\Flight\SearchFlightPage;
use App\Livewire\Flight\OfflineSuccessPage;
use App\Models\FlightSearch;

Route::get('/', Landing::class)->name('landing');
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/about-us', fn () => redirect()->route('cms.page', 'about-us'))->name('about.us');
Route::get('/terms-and-conditions', fn () => redirect()->route('cms.page', 'terms-and-conditions'))->name('terms');
Route::get('/page/{slug}', \App\Livewire\Cms\ShowPage::class)->name('cms.page');
Route::get('/blog', \App\Livewire\Cms\BlogIndex::class)->name('blog.index');
Route::get('/blog/{slug}', \App\Livewire\Cms\BlogShow::class)->name('blog.show');

// Route::middleware('guest')->group(function () {
    Route::get('/mail', MailTest::class);

    Route::get('/login', Login::class)->name('login');

    Route::get('/search', FlightResult::class)->name('search.results');
    Route::get('/search/status/{searchId}', function (string $searchId) {
        $search = FlightSearch::query()->firstWhere('search_id', $searchId);

        if (!$search) {
            return response()->json(['status' => 'not_found'], 404);
        }

        $meta = $search->meta ?? [];

        return response()->json([
            'status' => $search->status,
            'more_results_ready' => ($meta['more_results_ready'] ?? false)
                || in_array($search->status, ['completed', 'view'], true),
        ]);
    })->name('search.status');
    Route::get('/booking', BookingPage::class)->name('booking.page');
    Route::get('/booking/payment', PaymentBooking::class)->name('booking.payment');

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
   
    // Route::prefix('flight')->group( function () {
     
    // });
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/payment', Payment::class)->name('payment');
    Route::get('/customers', Customers::class)->name('customers');
    Route::get('/settings', Settings::class)->name('settings');
    Route::get('/admin/pages', \App\Livewire\Admin\ManagePages::class)->name('admin.pages');
    Route::get('/admin/blog', \App\Livewire\Admin\ManageBlogs::class)->name('admin.blog');
    Route::post('/admin/cms/upload-image', [\App\Http\Controllers\CmsMediaController::class, 'upload'])->name('admin.cms.upload');

    Route::prefix('flight')->group(function () {
        Route::get('/search', SearchFlightPage::class)->name('flight.search');
        Route::get('/search_result', FlightResult::class)->name('flight.search.results');
        Route::get('/search_booking', BookingPage::class)->name('flight.booking.page');
        Route::get('/search_payment', PaymentBooking::class)->name('flight.booking.payment');
    });
    Route::prefix('settings')->group(function () {
        Route::get('/airport', AirportPage::class)->name('settings.airport');
        Route::get('/payment', PaymentSettings::class)->name('settings.payment');

        Route::get('/airline', AirlinePage::class)->name('settings.airline');
    });

    // Route::prefix('payment')->group(function () {

    //     Route::get('/offline/{orderResponse}', OfflineSuccessPage::class)->name('payment.offline');
    // });
});




