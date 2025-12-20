<?php

namespace App\Http\Requests\Panel\Auth;

use Illuminate\Foundation\Http\FormRequest;

class FixedPageRequest extends FormRequest
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
            'name_ar' => ['required', 'string', $request_method ? 'unique:fixed_pages' :
             'unique:fixed_pages,name_ar,'.$id],

            'name_en' => ['required', 'string', $request_method ? 'unique:fixed_pages' :
             'unique:fixed_pages,name_en,'.$id],

            'content_ar' => ['required', 'string'],
            'content_en' => ['required', 'string'],
            'slug' => ['required', 'string'],
        ];
    }

    public function attributes()
    {
        return [
            'name_ar' => __('dashboard.fixed_page_name_ar'),
            'name_en' => __('dashboard.fixed_page_name_en'),
            'content_ar' => __('dashboard.fixed_page_content_ar'),
            'content_en' => __('dashboard.fixed_page_content_en'),
            'slug' => __('dashboard.fixed_page_slug'),
        ];
    }
}
