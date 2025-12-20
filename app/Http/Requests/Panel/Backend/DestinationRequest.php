<?php

namespace App\Http\Requests\Panel\Backend;

use Illuminate\Foundation\Http\FormRequest;

class DestinationRequest extends FormRequest
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
        return [
            'description_ar' => ['required', 'string'],
            'description_en' => ['required', 'string'],
            'country_id' => ['required', 'exists:countries,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'travel_interests' => ['required', 'array'],
            'travel_interests.*' => ['exists:travel_interests,id'],
            'estimated_cost' => ['required', 'numeric'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ];
    }

    public function attributes()
    {
        return [
            'description_ar' => __('dashboard.description_ar'),
            'description_en' => __('dashboard.description_en'),
            'country_id' => __('dashboard.country_id'),
            'city_id' => __('dashboard.city_id'),
            'travel_interests' => __('dashboard.travel_interests'),
            'travel_interests.*' => __('dashboard.travel_interests'),
            'estimated_cost' => __('dashboard.estimated_cost'),
            'latitude' => __('dashboard.latitude'),
            'longitude' => __('dashboard.longitude'),
        ];
    }
}
