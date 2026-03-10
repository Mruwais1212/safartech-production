<?php

namespace App\Jobs;

use App\Models\TBOHotel;
use App\Services\TBOHotelBookingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CacheTBOHotelInDatabase implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public $hotelCodes;

    public $lang;

    public function __construct($hotelCodes, $lang)
    {
        $this->hotelCodes = $hotelCodes;
        $this->lang = $lang;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $hotelCodes = $this->hotelCodes;

        $hotels = (new TBOHotelBookingService)->getAllHotelDetails(implode(',', $hotelCodes));

        if (array_key_exists('HotelDetails', $hotels) && count($hotels['HotelDetails']) > 0) {
            foreach ($hotels['HotelDetails'] as $hotel) {
                $location = explode('|', $hotel['Map'] ?? '');
                $latitude = $location[0] ?? null;
                $longitude = $location[1] ?? null;

                TBOHotel::updateOrCreate(['code' => $hotel['HotelCode']], [
                    'name_ar' => $this->lang == 'ar' ? ($hotel['HotelName'] ?? null) : null,
                    'name_en' => $this->lang == 'en' ? ($hotel['HotelName'] ?? null) : null,
                    'description_ar' => $this->lang == 'ar' ? ($hotel['Description'] ?? null) : null,
                    'description_en' => $this->lang == 'en' ? ($hotel['Description'] ?? null) : null,
                    'address' => $hotel['Address'] ?? null,
                    'facilities_ar' => app()->getLocale() == 'ar' ? json_encode($hotel['HotelFacilities'] ?? []) : null,
                    'facilities_en' => app()->getLocale() == 'en' ? json_encode($hotel['HotelFacilities'] ?? []) : null,
                    'images' => json_encode($hotel['Images'] ?? []),
                    'attractions' => json_encode($hotel['Attractions'] ?? []),
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'rating' => $hotel['HotelRating'] ?? null,
                    'pin_code' => $hotel['PinCode'] ?? null,
                    'phone' => $hotel['PhoneNumber'] ?? null,
                    'fax' => $hotel['FaxNumber'] ?? null,
                    'city_code' => $hotel['CityId'] ?? null,
                    'city_name' => $hotel['CityName'] ?? null,
                    'country_code' => $hotel['CountryCode'] ?? null,
                    'country_name' => $hotel['CountryName'] ?? null,
                    'check_in_time' => $hotel['CheckInTime'] ?? null,
                    'check_out_time' => $hotel['CheckOutTime'] ?? null,
                    'cached_at' => now(),
                ]);
            }
        }
    }
}
