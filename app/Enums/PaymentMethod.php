<?php

namespace App\Enums;

class PaymentMethod extends Enum
{
    public const CACHE_PAYMENT = 1;

    public const BALANCE_PAYMENT = 2;

    public const ELECTRONIC_PAYMENT = 3;

    public static function getLabel(int $value): string
    {
        return match ($value) {
            self::CACHE_PAYMENT => 'CACHE_PAYMENT',
            self::BALANCE_PAYMENT => 'BALANCE_PAYMENT',
            self::ELECTRONIC_PAYMENT => 'ELECTRONIC_PAYMENT',
            default => 'Unknown',
        };
    }
}
