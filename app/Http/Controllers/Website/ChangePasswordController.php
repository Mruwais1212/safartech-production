<?php

namespace App\Http\Controllers\Website;

use App\Http\Requests\Website\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        return view('website.change-password');
    }

    public function store(ChangePasswordRequest $request)
    {
        $user = User::find(auth('web')->user()->id);

        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();

            return back()->with('success', __('dashboard.Password changed successfully'));
        }

        return back()->with('error', __('dashboard.Old password is incorrect'));
    }
}
