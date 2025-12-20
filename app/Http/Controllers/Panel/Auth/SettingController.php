<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Auth\SettingRequest;
use App\Models\Panel\Setting;
use App\Models\Panel\SettingType;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();

        return view('admin.panel.setting.all', compact('settings'));
    }

    public function create()
    {
        $settingTypes = SettingType::all();

        return view('admin.panel.setting.add', compact('settingTypes'));
    }

    public function store(SettingRequest $request)
    {
        Setting::create($request->validated());

        return back()->with('success', __('dashboard.setting_added_successfully'));
    }

    public function edit($id)
    {
        $setting = Setting::find($id);
        $settingTypes = SettingType::all();

        return view('admin.panel.setting.add', compact('setting', 'settingTypes'));
    }

    public function update(SettingRequest $request, Setting $setting)
    {
        $setting->update($request->validated());

        return back()->with('success', __('dashboard.setting_updated_successfully'));
    }

    public function destroy($id)
    {
        $setting = Setting::find($id);

        if (! $setting) {
            return back()->with('error', __('dashboard.setting_not_found'));
        }

        $setting->delete();

        return back()->with('success', __('dashboard.setting_deleted_successfully'));
    }
}
