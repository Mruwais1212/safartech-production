<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CorrelationIdMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $incoming = (string) $request->header('X-Correlation-Id', '');
        $correlationId = preg_match('/^[A-Za-z0-9._-]{8,100}$/', $incoming)
            ? $incoming
            : (string) Str::uuid();

        $request->headers->set('X-Correlation-Id', $correlationId);
        Log::withContext(['correlation_id' => $correlationId]);

        $response = $next($request);
        $response->headers->set('X-Correlation-Id', $correlationId);

        return $response;
    }
}
