<?php

namespace App\Jobs;

use App\Models\Reservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReconcileFailedPaidReservationsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $maxAttempts = max(1, (int) config('payment.reconciliation_max_attempts', 3));

        Reservation::query()
            ->where('status', 2)
            ->whereNotNull('paid_at')
            ->where('reconciliation_status', 'reconcile_pending')
            ->where('reconcile_attempt_count', '<', $maxAttempts)
            ->where(function ($query) {
                $query->whereNull('booking_reference_id')
                    ->orWhere('booking_reference_id', '');
            })
            ->orderBy('id')
            ->chunkById(50, function ($reservations) use ($maxAttempts) {
                foreach ($reservations as $reservation) {
                    $attempt = (int) $reservation->reconcile_attempt_count + 1;
                    $timestamp = now()->toDateTimeString();
                    $marker = '[reconciliation_hotfix_v3_1]';
                    $note = "{$marker} attempt={$attempt} at={$timestamp}";

                    $existingError = trim((string) $reservation->booking_error);
                    if ($existingError === '') {
                        $bookingError = $note;
                    } elseif (Str::contains($existingError, $marker)) {
                        $bookingError = preg_replace('/\[reconciliation_hotfix_v3_1\].*$/', $note, $existingError) ?: $note;
                    } else {
                        $bookingError = $existingError.'; '.$note;
                    }

                    $reservation->update([
                        'reconcile_attempt_count' => $attempt,
                        'last_reconciled_at' => now(),
                        'reconciliation_status' => $attempt >= $maxAttempts ? 'needs_manual_review' : 'reconcile_pending',
                        'booking_error' => $bookingError,
                    ]);

                    Log::warning('Failed paid reservation reconciled', [
                        'reservation_id' => $reservation->id,
                        'attempt' => $attempt,
                        'max_attempts' => $maxAttempts,
                        'reconciliation_status' => $attempt >= $maxAttempts ? 'needs_manual_review' : 'reconcile_pending',
                    ]);
                }
            });
    }
}
