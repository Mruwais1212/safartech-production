<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TBOCitySearchController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.tbo.api_url');
    }

    public function citySearch(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'CountryCode' => 'required|string|size:2',
            ]);

            // Check cache first
            $cacheKey = 'tbo_cities_'.$validatedData['CountryCode'];
            if (Cache::has($cacheKey)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cities retrieved from cache',
                    'data' => Cache::get($cacheKey),
                ]);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode(config('services.tbo.username').':'.config('services.tbo.password')),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl.'/CityList', [
                'CountryCode' => $validatedData['CountryCode'],
            ]);

            if ($response->successful()) {
                $cityData = $response->json();

                if (isset($cityData['Status']) && $cityData['Status']['Code'] == 200) {
                    $cities = $cityData['CityList'] ?? [];

                    // Cache the result
                    Cache::put($cacheKey, $cities, now()->addHours(24));

                    return response()->json([
                        'success' => true,
                        'message' => 'Cities retrieved successfully',
                        'data' => $cities,
                    ]);
                }
                Log::error('TBO City Search API Error', $cityData);

                return response()->json([
                    'success' => false,
                    'message' => $cityData['Status']['Description'] ?? 'Error retrieving cities',
                ], 400);
            }
            Log::error('TBO City Search HTTP Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to TBO API',
            ], $response->status());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('TBO City Search Exception: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching cities',
            ], 500);
        }
    }
}
