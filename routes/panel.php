<?php

use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

Route::group(['namespace' => 'Guest'], function () {
    Route::resource('login', 'LoginController')->only('index', 'store');
    Route::post('logout', 'LoginController@logout')->middleware(['auth:admin', 'throttle:admin-ops']);
});

Route::group(['namespace' => 'Auth'], function () {
    Route::get('logs', [LogViewerController::class, 'index'])->middleware('auth:admin');
    Route::get('/', 'HomeController@index');
    Route::get('activity', 'ActivityController@index');
    Route::get('edit-profile', 'HomeController@editProfile');
    Route::post('edit-profile', 'HomeController@updateProfile');
    Route::resource('main-slider', 'MainSliderController');
    Route::resource('slider', 'SliderController');
    Route::resource('contact', 'ContactController');
    Route::post('privilege-change-sort', 'PrivilegeController@changeSort');
    Route::post('privilege-change-status/{id}', 'PrivilegeController@changeStatus')->middleware(['auth:admin', 'permission', 'throttle:admin-ops']);
    Route::resource('privilege', 'PrivilegeController');
    Route::get('privilegeItems', 'PrivilegeController@privilegeItems');
    Route::resource('privilege-group', 'PrivilegeGroupController');
    Route::resource('setting-type', 'SettingTypeController');
    Route::resource('setting', 'SettingController');
    Route::resource('settings', 'SettingsController');
    Route::resource('fixed-page', 'FixedPageController');
    Route::resource('group-notification', 'GroupNotificationController')->only(['index', 'create', 'store']);
    Route::post('payment-settings/{id}/change-status', 'PaymentSettingController@changeStatus');
    Route::resource('payment-settings', 'PaymentSettingController');
});

Route::group(['namespace' => 'Backend'], function () {
    Route::resource('permission', 'PermissionController');
    Route::resource('supervisor', 'SupervisorController');
    Route::get('notifications', 'NotificationController@index');
    Route::resource('city', 'CityController');
    Route::resource('country', 'CountryController');
    Route::post('block-user/{id}', 'UserController@block');
    Route::resource('users', 'UserController');
    Route::resource('page-content', 'PageContentController');

    Route::resource('ai-planner-sections', 'AiPlannerContentController')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::get('balance', 'BalanceController@index');
    Route::get('add-balance', 'BalanceController@addBalance');
    Route::post('add-balance', 'BalanceController@addBalancePost');
    Route::get('deduct-balance', 'BalanceController@deductBalance');
    Route::post('deduct-balance', 'BalanceController@deductBalancePost');

    Route::resource('travel-interest', 'TravelInterestController');
    Route::resource('destination', 'DestinationController');
    Route::resource('cache-destination', 'CacheDestinationController')->only('index', 'store');
    Route::get('reservations', 'ReservationController@index');
    Route::post('delete-all-reservations', 'ReservationController@delete_all')->middleware(['auth:admin', 'permission', 'throttle:admin-ops']);
    Route::get('financial_reports', 'ReservationController@financial_reports');

    Route::get('hotel-reservations', 'ReservationController@hotel');
    Route::post('cancel-hotel-reservations/{id}', 'ReservationController@cancel_hotel')->middleware(['auth:admin', 'permission', 'throttle:admin-ops']);
    Route::get('flight-reservations', 'ReservationController@flight');
    Route::post('cancel-flight-reservations/{id}', 'ReservationController@cancel_flight')->middleware(['auth:admin', 'permission', 'throttle:admin-ops']);
    Route::get('hotel-and-flight-reservations', 'ReservationController@hotelAndFlight');

    Route::get('operations', 'OperationController@index');

    Route::get('refunds', 'RefundController@index');
    Route::get('generate-invoice/{id}', [\App\Http\Controllers\InvoiceController::class, 'generateInvoice'])->middleware(['auth:admin', 'permission']);

    Route::get('cached-hotels', 'CacheHotelController@index');
    Route::post('allhotels', 'CacheHotelController@allhotels');
    Route::get('cached-hotels/{id}/edit', 'CacheHotelController@edit');
    Route::put('cached-hotels/{id}', 'CacheHotelController@update');
    Route::post('delete-image-cached-hotels/{id}', 'CacheHotelController@deleteImage');
    Route::get('tbo-flight-balance', [\App\Http\Controllers\Panel\Auth\HomeController::class, 'getTboFlightBalance']);
});
