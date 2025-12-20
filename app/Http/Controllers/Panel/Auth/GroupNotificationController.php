<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Auth\GroupNotificationRequest;
use App\Models\Panel\GroupNotification;
use App\Models\User;
use App\Notifications\SendGroupNotification;
use Illuminate\Support\Facades\Notification;

class GroupNotificationController extends Controller
{
    public function index()
    {
        $group_notifications = GroupNotification::all();

        return view('admin.panel.group-notification.all', compact('group_notifications'));
    }

    public function create()
    {
        return view('admin.panel.group-notification.add');
    }

    public function store(GroupNotificationRequest $request)
    {
        $notification = GroupNotification::create(['user_id' => auth('admin')->user()->id, 'types' => json_encode($request->types)] + $request->validated());

        $users = User::query()
            ->when($request->types, function ($query) use ($request) {
                $query->whereIn('user_type_id', $request->types);
            })
            ->get();

        Notification::send($users, new SendGroupNotification($notification));

        return back()->with('success', __('dashboard.group_notification_created_successfully'));
    }
}
