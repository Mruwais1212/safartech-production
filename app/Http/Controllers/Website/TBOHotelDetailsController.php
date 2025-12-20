<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TBOHotelDetailsController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.tbo.api_url');
    }

    public function hotelDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'Hotelcodes' => 'required|string|max:255',
                'Language' => ['required', 'string', 'size:2', Rule::in(['en', 'ar', 'fr', 'de', 'es'])], // Add more supported languages as needed
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validatedData = $validator->validated();

            $cacheKey = 'tbo_hotel_details_'.md5($validatedData['Hotelcodes'].$validatedData['Language']);

            if (Cache::has($cacheKey)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Hotel details retrieved from cache',
                    'data' => Cache::get($cacheKey),
                ]);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode(config('services.tbo.username').':'.config('services.tbo.password')),
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl.'/Hoteldetails', $validatedData);

            if ($response->successful()) {
                $hotelData = $response->json();

                if (isset($hotelData['Status']) && $hotelData['Status']['Code'] == 200) {
                    $details = $hotelData['HotelDetails'] ?? [];

                    Cache::put($cacheKey, $details, now()->addHours(24));

                    return response()->json([
                        'success' => true,
                        'message' => 'Hotel details retrieved successfully',
                        'data' => $details,
                    ]);
                }
                Log::error('TBO Hotel Details API Error', $hotelData);

                return response()->json([
                    'success' => false,
                    'message' => $hotelData['Status']['Description'] ?? 'Error retrieving hotel details',
                ], 400);
            }
            Log::error('TBO Hotel Details HTTP Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to TBO API',
            ], $response->status());
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('TBO Hotel Details Connection Exception: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection to TBO API timed out. Please try again later.',
            ], 504);
        } catch (\Exception $e) {
            Log::error('TBO Hotel Details Unexpected Exception: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while retrieving hotel details',
            ], 500);
        }
    }
}
