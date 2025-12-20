<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Auth\SettingsRequest;
use App\Models\Panel\Setting;
use App\Models\Panel\SettingType;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::where('status', 1)->get();
        $setting_types = SettingType::whereHas(
            'settings',
            function ($query) {
                $query->where('status', 1);
            }
        )->get();

        return view('admin.panel.settings.all', compact('settings', 'setting_types'));
    }

    public function store(SettingsRequest $request)
    {
        foreach ($request->handle_settings as $key => $value) {
            Setting::find($key)->update(['value' => $value]);
        }

        return back()->with('success', __('dashboard.settings_updated_successfully'));
    }
}
