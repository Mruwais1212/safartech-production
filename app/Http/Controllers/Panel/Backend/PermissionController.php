<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Auth\PrivilegeGroupRequest;
use App\Models\Panel\GroupPrivileges;
use App\Models\Panel\Privilege;
use App\Models\Panel\PrivilegeGroup;

class PermissionController extends Controller
{
    public function index()
    {
        $privilege_groups = PrivilegeGroup::whereNotIn('id', [1, 2])->where('guard', 1)->get();

        return view('admin.backend.permission.all', compact('privilege_groups'));
    }

    public function create()
    {
        $privilege_ids = GroupPrivileges::pluck('privilege_id')->toArray();

        $privileges = Privilege::where('parent_id', null)->where('status', 1)->where('guard', 1)->get();

        return view('admin.backend.permission.add', compact('privileges'));
    }

    public function store(PrivilegeGroupRequest $request)
    {
        $privilege_group = PrivilegeGroup::create($request->only('name_ar', 'name_en'));

        $privilege_group->privileges()->sync($request->privileges);

        return redirect()->back()->with('success', __('dashboard.privilege_group_added_successfully'));
    }

    public function edit($id)
    {
        if (in_array($id, [1, 2])) {
            return redirect()->back()->with('error', __('dashboard.privilege_group_not_found'));
        }

        $privilege_group = PrivilegeGroup::findOrFail($id);

        $privilege_ids = GroupPrivileges::pluck('privilege_id')->toArray();

        $privileges = Privilege::where('parent_id', null)->where('status', 1)->where('guard', 1)->get();

        $privilege_ids = GroupPrivileges::where('privilege_group_id', $id)->pluck('privilege_id')->toArray();

        return view('admin.backend.permission.add', compact('privilege_group', 'privilege_ids', 'privileges'));
    }

    public function update(PrivilegeGroupRequest $request, PrivilegeGroup $permission)
    {
        $permission->update($request->only('name_ar', 'name_en'));

        $permission->privileges()->sync($request->privileges);

        return redirect()->back()->with('success', __('dashboard.privilege_group_updated_successfully'));
    }

    public function destroy($id)
    {
        if (in_array($id, [1, 2])) {
            return redirect()->back()->with('error', __('dashboard.privilege_group_not_found'));
        }

        $privilege_group = PrivilegeGroup::findOrFail($id);

        if (! $privilege_group) {
            return redirect()->back()->with('error', __('dashboard.privilege_group_not_found'));
        }

        $privilege_group->privileges()->detach();
        $privilege_group->delete();

        return redirect()->back()->with('success', __('dashboard.privilege_group_deleted_successfully'));
    }
}
