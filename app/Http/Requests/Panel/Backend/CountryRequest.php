<?php

namespace App\Http\Requests\Panel\Backend;

use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
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
        $id = request()->isMethod('POST') ?: array_reverse(explode('/', request()->getRequestUri()))[0];

        return [
            'name_ar' => ['required', 'string', 'max:255', request()->isMethod('POST') ? 'unique:countries,name_ar' : 'unique:countries,name_ar,'.$id],
            'name_en' => ['required', 'string', 'max:255', request()->isMethod('POST') ? 'unique:countries,name_en' : 'unique:countries,name_en,'.$id],
        ];
    }
}
