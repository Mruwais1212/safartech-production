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
        $get_method_name = $request->route()->getActionMethod();
        $explode_method_name = explode('@', $request->route()->getActionName())[0];
        $explode_controller_path = explode("App\Http\Controllers\\", $explode_method_name)[1];

        $currentUser = User::find(auth($guard)->id());

        $privilege = Privilege::where('method', $get_method_name)->where('controller', $explode_controller_path)->first();
        $checkPrivilege = ! $currentUser->privileges()->where('method', $get_method_name)->where('controller', $explode_controller_path)->first();

        if ($privilege && $checkPrivilege) {
            return abort(403, __('dashboard.You do not have permission to access this page'));
        }

        return $next($request);
    }
}
