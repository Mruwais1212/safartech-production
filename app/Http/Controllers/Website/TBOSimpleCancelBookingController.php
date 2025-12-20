<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TBOSimpleCancelBookingController extends Controller
{
    public function cancelBooking(Request $request)
    {
        $request->validate([
            'ConfirmationNumber' => 'required|string',
        ]);

        $username = config('services.tbo.username');
        $password = config('services.tbo.password');
        $apiUrl = config('services.tbo.api_url').'/Cancel';

        $response = Http::withHeaders([
            'Authorization' => 'Basic '.base64_encode($username.':'.$password),
            'Content-Type' => 'application/json',
        ])->post($apiUrl, [
            'ConfirmationNumber' => $request->ConfirmationNumber,
        ]);

        $cancelResponse = $response->json();

        return response()->json($cancelResponse);
    }
}
