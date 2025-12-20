<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TBOFlightBookingController extends Controller
{
    private $baseUrl;

    private $maxRetries = 3;

    private $authController;

    public function __construct(TBOFlightAuthController $authController)
    {
        $this->baseUrl = config('services.tbo.flight_api_url');
        $this->authController = $authController;
    }

    public function bookFlight(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'TraceId' => 'required|string',
                'ResultIndex' => 'required|integer',
                'Passengers' => 'required|array|min:1',
                'Passengers.*.Title' => ['required', Rule::in(['Mr', 'Mrs', 'Ms', 'Mstr', 'Miss'])],
                'Passengers.*.FirstName' => 'required|string|max:50',
                'Passengers.*.LastName' => 'required|string|max:50',
                'Passengers.*.PaxType' => ['required', Rule::in(['Adult', 'Child', 'Infant'])],
                'Passengers.*.DateOfBirth' => 'required|date|before:today',
                'Passengers.*.Gender' => ['required', Rule::in(['Male', 'Female'])],
                'Passengers.*.PassportNo' => 'required|string|max:20',
                'Passengers.*.PassportExpiry' => 'required|date|after:today',
                'Passengers.*.AddressLine1' => 'required|string|max:100',
                'Passengers.*.City' => 'required|string|max:50',
                'Passengers.*.CountryCode' => 'required|string|size:2',
                'ContactDetails' => 'required|array',
                'ContactDetails.Email' => 'required|email',
                'ContactDetails.PhoneNumber' => 'required|string|max:20',
                'PaymentDetails' => 'required|array',
                'PaymentDetails.PaymentMode' => ['required', Rule::in(['CreditCard', 'DebitCard', 'NetBanking'])],
                'PaymentDetails.CardNumber' => 'required_if:PaymentDetails.PaymentMode,CreditCard,DebitCard|string|max:16',
                'PaymentDetails.CardHolderName' => 'required_if:PaymentDetails.PaymentMode,CreditCard,DebitCard|string|max:50',
                'PaymentDetails.ExpiryMonth' => 'required_if:PaymentDetails.PaymentMode,CreditCard,DebitCard|integer|between:1,12',
                'PaymentDetails.ExpiryYear' => 'required_if:PaymentDetails.PaymentMode,CreditCard,DebitCard|integer|min:'.date('Y'),
                'PaymentDetails.CVV' => 'required_if:PaymentDetails.PaymentMode,CreditCard,DebitCard|string|size:3',
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

            // Prepare the booking request payload
            $bookingPayload = [
                'TraceId' => $validatedData['TraceId'],
                'ResultIndex' => $validatedData['ResultIndex'],
                'Passengers' => $validatedData['Passengers'],
                'ContactDetails' => $validatedData['ContactDetails'],
                'PaymentDetails' => $this->sanitizePaymentDetails($validatedData['PaymentDetails']),
            ];

            $response = $this->makeApiCall('/Book', $bookingPayload, $token);

            return $this->processApiResponse($response, 'Flight Booking');
        } catch (\Exception $e) {
            Log::error('Flight Booking Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred during the flight booking process.',
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
                ])->timeout(60)->post($this->baseUrl.$endpoint, $data);

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
                'data' => $this->formatBookingResponse($responseData['Response']['Results'] ?? []),
            ]);
        }
        Log::error("TBO $operationType Error", $this->sanitizeLogData($responseData));

        return response()->json([
            'success' => false,
            'message' => $responseData['Response']['Error']['ErrorMessage'] ?? "Error during $operationType",
            'error_code' => $responseData['Response']['Error']['ErrorCode'] ?? null,
        ], 400);
    }

    private function formatBookingResponse($bookingData)
    {
        return [
            'BookingId' => $bookingData['BookingId'] ?? null,
            'PNR' => $bookingData['PNR'] ?? null,
            'BookingStatus' => $bookingData['BookingStatus'] ?? null,
            'TicketStatus' => $bookingData['TicketStatus'] ?? null,
            'TotalFare' => $bookingData['TotalFare'] ?? null,
            'Currency' => $bookingData['Currency'] ?? null,
        ];
    }

    private function sanitizePaymentDetails($paymentDetails)
    {
        // Remove sensitive data before sending to API
        if (isset($paymentDetails['CardNumber'])) {
            $paymentDetails['CardNumber'] = substr($paymentDetails['CardNumber'], -4);
        }
        if (isset($paymentDetails['CVV'])) {
            unset($paymentDetails['CVV']);
        }

        return $paymentDetails;
    }

    private function sanitizeLogData($data)
    {
        $sensitiveKeys = ['password', 'token', 'api_key', 'cardnumber', 'cvv'];

        array_walk_recursive($data, function (&$value, $key) use ($sensitiveKeys) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $value = '[REDACTED]';
            }
        });

        return $data;
    }
}
