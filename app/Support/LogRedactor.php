<?php

namespace App\Support;

class LogRedactor
{
    private const SENSITIVE_KEYS = [
        'password',
        'token',
        'authorization',
        'api_key',
        'passport',
        'email',
        'phone',
        // 'number' removed — substring match masked flight_number, confirmation_number,
        //   booking_reference_number which are operational identifiers needed in logs.
        //   Use the explicit keys below instead.
        'card_number',
        'credit_card',
        'cvv',
        // 'name' removed — too broad; masked airline_name, country_name, city_name etc.
        //   Explicit PII keys below replace it.
        'first_name',
        'last_name',
        'full_name',
        'passenger_name',
    ];

    public static function redact(array $data): array
    {
        $redacted = [];

        foreach ($data as $key => $value) {
            $normalizedKey = is_string($key) ? strtolower($key) : (string) $key;

            if (self::isSensitiveKey($normalizedKey)) {
                $redacted[$key] = self::mask($value);
                continue;
            }

            if (is_array($value)) {
                $redacted[$key] = self::redact($value);
                continue;
            }

            $redacted[$key] = $value;
        }

        return $redacted;
    }

    private static function isSensitiveKey(string $key): bool
    {
        foreach (self::SENSITIVE_KEYS as $sensitiveKey) {
            if (str_contains($key, $sensitiveKey)) {
                return true;
            }
        }

        return false;
    }

    private static function mask($value): string
    {
        if ($value === null) {
            return '[REDACTED]';
        }

        $stringValue = (string) $value;
        $length = strlen($stringValue);

        if ($length <= 4) {
            return str_repeat('*', max($length, 4));
        }

        return substr($stringValue, 0, 2) . str_repeat('*', $length - 4) . substr($stringValue, -2);
    }
}

