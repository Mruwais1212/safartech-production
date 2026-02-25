<?php

return [
    'reconciliation_interval_minutes' => (int) env('PAYMENT_RECONCILIATION_INTERVAL_MINUTES', 10),
    'reconciliation_max_attempts' => (int) env('PAYMENT_RECONCILIATION_MAX_ATTEMPTS', 3),
];
