<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TBOPreBookController extends Controller
{
    private $baseUrl;

    private $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.tbo.api_url');
        $this->timeout = config('services.tbo.timeout', 30);
    }

    public function preBook(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'BookingCode' => 'required|string|max:255',
                'PaymentMode' => ['required', Rule::in(['Limit', 'SavedCard', 'NewCard'])],
                'IdempotencyKey' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validatedData = $validator->validated();

            $response = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode(config('services.tbo.username').':'.config('services.tbo.password')),
                'Content-Type' => 'application/json',
                'Idempotency-Key' => $validatedData['IdempotencyKey'],
            ])->timeout($this->timeout)->post($this->baseUrl.'/PreBook', $validatedData);

            if ($response->successful()) {
                $preBookData = $response->json();

                if (isset($preBookData['Status']) && $preBookData['Status']['Code'] == 200) {
                    Log::info('TBO PreBook Successful', ['BookingCode' => $validatedData['BookingCode']]);
                    return response()->json([
                        'success' => true,
                        'message' => 'PreBook successful',
                        'data' => $preBookData['HotelResult'] ?? [],
                    ]);
                }
                Log::error('TBO PreBook API Error', $preBookData);

                return response()->json([
                    'success' => false,
                    'message' => $preBookData['Status']['Description'] ?? 'Error during PreBook',
                ], 400);
            }
            Log::error('TBO PreBook HTTP Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to TBO API',
            ], $response->status());
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('TBO PreBook Connection Exception: '.$e->getMessage(), [
                'exception_class' => get_class($e),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection to TBO API timed out. Please try again later.',
            ], 504);
        } catch (\Exception $e) {
            Log::error('TBO PreBook Unexpected Exception: '.$e->getMessage(), [
                'exception_class' => get_class($e),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred during PreBook',
            ], 500);
        }
    }
}
