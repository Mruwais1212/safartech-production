<?php


use App\Http\Controllers\Website\TripController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Website\SessionController;

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

Route::group(['middleware' => ['web']], function () {
    Route::get('/', 'HomeController@index');
    Route::resource('ai-trip', 'AiTripController')->only('index', 'store');
    Route::post('search-plans', 'AiTripController@searchPlans');
    Route::post('search-plans-new', 'AiTripController@searchPlansNew');

    Route::get('ai-trip-planner', 'AiTripPlannerController@index');
    Route::get('latest-ai-trip-planner', 'MyLatestAiTripController@index')->middleware('auth:web');
    Route::get('latest-ai-trip-planner/{id}', 'MyLatestAiTripController@show')->middleware('auth:web');
    Route::get('ai-trip-latest-planner/{id}', 'MyLatestAiTripController@data')->middleware('auth:web');
    Route::post('ai-trip-results', 'AiTripPlannerController@show');
    Route::post('ai-trip-planner', 'AiTripPlannerController@store');
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
    Route::post('payment', 'PaymentController@payment');
    Route::get('success', 'PaymentController@success');
    Route::get('payment-moyasar', 'PaymentController@payment');
    Route::get('moyasar-callback', 'PaymentController@moyasarCallback');
    Route::get('moyasar-success', 'PaymentController@moyasarSuccess');
    Route::get('booking-error', 'PaymentController@bookingError');

    Route::get('flights', 'FlightController@index');
    Route::post('search-flights', 'FlightController@search');
    Route::post('search-flights-new', 'FlightController@searchNew');
    Route::get('select-flight', 'FlightController@selectFlight');
    Route::get('cancel-flight/{pnr}', 'FlightController@cancel_flight');

    Route::get('hotels', 'HotelController@index');
    Route::get('hotel/{id}', 'HotelController@show');
    Route::post('search-hotels', 'HotelController@search');
    Route::post('search-hotels-new', 'HotelController@searchNew');
    Route::get('select-room/{id}', 'HotelController@selectRoom');
    Route::get('choose/{id}', 'HotelController@choose');
    Route::get('search-airport', 'HotelController@searchAirport');

    Route::get('search-city', 'HotelController@searchCity');
    Route::get('search-airport-city', 'HotelController@searchAirportCity');
    Route::get('cancel-hotel/{booking_code}', 'HotelController@cancel_hotel');

    Route::post('tbo-hotel', 'TBOHotelDetailsController@hotelDetails');

    Route::get('/generate-invoice/{id}', [InvoiceController::class, 'generateInvoice']);

    Route::get('/session-data', [SessionController::class, 'showSessionData']);
    Route::get('/flight-details',[\App\Http\Controllers\Website\MyTripController::class,'flight_test']);
    
    // Booking Details Health Check Routes
    Route::get('/health/booking-system', [\App\Http\Controllers\BookingHealthController::class, 'systemHealth']);
    Route::get('/health/booking-details/{id}', [\App\Http\Controllers\BookingHealthController::class, 'bookingDetails']);
    Route::get('/health/recent-bookings', [\App\Http\Controllers\BookingHealthController::class, 'recentBookingsStatus']);
    Route::get('/health/status', function() {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => '1.0',
            'service' => 'booking-details-monitoring'
        ]);
    });
});

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

// Manual trigger for InProgress flight booking details check
Route::get('/admin/check-inprogress-flights/{pnr?}', function ($pnr = null) {
    if (!auth('web')->user()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    try {
        $query = \App\Models\ReservationFlight::query();
        
        if ($pnr) {
            $query->where('pnr', $pnr);
            $flights = $query->get();
            
            if ($flights->isEmpty()) {
                return response()->json(['error' => "No flights found with PNR: {$pnr}"], 404);
            }
        } else {
            $flights = $query->where('is_in_progress', true)
                           ->where('status', 5)
                           ->get();
            
            if ($flights->isEmpty()) {
                return response()->json(['message' => 'No InProgress flights to check'], 200);
            }
        }
        
        $dispatched = [];
        foreach ($flights as $flight) {
            \App\Jobs\FetchFlightBookingDetailsJob::dispatch($flight->id, $flight->pnr, $flight->tbo_booking_id, 0);
            
            $dispatched[] = [
                'flight_id' => $flight->id,
                'pnr' => $flight->pnr,
                'booking_id' => $flight->tbo_booking_id,
                'status' => $flight->status
            ];
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Dispatched ' . count($dispatched) . ' flight booking detail jobs',
            'flights' => $dispatched
        ]);
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Manual InProgress flight check error', [
            'pnr' => $pnr,
            'error' => $e->getMessage()
        ]);
        
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('admin.check.inprogress.flights');
