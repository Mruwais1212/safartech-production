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

        $apiUser = auth('api')->user();
        if (! $apiUser) {
            return response()->json(['message' => __('messages.You can\'t access this action')], 401);
        }
        $userType = Str::lower(UserType::getKey($apiUser->user_type_id));

        if (! in_array($userType, $guards)) {
            return response()->json(['message' => __('messages.You can\'t access this action')], 400);
        }

        return $next($request);
    }
}
