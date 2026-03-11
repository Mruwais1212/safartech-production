<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentSecurityPolicyReportOnly
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $policy = "default-src 'self'; script-src 'self' 'unsafe-inline' https://api.moyasar.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; connect-src 'self' https://api.openai.com https://api.moyasar.com; frame-src https://api.moyasar.com; object-src 'none'; base-uri 'self'; form-action 'self';";

        // Enforced CSP — was Content-Security-Policy-Report-Only (monitoring only, not enforced).
        $response->headers->set('Content-Security-Policy', $policy);

        return $response;
    }
}
