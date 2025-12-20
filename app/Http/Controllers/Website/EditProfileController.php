<?php

namespace App\Http\Controllers\Website;

use App\Http\Requests\Website\EditProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;

class EditProfileController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        return view('website.edit-profile');
    }

    public function store(EditProfileRequest $request)
    {
        $user = User::find(auth('web')->user()->id);

        $user->update([
            'name' => $request->name,
            'phone' => ltrim($request->phone, 0),
        ]);

        return back()->with('success', __('dashboard.profile_updated_successfully'));
    }
}
