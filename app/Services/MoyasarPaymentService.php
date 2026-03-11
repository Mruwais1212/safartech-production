<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoyasarPaymentService
{
    public static function createInvoice($data)
    {
        try {
            $response = Http::withBasicAuth(config('services.moyasar.secret_key'), '')
                ->timeout(10)
                ->retry(2, 200)
                ->post(
                    config('services.moyasar.base_url').'/v1/invoices',
                    [
                        'amount' => (int) (number_format($data['amount'] * 100, 2, '.', '')),
                        'currency' => $data['currency'],
                        'description' => $data['description'],
                        'callback_url' => $data['callback_url'],
                        'metadata' => $data['metadata'],
                        'success_url' => $data['success_url'],
                    ]
                );

            return $response->json();
        } catch (\Throwable $th) {
            Log::error('Moyasar createInvoice request failed', [
                'correlation_id' => $data['correlation_id'] ?? null,
                'reservation_id' => $data['reservation_id'] ?? null,
                'payment_id' => null,
            ]);

            return ['error' => $th->getMessage(), 'status' => 400, 'message' => 'Error'];
        }
    }

    public static function getInvoice($id, ?string $correlationId = null, $reservationId = null)
    {
        try {
            $response = Http::withBasicAuth(config('services.moyasar.secret_key'), '')
                ->timeout(10)
                ->retry(2, 200)
                ->get(config('services.moyasar.base_url').'/v1/payments/'.$id);

            return $response->json();
        } catch (\Throwable $th) {
            Log::error('Moyasar getInvoice request failed', [
                'correlation_id' => $correlationId,
                'reservation_id' => $reservationId,
                'payment_id' => $id,
            ]);

            return ['error' => $th->getMessage(), 'status' => 400, 'message' => 'Error'];
        }
    }

    public static function refund($id, $amount, ?string $correlationId = null, $reservationId = null)
    {
        try {
            $response = Http::withBasicAuth(config('services.moyasar.secret_key'), '')
                ->timeout(10)
                ->retry(2, 200)
                ->post(
                    config('services.moyasar.base_url').'/v1/payments/'.$id.'/refund',
                    [
                        // Moyasar expects amount in smallest currency unit (halala for SAR).
                        // $amount is in SAR (decimal), so multiply by 100 — same conversion
                        // used in createInvoice().
                        'amount' => (int) (number_format($amount * 100, 2, '.', '')),
                    ]
                );

            return $response->json();
        } catch (\Throwable $th) {
            Log::error('Moyasar refund request failed', [
                'correlation_id' => $correlationId,
                'reservation_id' => $reservationId,
                'payment_id' => $id,
            ]);

            return ['error' => $th->getMessage(), 'status' => 400, 'message' => 'Error'];
        }
    }
}
