<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Routing\Controllers\HasMiddleware;

abstract class Controller implements HasMiddleware
{
    public static function middleware()
    {
        return ['auth:admin', 'permission'];
    }
}
