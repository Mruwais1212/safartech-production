<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TBOBookController extends Controller
{
    private $baseUrl;

    private $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.tbo.api_url');
        $this->timeout = config('services.tbo.timeout', 60);
    }

    public function book(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'BookingCode' => 'required|string|max:255',
                'CustomerDetails' => 'required|array',
                'CustomerDetails.*.CustomerNames' => 'required|array',
                'CustomerDetails.*.CustomerNames.*.Title' => ['required', Rule::in(['Mr', 'Mrs', 'Ms'])],
                'CustomerDetails.*.CustomerNames.*.FirstName' => 'required|string|max:50',
                'CustomerDetails.*.CustomerNames.*.LastName' => 'required|string|max:50',
                'CustomerDetails.*.CustomerNames.*.Type' => ['required', Rule::in(['Adult', 'Child'])],
                'ClientReferenceId' => 'required|string|max:255',
                'BookingReferenceId' => 'required|string|max:255',
                'TotalFare' => 'required|numeric|min:0',
                'EmailId' => 'required|email',
                'PhoneNumber' => 'required|string|max:20',
                'BookingType' => 'required|in:Voucher',
                'PaymentMode' => ['required', Rule::in(['Limit', 'SavedCard', 'NewCard'])],
                'PaymentInfo' => 'required_if:PaymentMode,NewCard,SavedCard|array',
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

            // Remove sensitive data from logging
            $logSafeData = $validatedData;
            unset($logSafeData['PaymentInfo']);

            $response = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode(config('services.tbo.username').':'.config('services.tbo.password')),
                'Content-Type' => 'application/json',
                'Idempotency-Key' => $validatedData['IdempotencyKey'],
            ])->timeout($this->timeout)->post($this->baseUrl.'/Book', $validatedData);

            if ($response->successful()) {
                $bookingData = $response->json();

                if (isset($bookingData['Status']) && $bookingData['Status']['Code'] == 200) {
                    Log::info('TBO Booking Successful', [
                        'BookingCode' => $validatedData['BookingCode'],
                        'ClientReferenceId' => $validatedData['ClientReferenceId'],
                        'ConfirmationNumber' => $bookingData['ConfirmationNumber'] ?? null,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Booking successful',
                        'data' => [
                            'ConfirmationNumber' => $bookingData['ConfirmationNumber'] ?? null,
                            'BookingDetails' => $bookingData['BookingDetails'] ?? [],
                        ],
                    ]);
                }
                Log::error('TBO Booking API Error', array_merge(['error' => $bookingData['Status'] ?? 'Unknown error'], $logSafeData));

                return response()->json([
                    'success' => false,
                    'message' => $bookingData['Status']['Description'] ?? 'Error during booking',
                ], 400);
            }
            Log::error('TBO Booking HTTP Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'request' => $logSafeData,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to TBO API',
            ], $response->status());
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('TBO Booking Connection Exception: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['PaymentInfo']),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection to TBO API timed out. Please check booking status before retrying.',
            ], 504);
        } catch (\Exception $e) {
            Log::error('TBO Booking Unexpected Exception: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['PaymentInfo']),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred during booking. Please check booking status.',
            ], 500);
        }
    }
}
