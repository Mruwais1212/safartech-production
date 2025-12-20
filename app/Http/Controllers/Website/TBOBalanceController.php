<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TBOBalanceController extends Controller
{
    private $apiUrl = 'http://xmloutapi.tboair.com/api/v1/Authenticate/ValidateAgency/GetAgencyBalance';

    private $username;

    private $password;

    private $threshold;

    public function __construct()
    {
        $this->username = Config::get('services.tbo.username');
        $this->password = Config::get('services.tbo.password');
        $this->threshold = Config::get('services.tbo.threshold');
    }

    public function checkWalletBalance(Request $request)
    {
        try {
            $response = Http::post($this->apiUrl, [
                'UserName' => $this->username,
                'Password' => $this->password,
                'BookingMode' => 'API',
                'EndUserIp' => $request->ip(),
            ]);

            if ($response->successful()) {
                $balanceData = $response->json();

                if ($balanceData['IsSuccess']) {
                    $balance = floatval($balanceData['TotalAvailableLimit']);
                    $currency = $balanceData['Currency'];

                    if ($balance >= $this->threshold) {
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Sufficient balance',
                            'balance' => $balance,
                            'currency' => $currency,
                            'use_tbo' => true,
                            'token_id' => $balanceData['TokenId'],
                        ]);
                    }

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Low balance. Switching to affiliate marketing.',
                        'balance' => $balance,
                        'currency' => $currency,
                        'use_tbo' => false,
                    ]);
                }

                throw new \Exception($balanceData['ErrorMessage'] ?? 'Unknown error occurred');
            } else {
                throw new \Exception('Failed to get balance from TBO');
            }
        } catch (\Exception $e) {
            Log::error('TBO Balance Check Error: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to check balance. Defaulting to affiliate marketing.',
                'use_tbo' => false,
                'error_code' => $balanceData['ErrorCode'] ?? null,
                'error_message' => $balanceData['ErrorMessage'] ?? $e->getMessage(),
            ], 500);
        }
    }

    public function switchToAffiliate()
    {
        // Logic to switch to affiliate marketing
        session(['use_affiliate' => true]);

        return response()->json(['message' => 'Switched to affiliate marketing']);
    }
}
