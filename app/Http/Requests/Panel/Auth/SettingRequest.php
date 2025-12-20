<?php

namespace App\Http\Requests\Panel\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
        $request_method = request()->getMethod() == 'POST';

        $id = request()->isMethod('POST') ?: array_reverse(explode('/', request()->getRequestUri()))[0];

        return [
            'name_ar' => ['required', 'string', 'max:255',  $request_method ? 'unique:settings' :
                'unique:settings,name_ar,'.$id],

            'name_en' => ['required', 'string', 'max:255',  $request_method ? 'unique:settings' :
                'unique:settings,name_en,'.$id],

            'code' => ['required', 'string', 'max:255',  $request_method ? 'unique:settings' :
                'unique:settings,code,'.$id],

            'setting_type_id' => ['required', 'integer', 'exists:setting_types,id'],
            'value' => ['required'],
            'input_type' => ['required'],
            'sort' => ['nullable'],
            'status' => ['nullable'],
            'note_ar' => ['nullable'],
            'note_en' => ['nullable'],
        ];
    }

    public function attributes()
    {
        return [
            'name_ar' => __('dashboard.setting_name_ar'),
            'name_en' => __('dashboard.setting_name_en'),
            'code' => __('dashboard.setting_code'),
            'setting_type_id' => __('dashboard.setting_type'),
            'value' => __('dashboard.setting_value'),
            'input_type' => __('dashboard.setting_input_type'),
            'sort' => __('dashboard.setting_sort'),
            'status' => __('dashboard.setting_status'),
            'note_ar' => __('dashboard.setting_note_ar'),
            'note_en' => __('dashboard.setting_note_en'),
        ];
    }
}
