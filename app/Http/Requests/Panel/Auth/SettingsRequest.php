<?php

namespace App\Http\Requests\Panel\Auth;

use App\Models\Panel\Setting;
use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
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
            'settings' => ['required', 'array'],
        ];
    }

    public function passedValidation()
    {
        $settings = [];
        foreach (request()->settings as $key => $setting) {
            if ($setting != null || is_file($setting)) {
                $settings[$key] = $setting;
            }

            if (is_file(request()->settings[$key])) {
                $setting = Setting::find($key);
                $name = 'setting-'.$setting->code.'.png';
                request()->settings[$key]->move('settings', $name);
                $settings[$key] = $name;
            }
        }

        return $this->merge(['handle_settings' => $settings]);
    }
}
