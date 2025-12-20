<?php

namespace App\Http\Requests\Panel\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PaymentSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = array_reverse(explode('/', request()->getUri()))[0];

        return [
            'code' => ['required', request()->getMethod() == 'POST' ? 'unique:payment_settings,code' : 'unique:payment_settings,code,'.$id],
            'name_ar' => [request()->getMethod() == 'POST' ? 'required' : 'nullable', request()->getMethod() == 'POST' ? 'unique:payment_settings,name_ar' : 'nullable'],
            'name_en' => [request()->getMethod() == 'POST' ? 'required' : 'nullable', request()->getMethod() == 'POST' ? 'unique:payment_settings,name_en' : 'nullable'],
            'status' => ['nullable', 'in:0,1'],
            'public_key' => ['required_if:code,tap,tabby,tamara'],
            'secret_key' => ['required_if:code,tap,tabby,tamara'],
            'merchant_code' => ['required_if:code,tabby'],
            'auth_token' => ['required_if:code,tamara'],
            'notification_token' => ['required_if:code,tamara'],
            'currency' => ['required_if:code,tap,tabby,tamara'],
            'country_code' => ['required_if:code,tamara'],
            'lang' => ['required_if:code,tap'],
            'tranportal_id' => ['required_if:code,arjbank'],
            'tranportal_password' => ['required_if:code,arjbank'],
            'resource_key' => ['required_if:code,arjbank'],
        ];
    }

    public function messages()
    {
        return [
            'public_key.required_if' => __('validation.required', ['attribute' => 'Public Key']),
            'secret_key.required_if' => __('validation.required', ['attribute' => 'Secret Key']),
            'merchant_code.required_if' => __('validation.required', ['attribute' => 'Merchant Code']),
            'auth_token.required_if' => __('validation.required', ['attribute' => 'Auth Token']),
            'notification_token.required_if' => __('validation.required', ['attribute' => 'Notification Token']),
            'currency.required_if' => __('validation.required', ['attribute' => 'Currency']),
            'country_code.required_if' => __('validation.required', ['attribute' => 'Country Code']),
            'lang.required_if' => __('validation.required', ['attribute' => 'Language']),
            'tranportal_id.required_if' => __('validation.required', ['attribute' => 'Tranportal ID']),
            'tranportal_password.required_if' => __('validation.required', ['attribute' => 'Tranportal Password']),
            'resource_key.required_if' => __('validation.required', ['attribute' => 'Resource Key']),
        ];
    }
}
