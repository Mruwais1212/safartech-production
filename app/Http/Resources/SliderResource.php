<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'photo' => is_file('uploads/'.$this->photo) ? asset('uploads/'.$this->photo) : asset('/images/placeholder.png'),
            // 'mobile_photo' => is_file('uploads/' . $this->mobile_photo) ? asset('uploads/' . $this->mobile_photo) : asset('/images/placeholder.png'), ,
            'title' => app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en,
            'type' => $this->type,
            'description' => app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en,
            // 'button_text'  => app()->getLocale() == 'ar' ? $this->button_text_ar : $this->description_en,
            // 'button_url'   => $this->button_url,
        ];
    }
}
