<?php

namespace App\Http\Requests\Panel\Backend;

use Illuminate\Foundation\Http\FormRequest;

class AiPlannerContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $request_method = request()->getMethod() == 'POST';

        $id = request()->isMethod('POST') ?: array_reverse(explode('/', request()->getRequestUri()))[0];

        return [
            'name_ar' => ['required', 'string', $request_method ? 'unique:ai_planner_sections' :
             'unique:ai_planner_sections,name_ar,'.$id],

            'name_en' => ['required', 'string', $request_method ? 'unique:ai_planner_sections' :
             'unique:ai_planner_sections,name_en,'.$id],

            'sort' => ['required', 'integer'],
            'icon' => ['required', 'string'],
        ];
    }

    public function attributes()
    {
        return [
            'name_ar' => __('dashboard.item_name_ar'),
            'name_en' => __('dashboard.item_name_en'),
            'sort' => __('dashboard.item_sort'),
            'icon' => __('dashboard.item_icon'),
        ];
    }
}
