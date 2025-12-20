<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TBOFlightSearchController extends Controller
{
    private $baseUrl;

    private $maxRetries = 3;

    private $authController;

    public function __construct(TBOFlightAuthController $authController)
    {
        $this->baseUrl = config('services.tbo.flight_api_url');
        $this->authController = $authController;
    }

    public function search(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'Origin' => 'required|string|size:3',
                'Destination' => 'required|string|size:3',
                'DepartureDate' => 'required|date|after:today',
                'ReturnDate' => 'nullable|date|after:DepartureDate',
                'AdultCount' => 'required|integer|min:1|max:9',
                'ChildCount' => 'nullable|integer|min:0|max:6',
                'InfantCount' => 'nullable|integer|min:0|max:3',
                'Class' => ['required', Rule::in(['Economy', 'PremiumEconomy', 'Business', 'First'])],
                'PreferredAirlines' => 'nullable|array',
                'PreferredAirlines.*' => 'string|size:2',
                'NonStop' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validatedData = $validator->validated();

            // Generate a unique cache key based on the search parameters
            $cacheKey = 'flight_search_'.md5(json_encode($validatedData));

            // Check if the result is already in cache
            if (Cache::has($cacheKey)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Flight search results retrieved from cache',
                    'data' => Cache::get($cacheKey),
                ]);
            }

            // Get the authentication token
            $authResponse = $this->authController->authenticate();
            if (! $authResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication failed',
                ], 401);
            }
            $token = $authResponse['token'];

            // Prepare the search request payload
            $searchPayload = [
                'Origin' => $validatedData['Origin'],
                'Destination' => $validatedData['Destination'],
                'DepartureDate' => $validatedData['DepartureDate'],
                'ReturnDate' => $validatedData['ReturnDate'] ?? null,
                'AdultCount' => $validatedData['AdultCount'],
                'ChildCount' => $validatedData['ChildCount'] ?? 0,
                'InfantCount' => $validatedData['InfantCount'] ?? 0,
                'JourneyType' => isset($validatedData['ReturnDate']) ? 'Return' : 'OneWay',
                'PreferredAirlines' => $validatedData['PreferredAirlines'] ?? [],
                'Class' => $validatedData['Class'],
                'NonStop' => $validatedData['NonStop'] ?? false,
            ];

            $response = $this->makeApiCall('/Search', $searchPayload, $token);

            $processedResponse = $this->processApiResponse($response, 'Flight Search');

            if ($processedResponse['success']) {
                // Cache the successful response for 5 minutes
                Cache::put($cacheKey, $processedResponse['data'], now()->addMinutes(5));
            }

            return response()->json($processedResponse, $processedResponse['success'] ? 200 : 400);
        } catch (\Exception $e) {
            Log::error('Flight Search Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred during the flight search.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function makeApiCall($endpoint, $data, $token)
    {
        $attempt = 0;
        while ($attempt < $this->maxRetries) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($this->baseUrl.$endpoint, $data);

                if ($response->successful()) {
                    return $response;
                }
            } catch (\Exception $e) {
                Log::warning("API call attempt {$attempt} failed: ".$e->getMessage());
            }
            $attempt++;
            if ($attempt < $this->maxRetries) {
                sleep(pow(2, $attempt)); // Exponential backoff
            }
        }

        throw new \Exception("API call failed after {$this->maxRetries} attempts");
    }

    private function processApiResponse($response, $operationType)
    {
        $responseData = $response->json();
        $success = isset($responseData['Response']['ResponseStatus']) && $responseData['Response']['ResponseStatus'] == 1;

        if ($success) {
            Log::info("TBO $operationType Successful", $this->sanitizeLogData($responseData));

            return [
                'success' => true,
                'message' => "$operationType successful",
                'data' => $responseData['Response']['Results'] ?? [],
            ];
        }
        Log::error("TBO $operationType Error", $this->sanitizeLogData($responseData));

        return [
            'success' => false,
            'message' => $responseData['Response']['Error']['ErrorMessage'] ?? "Error during $operationType",
            'error_code' => $responseData['Response']['Error']['ErrorCode'] ?? null,
        ];
    }

    private function sanitizeLogData($data)
    {
        $sensitiveKeys = ['password', 'token', 'api_key'];

        array_walk_recursive($data, function (&$value, $key) use ($sensitiveKeys) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $value = '[REDACTED]';
            }
        });

        return $data;
    }
}
