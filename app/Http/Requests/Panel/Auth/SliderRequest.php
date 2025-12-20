<?php

namespace App\Http\Requests\Panel\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
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

        return [
            'title_ar' => [$request_method ? 'required' : 'nullable', 'string', 'max:255'],
            'title_en' => [$request_method ? 'required' : 'nullable', 'string', 'max:255'],
            'description_ar' => ['required', 'string'],
            'description_en' => ['required', 'string'],
            'main_slider_id' => ['required'],
            'photo' => [$request_method ? 'required' : 'nullable'],
            'mobile_photo' => ['nullable'],
            // 'type'           => [$request_method ? 'required' : 'nullable', 'in:1,2'],
            // 'button_text_ar' => ['nullable', 'string', 'max:255'],
            // 'button_text_en' => ['nullable', 'string', 'max:255'],
            // 'button_url'     => ['nullable', 'string', 'max:255'],
            // 'text_color'     => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes()
    {
        return [
            'title_ar' => __('dashboard.slider_title_ar'),
            'title_en' => __('dashboard.slider_title_en'),
            'main_slider_id' => __('dashboard.slider_main_slider_id'),
            'photo' => __('dashboard.slider_photo'),
            'mobile_photo' => __('dashboard.slider_mobile_photo'),
            'type' => __('dashboard.slider_type'),
            'button_text_ar' => __('dashboard.slider_button_text_ar'),
            'button_text_en' => __('dashboard.slider_button_text_en'),
            'button_url' => __('dashboard.slider_button_url'),
            'text_color' => __('dashboard.slider_text_color'),
        ];
    }

    public function prepareForValidation()
    {
        if (request()->hasFile('photo')) {
            $checkPhotoIsImage = in_array(
                request()->photo->getClientOriginalExtension(),
                ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp', 'tiff']
            );
            $this->merge(
                [
                    'type' => $checkPhotoIsImage ? 1 : 2,
                    'main_slider_id' => 1,
                ]
            );
        }
    }
}
