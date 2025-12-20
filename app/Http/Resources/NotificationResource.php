<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => (int) $this->user_id,
            'title' => app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en,
            'message' => app()->getLocale() == 'ar' ? $this->message_ar : $this->message_en,
            'type' => $this->type,
            'status' => (int) $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created' => $this->created_at->diffForHumans(),
            'photo' => is_file('uploads/'.$this->photo) ? asset('uploads/'.$this->photo) : asset('/images/placeholder.png'),
            'user_type_id' => $this->user_type_id,
            'item_id' => $this->item_id,
            'item_type' => $this->item_type,
            'item' => $this->itemResource(),
        ];
    }
}
