<?php

namespace App\Http\Requests\Panel\Auth;

use Illuminate\Foundation\Http\FormRequest;

class MainSliderRequest extends FormRequest
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
        return [
            'name_ar' => ['required', 'string'],
            'name_en' => ['required', 'string'],
        ];
    }

    public function attributes()
    {
        return [
            'name_ar' => __('dashboard.main_slider_name_ar'),
            'name_en' => __('dashboard.main_slider_name_en'),
        ];
    }
}
