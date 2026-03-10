<?php

/**
 * Locale-namespaced website routes.
 * This file is loaded TWICE by bootstrap/app.php:
 *   1. Via web: key  — without prefix/namespace (provides non-locale URLs)
 *   2. Via then: callback — with locale prefix + App\Http\Controllers\Website namespace
 *
 * Rules:
 *   - ONLY use string-based controller references ('HomeController@index').
 *   - Do NOT use FQCN (ClassName::class) here — those belong in routes/standalone.php.
 */

use Illuminate\Support\Facades\Route;

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

    Route::get('saved-trips', 'HomeController@savedTrips');
    Route::get('trip-details/{id}', 'HomeController@tripDetails');

    Route::resource('passenger-information', 'PassengerInformationController');
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
});
