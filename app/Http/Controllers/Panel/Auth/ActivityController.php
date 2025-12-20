<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Panel\Controller;
use MFrouh\ActivityModel\Models\Activity;

class ActivityController extends Controller
{
    public function index()
    {
        if (auth('admin')->user()->group_privilege_id == 1) {
            $activities = Activity::orderBy('id', 'desc')->get();
        } else {
            $activities = Activity::whereNotIn('activity_type', ['App\Models\Panel\Privilege', 'App\Models\Panel\SettingType'])
                ->orderBy('id', 'desc')->get();
        }

        return view('admin.panel.activity.all', ['activities' => $activities]);
    }
}
