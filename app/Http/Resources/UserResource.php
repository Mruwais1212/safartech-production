<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_code' => $this->phone_code,
            'activate' => $this->activate,
            'block' => (int) $this->block,
            'user_type_id' => $this->user_type_id,
            'receive_notification' => (int) $this->receive_notification,
            'photo' => is_file('uploads/'.$this->photo) ? asset('uploads/'.$this->photo) : asset('/images/default_user.png'),
            'lang' => $this->lang,
            'token' => $this->token,
            'balance' => $this->balance ?: 0,
        ];
    }
}
