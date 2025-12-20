<?php

namespace App\Http\Requests\Panel\Backend;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $phone_regex = '/^(05)[0-9]{8}$|^(5)[0-9]{8}$/';
        $id = request()->isMethod('POST') ?: array_reverse(explode('/', request()->getRequestUri()))[0];

        return [
            'name' => ['required', 'string'],
            'email' => ['nullable', 'string', 'email', request()->isMethod('POST') ? 'unique:users,email' : 'unique:users,email,'.$id],
            'phone' => ['required', 'numeric', 'regex:'.$phone_regex, request()->isMethod('POST') ? 'unique:users' : 'unique:users,phone,'.$id],
            'phone_code' => ['required', 'numeric'],
            'photo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,gif,svg,webp,avif,bmp,tiff,ico'],
            'player_position' => ['required', 'in:1,2,3,4'],
        ];
    }

    public function attributes()
    {
        return [
            'phone_code' => __('dashboard.phone_code'),
            'player_position' => __('dashboard.player_position'),
        ];
    }
}
