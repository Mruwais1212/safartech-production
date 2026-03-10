<?php

namespace App\Http\Middleware;

use App\Models\Panel\Privilege;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class Permission
{
    public function handle(Request $request, Closure $next, $guard = 'admin')
    {
        $actionName = $request->route()->getActionName();

        // Guard against closures and invokable controllers (no '@' separator)
        if (! str_contains($actionName, '@')) {
            return $next($request);
        }

        $get_method_name  = $request->route()->getActionMethod();
        $controllerClass  = explode('@', $actionName)[0];
        $parts            = explode("App\Http\Controllers\\", $controllerClass);

        if (! isset($parts[1])) {
            return $next($request);
        }

        $explode_controller_path = $parts[1];
        $currentUser = User::find(auth($guard)->id());

        if (! $currentUser) {
            return abort(403, __('dashboard.You do not have permission to access this page'));
        }

        $privilege      = Privilege::where('method', $get_method_name)->where('controller', $explode_controller_path)->first();
        $checkPrivilege = ! $currentUser->privileges()->where('method', $get_method_name)->where('controller', $explode_controller_path)->first();

        if ($privilege && $checkPrivilege) {
            return abort(403, __('dashboard.You do not have permission to access this page'));
        }

        return $next($request);
    }
}
