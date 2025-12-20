<?php

namespace App\Http\Requests\Panel\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SettingTypeRequest extends FormRequest
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
            'name_ar' => ['required', 'string', 'max:255', $request_method ? 'unique:setting_types' :
                'unique:setting_types,name_ar,'.$id],

            'name_en' => ['required', 'string', 'max:255', $request_method ? 'unique:setting_types' :
                'unique:setting_types,name_en,'.$id],
        ];
    }

    public function attributes()
    {
        return [
            'name_ar' => __('dashboard.setting_type_name_ar'),
            'name_en' => __('dashboard.setting_type_name_en'),
        ];
    }
}
