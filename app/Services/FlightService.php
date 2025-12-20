<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FlightService
{
    private $baseUrl;

    private $userId;

    private $userPassword;

    public function __construct()
    {
        $this->baseUrl = config('services.flight.baseUrl');
        $this->userId = config('services.flight.userId');
        $this->userPassword = config('services.flight.userPassword');
    }

    public function search()
    {
        $ip = Http::get('https://api.ipify.org?format=json')->json()['ip'];

        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($this->baseUrl.'/availability', [
            'user_id' => $this->userId,
            'user_password' => $this->userPassword,
            'access' => 'Test',
            'ip_address' => $ip, //check not work because this ip
            'requiredCurrency' => 'USD',
            'journeyType' => 'Return',
            'OriginDestinationInfo' => [
                [
                    'departureDate' => '2024-09-19',
                    'returnDate' => '2024-09-29',
                    'airportOriginCode' => 'SFO',
                    'airportDestinationCode' => 'LAX',
                ],
            ],
            'class' => 'Economy',
            'adults' => 2,
            'childs' => 1,
            'infants' => 1,
        ])->json();
    }
}
