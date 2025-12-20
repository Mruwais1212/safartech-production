<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Auth\SettingTypeRequest;
use App\Models\Panel\SettingType;

class SettingTypeController extends Controller
{
    public function index()
    {
        $setting_types = SettingType::all();

        return view('admin.panel.setting-type.all', compact('setting_types'));
    }

    public function create()
    {
        return view('admin.panel.setting-type.add');
    }

    public function store(SettingTypeRequest $request)
    {
        SettingType::create($request->validated());

        return back()->with('success', __('dashboard.setting_type_created_successfully'));
    }

    public function edit($id)
    {
        $setting_type = SettingType::find($id);

        return view('admin.panel.setting-type.add', compact('setting_type'));
    }

    public function update(SettingTypeRequest $request, SettingType $setting_type)
    {
        $setting_type->update($request->validated());

        return back()->with('success', __('dashboard.setting_type_updated_successfully'));
    }

    public function destroy($id)
    {
        $setting_type = SettingType::find($id);

        if (! $setting_type) {
            return back()->with('error', __('dashboard.setting_type_not_found'));
        }

        $setting_type->delete();

        return back()->with('success', __('dashboard.setting_type_deleted_successfully'));
    }
}
