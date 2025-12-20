<?php

namespace App\Http\Requests\Panel\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupervisorRequest extends FormRequest
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

    public function rules()
    {
        $phone_regex = '/^(05)[0-9]{8}$|^(5)[0-9]{8}$/';

        return [
            'name' => ['required'],
            'email' => [request()->isMethod('POST') ? 'required' : 'nullable', request()->isMethod('POST') ?
                'unique:users,email' : 'unique:users,email,'.request('supervisor')->id, 'email'],
            'group_privilege_id' => ['required', 'exists:group_privileges,id'],
            'phone' => ['required', 'regex:'.$phone_regex, request()->isMethod('POST') ? Rule::unique('users')->where('phone', $this->phone) :
                Rule::unique('users')->where('phone', $this->phone)
                    ->whereNot('id', request('supervisor')->id)],
            'password' => [request()->isMethod('POST') ? 'required' : 'nullable', 'confirmed', 'min:6'],
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('dashboard.supervisor_name'),
            'email' => __('dashboard.email'),
            'group_privilege_id' => __('dashboard.privilege_type'),
            'password' => __('dashboard.password'),
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'phone' => ltrim($this->phone, 0),
        ]);
    }
}
