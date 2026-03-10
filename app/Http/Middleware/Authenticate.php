<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($guards);
        if (auth('admin')->user() && (! in_array(auth('admin')->user()->user_type_id, [UserType::ADMIN, UserType::SUPER_ADMIN, UserType::SUPERVISOR]))) {
            auth('admin')->logout();

            return redirect()->to('admin-panel/login')->with('error', __('dashboard.You must be the owner of the site or a supervisor to be able to enter the site'));
        }

        return $next($request);
    }

    protected function authenticate(array $guards)
    {
        if (empty($guards)) {
            if (auth('admin')->check() && (auth('admin')->user()->user_type_id == UserType::SUPER_ADMIN || auth('admin')->user()->user_type_id == UserType::ADMIN)) {
                return $this->auth->authenticate();
            }

            throw new AuthenticationException('Unauthenticated.', $guards, '/');
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        if (($guards[0] ?? null) === 'web') {
            throw new AuthenticationException('Unauthenticated.', $guards, '/sign-in');
        }

        throw new AuthenticationException('Unauthenticated.', $guards, "/{$guards[0]}-panel/login");
    }
}
