<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Auth\PrivilegeRequest;
use App\Models\Panel\Privilege;
use Illuminate\Http\Request;

class PrivilegeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->type == 'hidden') {
            $privileges = Privilege::with('subPrivilege')->where('parent_id', null)->where('status', 0)->where('type', 1)->get();
        } else {
            $privileges = Privilege::with('subPrivilege')->where('parent_id', null)->where('status', 1)->where('type', 1)->get();
        }

        return view('admin.panel.privilege.all', compact('privileges'));
    }

    public function create()
    {
        return view('admin.panel.privilege.add');
    }

    public function store(PrivilegeRequest $request)
    {
        $privilege = Privilege::create($request->validated());

        if ($request->sub_privilege && is_array($request->sub_privilege) && count($request->sub_privilege) > 0) {
            foreach ($request->sub_privilege as $key => $value) {
                $privilege->subPrivilege()->create($value);
            }
        }

        return redirect()->back()->with('success', __('dashboard.privilege_added_successfully'));
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $privilege = Privilege::findOrFail($id);

        return view('admin.panel.privilege.add', compact('privilege'));
    }

    public function update(PrivilegeRequest $request, Privilege $privilege)
    {
        $privilege->update($request->except(['_method', 'sub_privilege', '_token']));

        if ($request->sub_privilege && is_array($request->sub_privilege) && count($request->sub_privilege) > 0) {
            foreach ($request->sub_privilege as $key => $value) {
                $privilege->subPrivilege()->updateOrCreate(['id' => array_key_exists('id', $value) ? $value['id'] : null], $value);
            }
        }

        return redirect()->back()->with('success', __('dashboard.privilege_updated_successfully'));
    }

    public function destroy($id)
    {
        $privilege = Privilege::findOrFail($id);

        if (! $privilege) {
            return redirect()->back()->with('error', 'Privilege not found.');
        }

        $privilege->delete();

        return redirect()->back()->with('success', __('dashboard.privilege_deleted_successfully'));
    }

    public function changeSort(Request $request)
    {
        foreach ($request->position as $key => $value) {
            Privilege::find($value)->update(['sort' => $key]);
        }
    }

    public function changeStatus($id)
    {
        $privilege = Privilege::find($id);

        if (! $privilege) {
            return redirect()->back()->with('error', __('dashboard.privilege_not_found'));
        }

        $privilege->update(['status' => ! $privilege->status]);

        return redirect()->back()->with('success', __('dashboard.privilege_updated_successfully'));
    }

    public function privilegeItems()
    {
        return view('admin.panel.privilege.item');
    }
}
