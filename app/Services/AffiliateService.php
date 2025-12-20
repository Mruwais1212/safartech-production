<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AffiliateService
{
    private $baseUrl;

    private $api_key;

    public function __construct()
    {
        $this->baseUrl = config('services.agoda.url');
        $this->api_key = config('services.agoda.api_key');
    }

    public function getCities()
    {
        $response = Http::withHeaders(['Authorization' => 'Bearer '.$this->api_key])
            ->get($this->baseUrl.'/cities');

        return $response->json();
    }

    public function searchHotels()
    {
        $response = Http::withHeaders(['Authorization' => 'Bearer '.$this->api_key])
            ->get(
                $this->baseUrl.'/hotels',
                [
                    'city_id' => '130443',
                    'check_in' => '2024-10-16',
                    'check_out' => '2024-10-17',
                    'adults' => 1,
                    'children' => 0,
                    'rooms' => 1,
                ]
            );

        return $response->json();
    }

    public function getHotelDetails()
    {
        $hotel_id = '123456';
        $response = Http::get($this->baseUrl."/hotels/$hotel_id");

        return $response->json();
    }

    public function getHotelRooms()
    {
        $response = Http::withHeaders(['Authorization' => 'Bearer '.$this->api_key])
            ->get(
                $this->baseUrl.'/rooms/availability',
                [
                    'hotel_id' => '123456',
                    'check_in' => '2024-08-16',
                    'check_out' => '2024-08-17',
                    'adults' => 1,
                    'children' => 0,
                    'rooms' => 1,
                ]
            );

        return $response->json();
    }

    public function getBookingLink()
    {
        $response = Http::withHeaders(['Authorization' => 'Bearer '.$this->api_key, 'Content-Type' => 'application/json'])
            ->post(
                $this->baseUrl.'/booking',
                [
                    'hotel_id' => '123456',
                    'check_in' => '2024-08-16',
                    'check_out' => '2024-08-17',
                    'adults' => 1,
                    'children' => 0,
                    'rooms' => 1,
                    'affiliate_id' => 'YourAffiliateID',
                    'customer_details' => ['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'johndoe@example.com'],
                ]
            );

        return $response->json();
    }

    public function testSearch()
    {
        $response = Http::withHeaders(['Authorization' => $this->api_key, 'Accept-Encoding' => 'gzip,deflate'])
            ->post('https://affiliateapi7643.agoda.com/affiliateservice/lt_v1', [
                'criteria' => [
                    'additional' => [
                        'currency' => 'SAR',
                        'language' => 'ar-ae',
                    ],
                    'checkInDate' => '2024-10-16',
                    'checkOutDate' => '2024-10-19',
                    'cityId' => 9395,
                ],
            ]);

        return $response->json();
    }
}
