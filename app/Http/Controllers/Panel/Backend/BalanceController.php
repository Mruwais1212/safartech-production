<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Enums\UserType;
use App\Http\Controllers\Panel\Controller;
use App\Models\Balance;
use App\Models\User;
use App\Notifications\Balance\AddBalanceFromAdminPanelNotification;
use App\Notifications\Balance\DeductBalanceFromAdminPanelNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class BalanceController extends Controller 
{
    public function index(Request $request)
    {
        $balances = Balance::orderBy('id', 'desc')->get();

        return view('admin.backend.balance.all', compact('balances'));
    }

    public function addBalance()
    {
        $users = User::where('user_type_id', UserType::CLIENT)->get();

        return view('admin.backend.balance.add', compact('users'));
    }

    public function addBalancePost(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'description_ar' => 'required',
            'description_en' => 'required',
        ], [], [
            'description_ar' => __('dashboard.balance_description_ar'),
            'description_en' => __('dashboard.balance_description_en'),
            'amount' => __('dashboard.balance_amount'),
            'user_id' => __('dashboard.user_id'),
        ]);

        $user = User::find($request->user_id);

        // Notification::send($user, new AddBalanceFromAdminPanelNotification($request));

        return redirect()->back()->with('success', __('messages.add_balance_successfully'));
    }

    public function deductBalance()
    {
        $users = User::where('user_type_id', UserType::CLIENT)->get();

        return view('admin.backend.balance.deduct', compact('users'));
    }

    public function deductBalancePost(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'description_ar' => 'required',
            'description_en' => 'required',
        ], [], [
            'description_ar' => __('dashboard.balance_description_ar'),
            'description_en' => __('dashboard.balance_description_en'),
            'amount' => __('dashboard.balance_amount'),
            'user_id' => __('dashboard.user_id'),
        ]);

        $user = User::find($request->user_id);

        // Notification::send($user, new DeductBalanceFromAdminPanelNotification($request));

        return redirect()->back()->with('success', __('messages.deduct_balance_successfully'));
    }
}
