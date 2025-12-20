<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Auth\PaymentSettingRequest;
use App\Models\Panel\PaymentSetting;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $paymentSettings = PaymentSetting::all();

        return view('admin.panel.payment-setting.all', compact('paymentSettings'));
    }

    public function update(PaymentSettingRequest $request, $id)
    {
        $paymentSetting = PaymentSetting::find($id);

        if (! $paymentSetting) {
            return redirect()->back()->with('error', __('dashboard.not_found'));
        }

        $paymentSetting->update($request->validated() + ['status' => 1]);

        return redirect()->back()->with('success', __('dashboard.activated_payment_setting', ['name' => $paymentSetting->name_ar]));
    }

    public function changeStatus($id)
    {
        $paymentSetting = PaymentSetting::find($id);

        if (! $paymentSetting) {
            return redirect()->back()->with('error', __('dashboard.not_found'));
        }

        $paymentSetting->update([
            'status' => $paymentSetting->status == 1 ? 0 : 1,
        ]);

        return redirect()->back()->with('success', __('dashboard.'.($paymentSetting->status == 0 ? 'inactive_payment_setting' : 'activated_payment_setting'), ['name' => $paymentSetting->name_ar]));
    }
}
