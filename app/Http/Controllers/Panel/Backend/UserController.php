<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Enums\UserType;
use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Backend\UserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('user_type_id', UserType::CLIENT)->get();

        return view('admin.backend.user.all', compact('users'));
    }

    public function create()
    {
        return view('admin.backend.user.add');
    }

    public function store(UserRequest $request)
    {
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
        }

        User::create([
            'user_type_id' => UserType::CLIENT, 'password' => bcrypt('@Pass@123@word:)'),
            'photo' => $request->hasFile('photo') ? $fileName : 'default_user.png',
            'activate' => 1, 'block' => 0, 'is_fake_user' => 1,
        ] + $request->validated());

        return redirect()->back()->with('success', __('dashboard.user added successfully'));
    }

    public function edit($id)
    {
        $user = User::where('user_type_id', UserType::CLIENT)->where('activate', 1)->where('id', $id)->first();

        if (! $user) {
            return redirect()->back()->with('error', __('dashboard.user not found'));
        }

        return view('admin.backend.user.add', compact('user'));
    }

    public function update(UserRequest $request, $id)
    {
        $user = User::where('user_type_id', UserType::CLIENT)->where('activate', 1)->where('id', $id)->first();

        if (! $user) {
            return redirect()->back()->with('error', __('dashboard.user not found'));
        }

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            if (is_file('uploads/'.$user->photo) && $user->photo != 'default_user.png') {
                unlink('uploads/'.$user->photo);
            }
            $fileName = 'profile-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
        }

        $user->update(
            ['password' => $request->password ? bcrypt($request->password) : $user->password] +
                $request->validated()
        );

        return redirect()->back()->with('success', __('dashboard.user updated successfully'));
    }

    public function destroy($id)
    {
        $user = User::where('user_type_id', UserType::CLIENT)->where('activate', 1)->where('id', $id)->first();

        if (! $user) {
            return redirect()->back()->with('error', __('dashboard.user not found'));
        }

        $user->delete();

        return redirect()->back()->with('success', __('dashboard.user deleted successfully'));
    }

    public function block($id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['status' => 0, 'message' => __('dashboard.user not found')]);
        }

        $user->update(['block' => ! $user->block]);

        if ($user->refresh()->block) {
            return response()->json(['status' => 1, 'message' => __('dashboard.user blocked successfully')]);
        }

        return response()->json(['status' => 1, 'message' => __('dashboard.user unblocked successfully')]);
    }
}
