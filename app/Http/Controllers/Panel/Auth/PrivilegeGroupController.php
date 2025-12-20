<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Auth\PrivilegeGroupRequest;
use App\Models\Panel\GroupPrivileges;
use App\Models\Panel\Privilege;
use App\Models\Panel\PrivilegeGroup;

class PrivilegeGroupController extends Controller
{
    public function index()
    {
        $privilege_groups = PrivilegeGroup::where('id', '!=', 1)->get();

        return view('admin.panel.privilege-group.all', compact('privilege_groups'));
    }

    public function create()
    {
        $privileges = Privilege::where('parent_id', null)->whereNotIn('id', range(1, 10))->where('status', 1)->get();

        return view('admin.panel.privilege-group.add', compact('privileges'));
    }

    public function store(PrivilegeGroupRequest $request)
    {
        $privilege_group = PrivilegeGroup::create($request->only('name_ar', 'name_en'));

        $privilege_group->privileges()->sync($request->privileges);

        return redirect()->back()->with('success', __('dashboard.privilege_group_created_successfully'));
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        // if ($id == 1) {
        //     return redirect()->back()->with('error', __('dashboard.privilege_group_not_found'));
        // }
        $privilege_group = PrivilegeGroup::findOrFail($id);
        $privilege_ids = GroupPrivileges::where('privilege_group_id', $id)->pluck('privilege_id')->toArray();
        $privileges = Privilege::where('parent_id', null)->whereNotIn('id', range(1, 10))->where('status', 1)->get();

        return view('admin.panel.privilege-group.add', compact('privilege_group', 'privilege_ids', 'privileges'));
    }

    public function update(PrivilegeGroupRequest $request, PrivilegeGroup $privilege_group)
    {
        $privilege_group->update($request->only('name_ar', 'name_en'));

        $privilege_group->privileges()->sync($request->privileges);

        return redirect()->back()->with('success', __('dashboard.privilege_group_updated_successfully'));
    }

    public function destroy($id)
    {
        if ($id == 1) {
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
