<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::connection()->getPdo()->sqliteCreateFunction('acos', function ($number) {
            return acos($number);
        }, 1);

        DB::connection()->getPdo()->sqliteCreateFunction('cos', function ($number) {
            return cos($number);
        }, 1);

        DB::connection()->getPdo()->sqliteCreateFunction('radians', function ($number) {
            return deg2rad($number);
        }, 1);

        DB::connection()->getPdo()->sqliteCreateFunction('sin', function ($number) {
            return sin($number);
        }, 1);
    }
}
