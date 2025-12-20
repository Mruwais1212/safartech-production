<?php

namespace App\Http\Controllers\Panel\Guest;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LoginController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:admin', only: ['logout']),
            new Middleware('guest:admin', except: ['logout']),
        ];
    }

    public function index()
    {
        return view('admin.auth.login');
    }

    public function store(LoginRequest $request)
    {
        if (auth()->guard('admin')->attempt($request->only('email', 'password'), $request->remember)) {
            return redirect('/admin-panel')->with('success', __('dashboard.You are now logged in'));
        }

        return redirect()->back()->withInput($request->only('email'))->with('error', __('dashboard.Invalid email or password'));
    }

    public function logout(Request $request)
    {
        auth()->guard('admin')->logout();

        return redirect('/admin-panel/login')->with('success', __('dashboard.You are now logged out'));
    }
}
