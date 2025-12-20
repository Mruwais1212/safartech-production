<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserInformationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_code' => $this->phone_code,
            'phone' => $this->phone,
            'photo' => is_file('uploads/'.$this->photo) ? asset('uploads/'.$this->photo) : asset('/images/default_user.png'),
            'user_type_id' => (int) @$this->user_type_id,
            'block' => (int) $this->block,
        ];
    }
}
