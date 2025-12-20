<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TBOHotelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $ratingMap = [
            'OneStar' => 1,
            'TwoStar' => 2,
            'ThreeStar' => 3,
            'FourStar' => 4,
            'FiveStar' => 5,
        ];

        $hotelRating = @$this['HotelRating'];
        $numericRating = $ratingMap[$hotelRating] ?? null;

        if (@$this['HotelCode']) {
            return [
                'code' => @$this['HotelCode'],
                'name_ar' => @$this['HotelName'],
                'name_en' => @$this['HotelName'],
                'description_ar' => @$this['Description'],
                'description_en' => @$this['Description'],
                'address' => @$this['Address'],
                'facilities_ar' => @$this['HotelFacilities'] ?: [],
                'facilities_en' => @$this['HotelFacilities'] ?: [],
                'images' => @$this['Images'] ? : [url('/no-image.png')], // $this->images,
                'attractions' => [],
                'latitude' => explode('|', @$this['Map'])[0], //$this->['Map'],
                'longitude' => explode('|', @$this['Map'])[1], //$this->['Map'],
                'rating' => @$this['HotelRating'],
                'new_rating' => $numericRating,
                'pin_code' => @$this['PinCode'],
                'fax' => @$this['FaxNumber'],
                'country_code' => @$this['CountryCode'],
                'country_name' => @$this['CountryName'],
                'check_in_time' => @$this['CheckInTime'],
                'check_out_time' => @$this['CheckOutTime'],
                'rooms' => @$this['rooms'],
                'distance' => @$this['distance'],
            ];
        }

        return [
            'id' => $this->id,
            'code' => $this->code,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'address' => $this->address,
            'facilities_ar' => json_decode($this->facilities_ar),
            'facilities_en' => json_decode($this->facilities_en),
            'images' => is_array($this->images) ? $this->images : json_decode($this->images), // $this->images,
            'attractions' => $this->attractions,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'rating' => $this->rating,
            'new_rating' => $this->new_rating,
            'pin_code' => $this->pin_code,
            'phone' => $this->phone,
            'fax' => $this->fax,
            'city_code' => $this->city_code,
            'city_name' => $this->city_name,
            'country_code' => $this->country_code,
            'country_name' => $this->country_name,
            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,
            'rooms' => $this->rooms,
            'distance' => $this->distance,
        ];
    }
}
