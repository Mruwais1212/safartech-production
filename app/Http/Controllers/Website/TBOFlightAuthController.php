<?php

namespace App\Http\Controllers\Website;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TBOFlightAuthController extends Controller
{
    private $baseUrl;

    private $username;

    private $password;

    private $maxRetries = 3;

    public function __construct()
    {
        $this->baseUrl = config('services.tbo.flight_api_url');
        $this->username = config('services.tbo.username');
        $this->password = config('services.tbo.password');
    }

    public function authenticate()
    {
        $cacheKey = 'tbo_flight_token';

        // Check if a valid token is already in cache
        if (Cache::has($cacheKey)) {
            return response()->json([
                'success' => true,
                'message' => 'Token retrieved from cache',
                'token' => Cache::get($cacheKey),
            ]);
        }

        try {
            $response = $this->makeAuthenticationCall();

            $result = $response->json();

            if (isset($result['TokenId'])) {
                // Store the token in cache (valid until end of day)
                Cache::put($cacheKey, $result['TokenId'], now()->endOfDay());
                session(['traceId' => $result['TrackingId']]);

                return response()->json([
                    'success' => true,
                    'message' => 'Authentication successful',
                    'token' => $result['TokenId'],
                ]);
            }
            Log::error('TBO Authentication Error', ['response' => $result]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to authenticate with TBO API',
            ], 401);
        } catch (\Exception $e) {
            Log::error('TBO Authentication Exception: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during authentication',
            ], 500);
        }
    }

    private function makeAuthenticationCall()
    {
        $attempt = 0;
        while ($attempt < $this->maxRetries) {
            try {
                $response = Http::post($this->baseUrl.'/Authenticate', [
                    'Username' => $this->username,
                    'Password' => $this->password,
                ]);

                if ($response->successful()) {
                    return $response;
                }
            } catch (\Exception $e) {
                Log::warning("Authentication attempt {$attempt} failed: ".$e->getMessage());
            }
            $attempt++;
            if ($attempt < $this->maxRetries) {
                sleep(pow(2, $attempt)); // Exponential backoff
            }
        }

        throw new \Exception("Authentication failed after {$this->maxRetries} attempts");
    }
}
