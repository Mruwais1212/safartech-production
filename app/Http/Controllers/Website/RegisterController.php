<?php

namespace App\Http\Controllers\Website;

use App\Http\Requests\Website\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        return view('website.sign-up');
    }

    public function store(RegisterRequest $request)
    {
        $user = User::create($request->except('password', 'password_confirmation', 'photo', '_token') + ['password' => bcrypt($request->password)]);

        auth('web')->login($user);

        return redirect('/')->with('success', __('dashboard.account_created_successfully'));
    }
}
