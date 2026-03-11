<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TBOBookingDetailController extends Controller
{
    private $baseUrl;

    private $timeout;

    private $cacheDuration;

    public function __construct()
    {
        $this->baseUrl = config('services.tbo.api_url');
        $this->timeout = config('services.tbo.timeout', 30);
        $this->cacheDuration = config('services.tbo.booking_detail_cache_duration', 5); // in minutes
    }

    public function getBookingDetail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ConfirmationNumber' => 'required_without:BookingReferenceId|string|max:255',
                'BookingReferenceId' => 'required_without:ConfirmationNumber|string|max:255',
                'PaymentMode' => ['required', Rule::in(['Limit', 'SavedCard', 'NewCard'])],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validatedData = $validator->validated();

            $cacheKey = 'tbo_booking_detail_'.md5(json_encode($validatedData));

            if (Cache::has($cacheKey)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking details retrieved from cache',
                    'data' => Cache::get($cacheKey),
                ]);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode(config('services.tbo.username').':'.config('services.tbo.password')),
                'Content-Type' => 'application/json',
            ])->timeout($this->timeout)->post($this->baseUrl.'/BookingDetail', $validatedData);

            if ($response->successful()) {
                $bookingDetailData = $response->json();

                if (isset($bookingDetailData['Status']) && $bookingDetailData['Status']['Code'] == 200) {
                    Cache::put($cacheKey, $bookingDetailData['BookingDetail'], now()->addMinutes($this->cacheDuration));

                    Log::info('TBO Booking Detail Retrieved', [
                        'ConfirmationNumber' => $validatedData['ConfirmationNumber'] ?? null,
                        'BookingReferenceId' => $validatedData['BookingReferenceId'] ?? null,
                        'BookingStatus' => $bookingDetailData['BookingDetail']['BookingStatus'] ?? null,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Booking details retrieved successfully',
                        'data' => $bookingDetailData['BookingDetail'],
                    ]);
                }
                Log::error('TBO Booking Detail API Error', $bookingDetailData);

                return response()->json([
                    'success' => false,
                    'message' => $bookingDetailData['Status']['Description'] ?? 'Error retrieving booking details',
                    'error_code' => $bookingDetailData['Status']['Code'] ?? null,
                ], 400);
            }
            Log::error('TBO Booking Detail HTTP Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to TBO API. Please try again later.',
                'error_code' => $response->status(),
            ], $response->status());
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('TBO Booking Detail Connection Exception: '.$e->getMessage(), [
                'exception_class' => get_class($e),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection to TBO API timed out. Please try again later.',
                'error_code' => 'CONNECTION_TIMEOUT',
            ], 504);
        } catch (\Exception $e) {
            Log::error('TBO Booking Detail Unexpected Exception: '.$e->getMessage(), [
                'exception_class' => get_class($e),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while retrieving booking details. Please try again later.',
                'error_code' => 'UNEXPECTED_ERROR',
            ], 500);
        }
    }
}
