<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Website\Controller;
use App\Models\Reservation;
use App\Services\MoyasarPaymentService;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MoyasarWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        // --- HMAC signature verification ---
        $webhookSecret = config('services.moyasar.webhook_secret');

        if (! empty($webhookSecret)) {
            $signature = $request->header('X-Moyasar-Signature');

            if (empty($signature)) {
                Log::warning('Moyasar webhook missing signature header');

                return response()->json(['message' => 'Forbidden'], 403);
            }

            $expectedSignature = hash_hmac('sha256', $request->getContent(), $webhookSecret);

            if (! hash_equals($expectedSignature, $signature)) {
                Log::warning('Moyasar webhook HMAC mismatch', [
                    'ip' => $request->ip(),
                ]);

                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        // --- Payload validation ---
        $validation = Validator::make($request->all(), [
            'id' => ['required', 'string', 'max:191'],
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $payment = MoyasarPaymentService::getInvoice($request->input('id'));

        if (($payment['status'] ?? null) !== 'paid') {
            return response()->json(['message' => 'Ignored'], 202);
        }

        $result = DB::transaction(function () use ($payment) {
            $reservation = Reservation::where('payment_id', $payment['invoice_id'] ?? null)
                ->lockForUpdate()
                ->first();

            if (! $reservation) {
                return ['type' => 'missing'];
            }

            if ($reservation->payment_processed_at !== null || (int) $reservation->status === 1) {
                return ['type' => 'replay'];
            }

            $correlationId = 'webhook-res-'.$reservation->id.'-pay-'.($payment['invoice_id'] ?? 'unknown');
            $bookingResult = ReservationService::completeBooking($reservation->id, $correlationId);

            if (! $bookingResult['success']) {
                $reservation->update([
                    'status' => 2,
                    'payment_method' => 1,
                    'payment_type' => $payment['source']['company'] ?? null,
                    'paid_at' => now(),
                    'payment_processed_at' => now(),
                    'booking_error' => implode('; ', $bookingResult['errors']),
                    'reconciliation_status' => 'reconcile_pending',
                ]);

                return ['type' => 'booking_failed'];
            }

            $reservation->update([
                'status' => 1,
                'payment_method' => 1,
                'payment_type' => $payment['source']['company'] ?? null,
                'paid_at' => now(),
                'payment_processed_at' => now(),
                'reconciliation_status' => null,
            ]);

            return ['type' => 'completed'];
        });

        Log::info('Moyasar webhook processed', [
            'payment_id' => $payment['invoice_id'] ?? null,
            'result' => $result['type'] ?? 'unknown',
        ]);

        return response()->json(['message' => 'ok'], 200);
    }
}
