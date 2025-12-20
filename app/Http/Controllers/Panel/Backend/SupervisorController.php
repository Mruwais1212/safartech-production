<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Enums\UserType;
use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Backend\SupervisorRequest;
use App\Models\Panel\PrivilegeGroup;
use App\Models\User;

class SupervisorController extends Controller
{
    public function index()
    {
        $supervisors = User::where('user_type_id', UserType::SUPERVISOR)->get();

        return view('admin.backend.supervisor.all', compact('supervisors'));
    }

    public function create()
    {
        $permissions = PrivilegeGroup::whereNotIn('id', [1, 2])->where('guard', 1)->get();

        return view('admin.backend.supervisor.add', compact('permissions'));
    }

    public function store(SupervisorRequest $request)
    {
        User::create(['user_type_id' => UserType::SUPERVISOR, 'password' => bcrypt($request->password), 'activate' => 1] + $request->validated());

        return redirect()->back()->with('success', __('dashboard.supervisor added successfully'));
    }

    public function edit($id)
    {
        $supervisor = User::where('user_type_id', UserType::SUPERVISOR)->where('id', $id)->first();

        if (! $supervisor) {
            return redirect()->back()->with('error', __('dashboard.supervisor not found'));
        }

        $permissions = PrivilegeGroup::whereNotIn('id', [1, 2])->where('guard', 1)->get();

        return view('admin.backend.supervisor.add', compact('supervisor', 'permissions'));
    }

    public function update(SupervisorRequest $request, User $supervisor)
    {
        if (! $supervisor || $supervisor->user_type_id != UserType::SUPERVISOR) {
            return redirect()->back()->with('error', __('dashboard.supervisor not found'));
        }

        $supervisor->update(
            ['password' => $request->password ? bcrypt($request->password) : $supervisor->password] +
            $request->validated()
        );

        return redirect()->back()->with('success', __('dashboard.supervisor updated successfully'));
    }

    public function destroy(User $supervisor)
    {
        if (! $supervisor) {
            return redirect()->back()->with('error', __('dashboard.supervisor not found'));
        }

        $supervisor->delete();

        return redirect()->back()->with('success', __('dashboard.supervisor deleted successfully'));
    }
}
