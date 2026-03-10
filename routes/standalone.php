<?php

/**
 * Standalone routes — loaded once with web middleware and NO namespace override.
 * These use fully-qualified class names (FQCN) and must NOT be inside any
 * ->namespace('App\Http\Controllers\Website') group to avoid double-prefixing.
 */

use App\Http\Controllers\AdminMaintenanceController;
use App\Http\Controllers\BookingHealthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Website\SessionController;
use App\Http\Controllers\Website\TripController;
use App\Http\Controllers\Webhooks\MoyasarWebhookController;
use Illuminate\Support\Facades\Route;

// Payment webhook (CSRF excluded in bootstrap/app.php)
Route::post('webhooks/moyasar', MoyasarWebhookController::class)
    ->middleware('throttle:moyasar-webhook');

// Invoice generation
Route::get('/generate-invoice/{id}', [InvoiceController::class, 'generateInvoice'])
    ->middleware(['web', 'auth:web']);

// Booking health checks (authenticated)
Route::middleware(['web', 'auth:web'])->group(function () {
    Route::get('/health/booking-system', [BookingHealthController::class, 'systemHealth']);
    Route::get('/health/booking-details/{id}', [BookingHealthController::class, 'bookingDetails']);
    Route::get('/health/recent-bookings', [BookingHealthController::class, 'recentBookingsStatus']);
});

// Admin maintenance (admin guard + permission + rate limit)
Route::post('/admin/check-inprogress-flights/{pnr?}', [AdminMaintenanceController::class, 'checkInprogressFlights'])
    ->middleware(['web', 'auth:admin', 'permission', 'throttle:admin-ops'])
    ->name('admin.check.inprogress.flights');

// Simple status check (no auth)
Route::get('/health/status', fn () => response()->json(['status' => 'ok']));

// Trip search results (uses FQCN to avoid namespace collision)
Route::middleware('web')->get('/search-results', [TripController::class, 'showResults'])
    ->name('search.results');

// Dev-only debug routes
Route::middleware('web')->group(function () {
    if (app()->environment('local')) {
        Route::get('/session-data', [SessionController::class, 'showSessionData']);

        Route::get('/test-ssr', function () {
            return response()->json([
                'ssr_data'    => session('ssr_data', []),
                'passengers'  => session('passengers', []),
                'session_keys' => array_keys(session()->all()),
            ]);
        });

        Route::get('/test-passengers', function () {
            $passengers = session('passengers', []);
            return response()->json([
                'passengers'     => $passengers,
                'count'          => count($passengers),
                'first_passenger' => $passengers[0] ?? null,
            ]);
        });

        Route::get('/test-booking-error', function () {
            return redirect('/booking-error')->with('error_message', 'Test booking error message.');
        });

        Route::get('/flight-details', [\App\Http\Controllers\Website\MyTripController::class, 'flight_test']);
    }
});
