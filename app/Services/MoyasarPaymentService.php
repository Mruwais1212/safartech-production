<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MoyasarPaymentService
{
    public static function createInvoice($data)
    {
        try {
            $response = Http::withBasicAuth(env('MOYASAR_SECRET_KEY'), '')->post(
                env('MOYASAR_BASE_URL').'/v1/invoices',
                [
                    'amount' => (int) (number_format($data['amount'] * 100, 2, '.', '')),
                    'currency' => $data['currency'],
                    'description' => $data['description'],
                    'callback_url' => $data['callback_url'],
                    'metadata' => $data['metadata'],
                    'success_url' => $data['success_url'],
                    // 'methods' => ['creditcard', 'stcpay', 'mada'],
                ]
            );

            return $response->json();
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage(), 'status' => 400, 'message' => 'Error'];
        }
    }

    public static function getInvoice($id)
    {
        try {
            $response = Http::withBasicAuth(env('MOYASAR_SECRET_KEY'), '')->get(
                env('MOYASAR_BASE_URL').'/v1/payments/'.$id
            );

            return $response->json();
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage(), 'status' => 400, 'message' => 'Error'];
        }
    }

    public static function refund($id, $amount)
    {
        try {
            $response = Http::withBasicAuth(env('MOYASAR_SECRET_KEY'), '')->get(
                env('MOYASAR_BASE_URL').'/v1/payments/'.$id .'/refund',
                [
                    'amount' => (int) ($amount),
                ]
            );

            return $response->json();
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage(), 'status' => 400, 'message' => 'Error'];
        }
    }
}
