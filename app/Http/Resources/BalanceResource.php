<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BalanceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en,
            'description' => app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en,
            'user_id' => (int) $this->user_id,
            'balance_type_id' => (int) $this->balance_type_id,
            'amount' => $this->amount,
            'status' => (int) $this->status,
            'created_at' => @$this->created_at->format('Y-m-d'),
        ];
    }
}
