<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en,
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'estimated_cost' => $this->estimated_cost,
            'price' => $this->price,
            'currency' => $this->currency,
            'status' => $this->status,
            'image' => is_file('uploads/'.$this->image) ? asset('uploads/'.$this->image) : asset('/images/placeholder.png'),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'city' => new CityResource($this->city),
            'country' => new CountryResource($this->country),
            'travel_interests' => TravelInterestResource::collection($this->travelInterests),
            'places' => PlaceResource::collection($this->places),
        ];
    }
}
