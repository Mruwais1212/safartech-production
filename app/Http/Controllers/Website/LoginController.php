<?php

namespace App\Http\Controllers\Website;

use App\Http\Requests\Website\LoginRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        return view('website.sign-in');
    }

    public function store(LoginRequest $request)
    {
        if (auth('web')->attempt($request->only('email', 'password'), $request->remember)) {
            return redirect()->intended('/')->with('success', __('dashboard.You are now logged in'));
        }

        return redirect()->back()->with('error', __('dashboard.Invalid email or password'))->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        auth('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', __('dashboard.You are now logged out'));
    }
}
