<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckGuard
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (empty($guards)) {
            return response()->json(['message' => __('messages.You can\'t access this action')], 400);
        }

        $userType = Str::lower(UserType::getKey(auth('api')->user()->user_type_id));

        if (! in_array($userType, $guards)) {
            return response()->json(['message' => __('messages.You can\'t access this action')], 400);
        }

        return $next($request);
    }
}
