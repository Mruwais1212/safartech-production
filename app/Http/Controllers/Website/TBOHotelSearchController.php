<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TBOHotelSearchController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.tbo.api_url');
    }

    public function hotelSearch(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'CheckIn' => 'required|date|after:today',
                'CheckOut' => 'required|date|after:CheckIn',
                'CityCode' => 'required|string',
                'HotelCodes' => 'nullable|string',
                'GuestNationality' => 'required|string|size:2',
                'PaxRooms' => 'required|array|min:1',
                'PaxRooms.*.Adults' => 'required|integer|min:1|max:4',
                'PaxRooms.*.Children' => 'integer|min:0|max:3',
                'PaxRooms.*.ChildrenAges' => 'array|required_if:PaxRooms.*.Children,>,0',
                'PaxRooms.*.ChildrenAges.*' => 'integer|min:0|max:17',
                'ResponseTime' => 'required|integer|min:1|max:30',
                'IsDetailedResponse' => 'boolean',
                'Filters.Refundable' => 'boolean',
                'Filters.MealType' => ['nullable', Rule::in(['All', 'WithMeal', 'RoomOnly'])],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validatedData = $validator->validated();

            // Generate a unique cache key
            $cacheKey = 'tbo_hotel_search_'.md5(json_encode($validatedData));

            // Check cache
            if (Cache::has($cacheKey)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Hotel search results retrieved from cache',
                    'data' => Cache::get($cacheKey),
                ]);
            }

            // Make API call
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode(config('services.tbo.username').':'.config('services.tbo.password')),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl.'/search', $validatedData);

            if ($response->successful()) {
                $searchData = $response->json();

                if (isset($searchData['Status']) && $searchData['Status']['Code'] == 200) {
                    $hotels = $searchData['HotelResult'] ?? [];

                    // Cache the result for a short period (e.g., 5 minutes)
                    Cache::put($cacheKey, $hotels, now()->addMinutes(5));

                    return response()->json([
                        'success' => true,
                        'message' => 'Hotel search completed successfully',
                        'data' => $hotels,
                    ]);
                }
                Log::error('TBO Hotel Search API Error', $searchData);

                return response()->json([
                    'success' => false,
                    'message' => $searchData['Status']['Description'] ?? 'Error performing hotel search',
                ], 400);
            }
            Log::error('TBO Hotel Search HTTP Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to TBO API',
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('TBO Hotel Search Exception: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while searching for hotels',
            ], 500);
        }
    }
}
