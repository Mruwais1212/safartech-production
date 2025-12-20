<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Http\Controllers\Panel\Controller;
use MFrouh\ActivityModel\Models\Activity;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Activity::whereIn('activity_type', ['App\Models\Order'])->orderBy('id', 'desc')->get();

        return view('admin.backend.notification.all', compact('notifications'));
    }
}
