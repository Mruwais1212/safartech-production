<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class SocialLinkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'phone' => $this['phone'],
            'email' => $this['email'],
            'whatsapp' => '+2'.$this['whatsapp'],
            'facebook' => $this['facebook'],
            'twitter' => $this['twitter'],
            'instagram' => $this['instagram'],
            'youtube' => $this['youtube'],
            'snapchat' => $this['snapchat'],
            'android_download_link' => $this['android_download_link'],
            'ios_download_link' => $this['ios_download_link'],
        ];
    }
}
