<?php

namespace App\Http\Requests\Panel\Backend;

use Illuminate\Foundation\Http\FormRequest;

class TravelInterestRequest extends FormRequest
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
    public function rules(): array
    {
        $id = request()->isMethod('POST') ? null : array_reverse(explode('/', request()->getRequestUri()))[0];

        return [
            'name_ar' => ['required', 'string', $id ? 'unique:travel_interests,name_ar,'.$id : 'unique:travel_interests,name_ar'],
            'name_en' => ['required', 'string', $id ? 'unique:travel_interests,name_en,'.$id : 'unique:travel_interests,name_en'],
            'type' => ['required'],
        ];
    }
}
