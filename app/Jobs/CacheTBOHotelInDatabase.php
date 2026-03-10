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
                $location = explode('|', $hotel['Map']);
                $latitude = @$location[0];
                $longitude = @$location[1];

                TBOHotel::updateOrCreate(['code' => $hotel['HotelCode']], [
                    'name_ar' => $this->lang == 'ar' ? @$hotel['HotelName'] : null,
                    'name_en' => $this->lang == 'en' ? @$hotel['HotelName'] : null,
                    'description_ar' => $this->lang == 'ar' ? @$hotel['Description'] : null,
                    'description_en' => $this->lang == 'en' ? @$hotel['Description'] : null,
                    'address' => @$hotel['Address'],
                    'facilities_ar' => app()->getLocale() == 'ar' ? json_encode(@$hotel['HotelFacilities']) : null, // json_encode(@$hotel['HotelFacilities']),
                    'facilities_en' => app()->getLocale() == 'en' ? json_encode(@$hotel['HotelFacilities']) : null, // json_encode(@$hotel['HotelFacilities']),
                    'images' => json_encode(@$hotel['Images']),
                    'attractions' => json_encode(@$hotel['Attractions'] ?? []),
                    'latitude' => @$latitude,
                    'longitude' => @$longitude,
                    'rating' => @$hotel['HotelRating'],
                    'pin_code' => @$hotel['PinCode'],
                    'phone' => @$hotel['PhoneNumber'],
                    'fax' => @$hotel['FaxNumber'],
                    'city_code' => @$hotel['CityId'],
                    'city_name' => @$hotel['CityName'],
                    'country_code' => @$hotel['CountryCode'],
                    'country_name' => @$hotel['CountryName'],
                    'check_in_time' => @$hotel['CheckInTime'],
                    'check_out_time' => @$hotel['CheckOutTime'],
                    'cached_at' => now(),
                ]);
            }
        }
    }
}
