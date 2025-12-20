<?php

namespace App\Http\Requests\Panel\Backend;

use Illuminate\Foundation\Http\FormRequest;

class PageContentRequest extends FormRequest
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
            'name_ar' => ['required', 'string', $request_method ? 'unique:page_contents' :
             'unique:page_contents,name_ar,'.$id],

            'name_en' => ['required', 'string', $request_method ? 'unique:page_contents' :
             'unique:page_contents,name_en,'.$id],

            'content_ar' => ['required', 'string'],
            'content_en' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'url' => ['required', 'string'],
        ];
    }

    public function attributes()
    {
        return [
            'name_ar' => __('dashboard.page_content_name_ar'),
            'name_en' => __('dashboard.page_content_name_en'),
            'content_ar' => __('dashboard.page_content_content_ar'),
            'content_en' => __('dashboard.page_content_content_en'),
            'slug' => __('dashboard.page_content_slug'),
            'url' => __('dashboard.page_content_url'),
        ];
    }
}
