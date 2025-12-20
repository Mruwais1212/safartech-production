<?php

namespace App\Enums;

use Illuminate\Support\Str;
use ReflectionClass;
use Throwable;

class Enum
{
    public static function getCases(): array
    {
        $constants = new ReflectionClass(static::class);

        return $constants->getConstants();
    }

    public static function getKey(int $value, ?string $type = null)
    {
        $cases = self::getCases();
        $cases = array_flip($cases);

        try {
            return $type ? Str::$type($cases[$value]) : $cases[$value];
        } catch (Throwable $th) {
            return $cases[$value];
        }
    }
}
