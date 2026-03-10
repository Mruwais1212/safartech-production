<?php


use App\Http\Controllers\Website\TripController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Website\SessionController;
use App\Http\Controllers\Webhooks\MoyasarWebhookController;

Route::get('page/{page}', function ($page) {
    return view("website.$page");
});

Route::group(['middleware' => ['web', 'guest:web']], function () {
    Route::resource('sign-in', 'LoginController')->name('index', 'login')->only('index', 'store');
    Route::resource('sign-up', 'RegisterController')->name('index', 'register')->only('index', 'store');
    Route::resource('forgot-password', 'ForgotPasswordController')->only('index', 'store');
    Route::resource('reset-password', 'ResetPasswordController')->only('index', 'store')->name('index', 'password.reset');
});

Route::group(['middleware' => ['auth:web']], function () {
    Route::post('sign-out', 'LoginController@logout');
    Route::resource('change-password', 'ChangePasswordController')->only('index', 'store');
    Route::resource('edit-profile', 'EditProfileController')->only('index', 'store');
});


Route::post('webhooks/moyasar', MoyasarWebhookController::class)->middleware('throttle:moyasar-webhook');

Route::group(['middleware' => ['web']], function () {
    Route::get('/', 'HomeController@index');
    Route::get('ai-trip', 'AiTripController@index');
    Route::post('ai-trip', 'AiTripController@store')->middleware('throttle:ai-generation');
    Route::post('search-plans', 'AiTripController@searchPlans')->middleware('throttle:ai-generation');
    Route::post('search-plans-new', 'AiTripController@searchPlansNew')->middleware('throttle:ai-generation');

    Route::get('ai-trip-planner', 'AiTripPlannerController@index');
    Route::get('latest-ai-trip-planner', 'MyLatestAiTripController@index')->middleware('auth:web');
    Route::get('latest-ai-trip-planner/{id}', 'MyLatestAiTripController@show')->middleware('auth:web');
    Route::get('ai-trip-latest-planner/{id}', 'MyLatestAiTripController@data')->middleware('auth:web');
    Route::post('ai-trip-results', 'AiTripPlannerController@show')->middleware('throttle:ai-generation');
    Route::post('ai-trip-planner', 'AiTripPlannerController@store')->middleware('throttle:ai-generation');
    Route::resource('trips', 'TripController')->only('index', 'show');
    Route::get('plan/{id}', 'TripController@plan');
    Route::get('/search-results', [TripController::class, 'showResults'])->name('search.results');


    Route::get('saved-trips', 'HomeController@savedTrips');
    Route::get('trip-details/{id}', 'HomeController@tripDetails');

    Route::resource('passenger-information', 'PassengerInformationController');
    
    // SSR API endpoint for AJAX calls
    Route::get('passenger-information/ssr-data', 'PassengerInformationController@getSSRData')->name('passenger.ssr-data');
    Route::post('passenger-information/update-ssr', 'PassengerInformationController@updateSSRData')->name('passenger.update-ssr');

    Route::resource('contact-us', 'ContactUsController');
    Route::get('about-us', 'FixedPageController@aboutUs');
    Route::get('privacy-policy', 'FixedPageController@privacyPolicy');
    Route::get('terms-and-conditions', 'FixedPageController@termsAndConditions');

    Route::get('my-trips', 'MyTripController@index');
    Route::get('my-trips/{id}', 'MyTripController@show');
    Route::get('flight-tickets/{id}', 'MyTripController@flightTickets');
    Route::get('export-trip/{id}', 'MyTripController@exportTripDetails')->middleware('auth:web');

    Route::get('summary', 'PaymentController@summary');
    Route::post('payment', 'PaymentController@payment')->middleware('throttle:payment-init');
    Route::get('success', 'PaymentController@success');
    Route::get('moyasar-callback', 'PaymentController@moyasarCallback');
    Route::get('moyasar-success', 'PaymentController@moyasarSuccessEntry');
    Route::post('moyasar-success', 'PaymentController@moyasarSuccess')->middleware('throttle:payment-init');
    Route::get('booking-error', 'PaymentController@bookingError');

    Route::get('flights', 'FlightController@index');
    Route::post('search-flights', 'FlightController@search')->middleware('throttle:search');
    Route::post('search-flights-new', 'FlightController@searchNew')->middleware('throttle:search');
    Route::get('select-flight', 'FlightController@selectFlight');
    Route::post('cancel-flight/{pnr}', 'FlightController@cancel_flight')->middleware(['auth:web', 'throttle:cancel']);

    Route::get('hotels', 'HotelController@index');
    Route::get('hotel/{id}', 'HotelController@show');
    Route::post('search-hotels', 'HotelController@search')->middleware('throttle:search');
    Route::post('search-hotels-new', 'HotelController@searchNew')->middleware('throttle:search');
    Route::get('select-room/{id}', 'HotelController@selectRoom');
    Route::get('choose/{id}', 'HotelController@choose');
    Route::get('search-airport', 'HotelController@searchAirport');

    Route::get('search-city', 'HotelController@searchCity');
    Route::get('search-airport-city', 'HotelController@searchAirportCity');
    Route::post('cancel-hotel/{booking_code}', 'HotelController@cancel_hotel')->middleware(['auth:web', 'throttle:cancel']);

    Route::post('tbo-hotel', 'TBOHotelDetailsController@hotelDetails');

    Route::get('/generate-invoice/{id}', [InvoiceController::class, 'generateInvoice'])->middleware('auth:web');

    Route::get('/flight-details',[\App\Http\Controllers\Website\MyTripController::class,'flight_test']);

    // Booking Details Health Check Routes
    Route::middleware(['auth:web'])->group(function () {
        Route::get('/health/booking-system', [\App\Http\Controllers\BookingHealthController::class, 'systemHealth']);
        Route::get('/health/booking-details/{id}', [\App\Http\Controllers\BookingHealthController::class, 'bookingDetails']);
        Route::get('/health/recent-bookings', [\App\Http\Controllers\BookingHealthController::class, 'recentBookingsStatus']);
    });
});


Route::get('/health/status', function () {
    return response()->json(['status' => 'ok']);
});

Route::middleware('web')->group(function () {
    if (app()->environment('local')) {
        Route::get('/session-data', [SessionController::class, 'showSessionData']);

        // Test SSR data structure
        Route::get('/test-ssr', function () {
            $ssrData = session('ssr_data', []);
            $passengers = session('passengers', []);

            return response()->json([
                'ssr_data' => $ssrData,
                'passengers' => $passengers,
                'session_keys' => array_keys(session()->all())
            ]);
        });

        // Test passenger data with SSR
        Route::get('/test-passengers', function () {
            $passengers = session('passengers', []);

            return response()->json([
                'passengers' => $passengers,
                'count' => count($passengers),
                'first_passenger' => $passengers[0] ?? null,
                'ssr_data_summary' => array_map(function($passenger, $index) {
                    return [
                        'index' => $index,
                        'name' => ($passenger['first_name'] ?? '') . ' ' . ($passenger['last_name'] ?? ''),
                        'selected_meal' => $passenger['selected_meal'] ?? 'none',
                        'meal_price' => $passenger['meal_price'] ?? 0,
                        'selected_baggage' => $passenger['selected_baggage'] ?? 'none',
                        'baggage_price' => $passenger['baggage_price'] ?? 0,
                    ];
                }, $passengers, array_keys($passengers))
            ]);
        });

        // Test booking error page
        Route::get('/test-booking-error', function () {
            return redirect('/booking-error')->with('error_message', 'This is a test booking error message. Payment was successful but booking failed.');
        });
    }
});

// Manual trigger for InProgress flight booking details check
Route::post('/admin/check-inprogress-flights/{pnr?}', [\App\Http\Controllers\AdminMaintenanceController::class, 'checkInprogressFlights'])
    ->middleware(['auth:admin', 'permission', 'throttle:admin-ops'])
    ->name('admin.check.inprogress.flights');
