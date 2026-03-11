<?php

use App\Jobs\ReconcileFailedPaidReservationsJob;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('reservations:reconcile-failed-paid', function () {
    ReconcileFailedPaidReservationsJob::dispatch();
    $this->info('Reconciliation job dispatched');
});

// NOTE: queue:work is scheduled in bootstrap/app.php (--stop-when-empty, --tries=3, --timeout=720).
// Do not duplicate it here — a single definition prevents double-processing under the scheduler model.

// Monitor booking details system every 15 minutes
Schedule::command('booking:monitor --alert-threshold=10')->everyFifteenMinutes();

// Clean up old failed jobs daily
Schedule::command('queue:prune-failed-jobs --hours=168')->daily(); // Keep for 1 week
