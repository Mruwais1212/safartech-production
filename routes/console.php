<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();

// Monitor booking details system every 15 minutes
Schedule::command('booking:monitor --alert-threshold=10')->everyFifteenMinutes();

// Clean up old failed jobs daily
Schedule::command('queue:prune-failed-jobs --hours=168')->daily(); // Keep for 1 week
