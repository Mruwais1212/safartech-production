<?php

namespace App\Services;

use App\Models\Reservation;
use App\Support\LogRedactor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TBOHotelBookingService
{
    private $baseUrl;

    private $username;

    private $password;

    public function __construct()
    {
        $this->baseUrl = config('services.tbo.url');
        $this->username = config('services.tbo.username');
        $this->password = config('services.tbo.password');
    }

    public function searchRooms($request)
    {
        Log::info('TBO Search Rooms Request', LogRedactor::redact(['request' => $request->all()]));
        $paxRooms = $request->paxRooms ?? [];
        $rooms = $request->rooms;
        $adults = $request->adults;
        $children = $request->children + $request->infants;

        // $remainingAdults = $adults;
        // $remainingChildren = $children;

        // for ($rooms; $rooms > 0; $rooms--) {
        //     $childrenAges = [];

        //     $checkAdults = ($remainingAdults / 3) >= $rooms ? 3 : (($remainingAdults / 3) <= 1 ? ($rooms == 1 ? $remainingAdults : 1) : 2);
        //     $adults = $remainingAdults > $checkAdults ? $checkAdults : $remainingAdults;
        //     $remainingAdults -= $checkAdults;

        //     $checkChildren = ($remainingChildren / 3) >= $rooms ? 3 : (($remainingChildren / 3) < 1 && $remainingChildren == 1 ? 1 : 2);
        //     $children = $remainingChildren > $checkChildren ? $checkChildren : $remainingChildren;
        //     $remainingChildren -= $checkChildren;

        //     for ($i = 0; $i < $children; $i++) {
        //         $childrenAges[] = rand(min: 1, max: 10);
        //     }

        //     $paxRooms[] = (object) ['Adults' => $adults, 'Children' => $children > 0 ? $children : 0, 'ChildrenAges' => $childrenAges];
        // }

        session(['paxRooms' => $paxRooms]);

        Log::info('TBO Search Rooms', ['paxRooms' => $paxRooms]);

        // Ensure hotel codes don't exceed TBO API limit of 100
        $hotelCodes = $request->hotel_codes;
        if (is_string($hotelCodes)) {
            $codesArray = explode(',', $hotelCodes);
            if (count($codesArray) > 100) {
                $limitedCodes = array_slice($codesArray, 0, 100);
                $hotelCodes = implode(',', $limitedCodes);
                Log::warning('Hotel codes exceeded limit in searchRooms, trimmed to 100', [
                    'original_count' => count($codesArray),
                    'limited_count' => count($limitedCodes)
                ]);
            }
        }

        $response = Http::timeout(60)->withBasicAuth($this->username, $this->password)
            ->post($this->baseUrl.'/search', [
                'CheckIn' => $request->check_in,
                'CheckOut' => $request->check_out,
                'HotelCodes' => $hotelCodes,
                'GuestNationality' => $request->guest_nationality,
                'PaxRooms' => $paxRooms,
                'ResponseTime' => 20,
                'IsDetailedResponse' => true,
                'Filters' => (object) [
                    'NoOfRooms' => 0,
                ],
            ])->json();

        return $response;
    }

    public function searchAvailableHotelHasRooms($request)
    {
          Log::info('TBO Search Rooms Request', LogRedactor::redact(['request' => $request->all()]));

        // $paxRooms = [];
        // $rooms = $request->rooms;
        // $adults = $request->adults;
        // $children = $request->children + $request->infants;

        // $remainingAdults = $adults;
        // $remainingChildren = $children;

        // for ($rooms; $rooms > 0; $rooms--) {
        //     $childrenAges = [];

        //     $checkAdults = ($remainingAdults / 3) >= $rooms ? 3 : (($remainingAdults / 3) <= 1 ? ($rooms == 1 ? $remainingAdults : 1) : 2);
        //     $adults = $remainingAdults > $checkAdults ? $checkAdults : $remainingAdults;
        //     $remainingAdults -= $checkAdults;

        //     $checkChildren = ($remainingChildren / 3) >= $rooms ? 3 : (($remainingChildren / 3) < 1 && $remainingChildren == 1 ? 1 : 2);
        //     $children = $remainingChildren > $checkChildren ? $checkChildren : $remainingChildren;
        //     $remainingChildren -= $checkChildren;

        //     for ($i = 0; $i < $children; $i++) {
        //         $childrenAges[] = rand(min: 1, max: 10);
        //     }

        //     $paxRooms[] = (object) ['Adults' => $adults, 'Children' => $children > 0 ? $children : 0, 'ChildrenAges' => $childrenAges];
        // }

        // session(['paxRooms' => $paxRooms]);

        // Ensure hotel codes don't exceed TBO API limit of 100
        $hotelCodes = $request->hotel_codes;
        if (is_string($hotelCodes)) {
            $codesArray = explode(',', $hotelCodes);
            if (count($codesArray) > 100) {
                $limitedCodes = array_slice($codesArray, 0, 100);
                $hotelCodes = implode(',', $limitedCodes);
                Log::warning('Hotel codes exceeded limit, trimmed to 100', [
                    'original_count' => count($codesArray),
                    'limited_count' => count($limitedCodes)
                ]);
            }
        }
        Log::info('TBO Search Rooms', ['paxRooms' => $request->paxRooms]);
 
        $searchRequest = [
            'CheckIn' => $request->check_in,
            'CheckOut' => $request->check_out,
            'HotelCodes' => $hotelCodes,
            'GuestNationality' => $request->guest_nationality,
            'PaxRooms' => $request->paxRooms,
            'ResponseTime' => 20,
            'IsDetailedResponse' => false,
            'Filters' => (object) [
                'NoOfRooms' => 0,
                'Refundable' => $request->refundable ?? false,
                'MealType' => $request->meal_type ?? 'All',
            ]
        ];
      
        //Log::info('TBO Search Rooms', ['paxRooms' => $paxRooms]);
        Log::info('searchRequest All Data Search', LogRedactor::redact(['data' => $searchRequest]));

        return Http::timeout(60)->withBasicAuth($this->username, $this->password)
            ->post($this->baseUrl.'/search', $searchRequest)
            ->json();
    }

 
    public function getHotelCodeList($city_code, $is_detailed_response = true)
    {
        return Http::timeout(1200)->withBasicAuth($this->username, $this->password)
            ->post($this->baseUrl.'/TBOHotelCodeList', [
                'CityCode' => $city_code,
                'IsDetailedResponse' => $is_detailed_response,
            ])->json();
    }

    public function getHotelDetails($hotel_code)
    {
        return Http::timeout(1200)->withBasicAuth($this->username, $this->password)
            ->post($this->baseUrl.'/Hoteldetails', [
                'Hotelcodes' => $hotel_code,
                'Language' => app()->getLocale(),
                'IsDetailedResponse' => true,
            ])->json();
    }

    public function getAllHotelDetails($hotel_codes)
    {
        // Ensure we don't exceed the TBO API limit of 100 hotel codes
        $codesArray = is_string($hotel_codes) ? explode(',', $hotel_codes) : $hotel_codes;
        $limitedCodes = array_slice($codesArray, 0, 100);
        $finalCodes = is_array($hotel_codes) ? implode(',', $limitedCodes) : implode(',', $limitedCodes);
        
        Log::info('TBO getAllHotelDetails request', [
            'original_count' => count($codesArray),
            'limited_count' => count($limitedCodes),
            'hotel_codes' => $finalCodes
        ]);

        return Http::timeout(1200)->withBasicAuth($this->username, $this->password)
            ->post($this->baseUrl.'/Hoteldetails', [
                'Hotelcodes' => $finalCodes,
                'Language' => app()->getLocale(),
                'IsDetailedResponse' => true,
            ])->json();
    }

    public function getCountriesList()
    {
        return Http::timeout(1200)->withBasicAuth($this->username, $this->password)
            ->get($this->baseUrl.'/CountryList')->json();
    }

    public function getCityList($country_code)
    {
        return Http::timeout(1200)->withBasicAuth($this->username, $this->password)
            ->post($this->baseUrl.'/CityList', [
                'CountryCode' => $country_code,
            ])->json();
    }

    public function perBook($booking_code)
    {
        Log::info("prebook", ['BookingCode' => $booking_code]);
        $pre = Http::timeout(1200)->withBasicAuth($this->username, $this->password)
        ->post($this->baseUrl.'/PreBook', [
            'BookingCode' => $booking_code,
            'PaymentMode' => 'Limit',
        ])->json();
        Log::info("prebook-info", ['booking_code' => $booking_code, 'status_code' => $pre['Status']['Code'] ?? null]);
        return $pre;
    }

    public function cancel($hotel)
    {
        if($hotel->confirmation_number){
            Log::info("canceled hotel", ['booking_code' => $hotel->booking_code, 'reservation_id' => $hotel->reservation_id]);
            $pre = Http::timeout(1200)->withBasicAuth($this->username, $this->password)
            ->post($this->baseUrl.'/Cancel', [
                'ConfirmationNumber' => $hotel->confirmation_number,
            ])->json();
            Log::info("cancel success", ['booking_code' => $hotel->booking_code, 'status_code' => $pre['Status']['Code'] ?? null]);
            if ($pre['Status']['Code'] == 200) {
                $hotel->status = $hotel->is_refundable == 1 ? 2 : 3;
                $hotel->save();
                if($hotel->is_refundable == 1){
                    $reservation = Reservation::find($hotel->reservation_id);
                    MoyasarPaymentService::refund($reservation->payment_id, $hotel->price_with_tax);
                }
                if($hotel->is_refundable != 1){
                    $reservation = Reservation::find($hotel->reservation_id);
                    MoyasarPaymentService::refund($reservation->payment_id, $hotel->tax_amount);
                }
            }
            return $pre;
        }else{
            $hotel->status = $hotel->is_refundable == 1 ? 2 : 3;
            $hotel->save();
            if($hotel->is_refundable == 1){
                $reservation = Reservation::find($hotel->reservation_id);
                MoyasarPaymentService::refund($reservation->payment_id, $hotel->price_with_tax);
            }
            if($hotel->is_refundable != 1){
                $reservation = Reservation::find($hotel->reservation_id);
                MoyasarPaymentService::refund($reservation->payment_id, $hotel->tax_amount);
            }
        }
       return null;
    }

    public function bookingRoom($request)
    {
        Log::info('booking-room', [
            'booking_code' => $request->booking_code,
            'client_reference_id' => $request->client_reference_id,
            'booking_reference_id' => $request->booking_reference_id,
        ]);

        $book = Http::timeout(1200)->withBasicAuth($this->username, $this->password)
        ->post($this->baseUrl.'/Book', [
            'BookingCode' => $request->booking_code,
            'CustomerDetails' => $request->customer_details,
            'ClientReferenceId' => $request->client_reference_id,
            'BookingReferenceId' => $request->booking_reference_id,
            'TotalFare' => $request->total_fare,
            'EmailId' => $request->email,
            'PhoneNumber' => $request->phone_number,
            'BookingType' => 'Voucher',
            'PaymentMode' => 'Limit',
        ])->json();

        Log::info('booking-room-response', ['booking_code' => $request->booking_code, 'status_code' => $book['Status']['Code'] ?? null]);

        return $book;
    }

    public function bookingDetails($hotel, $bookingReferenceId = null)
    {
        $requestData = [
            'PaymentMode' => 'Limit',
        ];
        
        // Use booking reference ID if provided, otherwise use confirmation number
        if ($bookingReferenceId) {
            $requestData['BookingReferenceId'] = $bookingReferenceId;
            Log::info('Fetching booking details using BookingReferenceId: ' . $bookingReferenceId);
        } else {
            $requestData['ConfirmationNumber'] = $hotel->confirmation_number;
            Log::info('Fetching booking details using ConfirmationNumber: ' . $hotel->confirmation_number);
        }

        $book = Http::timeout(1200)->withBasicAuth($this->username, $this->password)
        ->post($this->baseUrl.'/BookingDetail', $requestData)->json();
        
        Log::info('booking details response', ['booking_reference_id' => $bookingReferenceId, 'confirmation_number' => $hotel->confirmation_number, 'status_code' => $book['Status']['Code'] ?? null]);

        return $book;
    }
}
