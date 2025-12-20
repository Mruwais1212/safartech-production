<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TBOFlightCancelBookingController extends Controller
{
    private $baseUrl;

    private $maxRetries = 3;

    private $authController;

    public function __construct(TBOFlightAuthController $authController)
    {
        $this->baseUrl = config('services.tbo.flight_api_url');
        $this->authController = $authController;
    }

    public function cancelBooking(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'BookingId' => 'required|string',
                'CancellationType' => 'required|in:Full,Partial',
                'PaxIndexes' => 'required_if:CancellationType,Partial|array',
                'PaxIndexes.*' => 'integer',
                'Remarks' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validatedData = $validator->validated();

            // Get the authentication token
            $authResponse = $this->authController->authenticate();
            if (! $authResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication failed',
                ], 401);
            }
            $token = $authResponse['token'];

            // Prepare the cancellation request payload
            $cancellationPayload = [
                'BookingId' => $validatedData['BookingId'],
                'CancellationType' => $validatedData['CancellationType'],
                'PaxIndexes' => $validatedData['PaxIndexes'] ?? [],
                'Remarks' => $validatedData['Remarks'] ?? '',
            ];

            $response = $this->makeApiCall('/Cancel', $cancellationPayload, $token);

            return $this->processApiResponse($response, 'Booking Cancellation');
        } catch (\Exception $e) {
            Log::error('Booking Cancellation Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred during the booking cancellation process.',
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

            return response()->json([
                'success' => true,
                'message' => "$operationType completed successfully",
                'data' => $this->formatCancellationResponse($responseData['Response']['Results'] ?? []),
            ]);
        }
        Log::error("TBO $operationType Error", $this->sanitizeLogData($responseData));

        return response()->json([
            'success' => false,
            'message' => $responseData['Response']['Error']['ErrorMessage'] ?? "Error during $operationType",
            'error_code' => $responseData['Response']['Error']['ErrorCode'] ?? null,
        ], 400);
    }

    private function formatCancellationResponse($cancellationData)
    {
        return [
            'CancellationId' => $cancellationData['CancellationId'] ?? null,
            'Status' => $cancellationData['Status'] ?? null,
            'RefundAmount' => $cancellationData['RefundAmount'] ?? null,
            'CancellationCharge' => $cancellationData['CancellationCharge'] ?? null,
            'Currency' => $cancellationData['Currency'] ?? null,
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
