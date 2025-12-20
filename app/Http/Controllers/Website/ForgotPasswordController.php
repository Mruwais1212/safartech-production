<?php

namespace App\Http\Controllers\Website;

use App\Http\Requests\Website\ForgotPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        return view('website.reset-password');
    }

    public function store(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status), 'success' => __('dashboard.Password reset link sent to your email')])
            : back()->withErrors(['email' => __($status)]);
    }
}
