<?php

namespace App\Services;

use App\Helpers\PriceCalculationHelper;
use App\Jobs\FetchBookingDetailsJob;
use App\Jobs\FetchFlightBookingDetailsJob;
use App\Models\Airport;
use App\Models\Panel\Setting;
use App\Models\Reservation;
use App\Models\ReservationFlight;
use App\Support\LogRedactor;
use App\Support\SupplierFieldMap;
use Illuminate\Support\Facades\DB;
use App\Traits\PriceCalculationTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ReservationService
{
    use PriceCalculationTrait;
    public static function add()
    {

        $hotel_commession = Setting::where('code', 'hotel_profit')->first() ? Setting::where('code', 'hotel_profit')->first()->value : 12;
        $flight_commession = Setting::where('code', 'flight_profit')->first() ? Setting::where('code', 'flight_profit')->first()->value : 7;
        $airPortFrom = Airport::find(Session::get('searchParams.origin'));
        $airPortTo = Airport::find(Session::get('searchParams.destination'));
        $airPortToo = Airport::where('city_code', Session::get('preferences.destination'))->first();

        $preferences = Session::get('preferences');
        $flight = Session::get('flight');
        $hotel = Session::get('hotel');
        $paxRoomsSession = Session::get('paxRooms');
        $selectedRoomSession = Session::get('hotel.selected_room');
        $outboundFlightSession = Session::get('outbound_flight');
        $tripType = $preferences['trip_type'] ?? null;
        $reservationType = 'inside';

        foreach ([$airPortFrom, $airPortTo, $airPortToo] as $airport) {
            if ($airport && $airport->country_code !== 'SA') {
                $reservationType = 'outside';
                break;
            }
        }

        $flightAmount = $flight['flight_amount'] ?? 0;
        $flightAmount = PriceCalculationHelper::calculatePriceAmounts($flightAmount, $reservationType,'flight');
        $inboundAmount = Session::has('flight.inbound_flightSegment') ? $flight['inbound_flight_amount'] ?? 0 : 0;
        $inboundAmount = PriceCalculationHelper::calculatePriceAmounts($inboundAmount, $reservationType,'flight');

        $hotelAmount = $hotel['TotalFareHotel'] ?? 0;
        $hotelAmount = PriceCalculationHelper::calculatePriceAmounts($hotelAmount, $reservationType,'hotel');

        $total_price_calculate = match ($tripType) {
            1 =>PriceCalculationHelper::mergeCalculatePrices([$flightAmount , $inboundAmount , $hotelAmount]) ,
            2 => $hotelAmount,
            3 => PriceCalculationHelper::mergeCalculatePrices([$flightAmount + $inboundAmount]),
            default => 0,
        };

        $price = $total_price_calculate['total_price'];

        do {
            $integerId = rand(1, 9999999999);
        } while (Reservation::where('uuid', $integerId)->exists());
        
        // Generate initial booking reference ID with max length of 25 characters
        $bookingReferenceId = 'BK' . time() . rand(100, 999);
        $bookingReferenceId = substr($bookingReferenceId, 0, 25);
        
        $reservationData = [
            'type' => Session::get('preferences.trip_type'),
            'trip_id' => Session::get('preferences.trip_id'),
            'price' => number_format($price, 2, '.', '') ,
            'currency' => Session::get('hotel.CurrencyHotel') ?? 'SAR',
            'unit_price' => $price,
            'uuid' => $integerId,
            'search_request'=> json_encode($paxRoomsSession),
            'booking_reference_id' => $bookingReferenceId,
        ];
        if ($airPortTo && $airPortTo->city) {
            $reservationData['to_id'] = Session::get('searchParams.destination');
            $reservationData['to_city'] = $airPortTo->city;
//            $reservationData['price'] = $total_price_calculate['total_price'] ?? $price;
            $reservationData['reservation_type'] = $reservationType;
        }
        if ($airPortToo && $airPortToo->city) {
            $reservationData['to_id'] = $airPortToo->id;
            $reservationData['to_city'] = $airPortToo->city;
//            $reservationData['price'] = $total_price_calculate['total_price'] ?? $price;
            $reservationData['reservation_type'] = $reservationType;
        }
        if ($airPortFrom && $airPortFrom->city) {
            $reservationData['from_id'] = Session::get('searchParams.origin');
            $reservationData['from_city'] = $airPortFrom->city;
        }



        $reservation = Reservation::updateOrCreate(
            [
                'user_id' => auth('web')->id(),
                'payment_method' => 0,
            ],
            $reservationData
        );
        $reservation->hotel()->delete();
        $reservation->flight()->delete();
        $reservation->passengers()->delete();

        if ($tripType == 1 || $tripType == 2) {
            $selectedHotelJson = Session::get('hotel.selected_hotel');
            $selected_hotel = $selectedHotelJson ? json_decode($selectedHotelJson) : null;

            if (! $selected_hotel) {
                Log::warning('ReservationService: hotel.selected_hotel is missing or invalid JSON', [
                    'user_id' => auth('web')->id(),
                ]);
                return null;
            }

            $first_tax_rate_hotel = $reservationType == 'inside' ? 15 : 0;
            $first_tax_amount_hotel = Session::get('hotel.TotalFareHotel') * $first_tax_rate_hotel / 100;

            $administrative_tax_rate_hotel = (int)$hotel_commession;
            $administrative_tax_amount_hotel = Session::get('hotel.TotalFareHotel') * $administrative_tax_rate_hotel / 100;

            $second_tax_rate_hotel = 15;
            $second_tax_amount_hotel = $administrative_tax_amount_hotel * $second_tax_rate_hotel / 100;

            $price_without_tax_hotel = Session::get('hotel.TotalFareHotel');
            $tax_amount_hotel = $first_tax_amount_hotel + $administrative_tax_amount_hotel + $second_tax_amount_hotel;

            $reservation->hotel()->create([
                'reservation_id' => $reservation->id,
                'hotel_id' => $selected_hotel->code,
                'check_in' => Session::get('preferences')['start_date'],
                'check_out' => Session::get('preferences')['end_date'],
                'rooms' => Session::get('preferences')['rooms'],
                'adults' => Session::get('preferences')['adults'],
                'children' => Session::get('preferences')['children'],
                'price' => Session::get('hotel.TotalFareHotel'),
                'currency' => Session::get('hotel.CurrencyHotel'),
                'hotel_name' => $selected_hotel->name_en ?: $selected_hotel->name_ar,
                'hotel_image' => isset($selected_hotel->images[0]) ? $selected_hotel->images[0] : null,
                'hotel_address' => $selected_hotel->address,
                'phone' => $selected_hotel->phone??'-',
                'hotel_rate' => $selected_hotel->rating,
                'booking_code' => Session::get('hotel.BookingCodeHotel'),
                'confirmation_number' => null,
                'country_id' => $selected_hotel->country_id ?? null,
                'city_id' => $selected_hotel->city_id ?? null,
                'date_from' => Session::get('preferences')['start_date'],
                'date_to' => Session::get('preferences')['end_date'],


                'first_tax_rate' => $first_tax_rate_hotel ?? 0,
                'first_tax_amount' => $first_tax_amount_hotel ?? 0,

                'second_tax_rate' => $second_tax_rate_hotel ?? 0,
                'second_tax_amount' => $second_tax_amount_hotel ?? 0,

                'administrative_tax_amount' => $administrative_tax_amount_hotel ?? 0,
                'administrative_tax_rate' => $administrative_tax_rate_hotel ?? 0,

                'price_without_tax' => $price_without_tax_hotel ?? 0,
                'price_with_tax' => $price_without_tax_hotel + $tax_amount_hotel,
                'tax_amount' => $tax_amount_hotel ?? 0,
                
                // Save room details as JSON
                'room_details' => $selectedRoomSession ? 
                    (is_string($selectedRoomSession) ? 
                        $selectedRoomSession : 
                        json_encode($selectedRoomSession)
                    ) : null,
            ]);
        }


        if ($tripType == 1 || $tripType == 3) {
            if (Session::get('outbound_flight')) {
                $first_tax_rate = $reservationType == 'inside' ? 15 : 0;
                $first_tax_amount = $flight['flight_amount'] * $first_tax_rate / 100;

                $administrative_tax_rate = (int)$flight_commession;
                $administrative_tax_amount = $flight['flight_amount'] * $administrative_tax_rate / 100;

                $second_tax_rate = 15;
                $second_tax_amount = $administrative_tax_amount * $second_tax_rate / 100;

                $price_without_tax = $flight['flight_amount'];
                $tax_amount = $first_tax_amount + $administrative_tax_amount + $second_tax_amount;
                $outbound_flight = $reservation->flight()->create([
                    'reservation_id' => $reservation->id,
                    'result_index' => Session::get('outbound_flight')['ResultIndex'],
                    SupplierFieldMap::FLIGHT_TRACE_ATTR => Session::get(SupplierFieldMap::FLIGHT_TRACE_SESSION_KEY),
                    'is_lcc' => Session::get('outbound_flight')['IsLCC'],
                    'is_refundable' => Session::get('outbound_flight')['IsRefundable'],
                    'last_ticket_date' => Session::get('outbound_flight')['LastTicketDate'],
                    'total_price' => $flight['flight_amount'],
                    'is_direct' => count(Session::get('outbound_flight')['Segments'][0]) == 1 ? true : false,
                    'flight_class' => Session::get('outbound_flight')['Segments'][0][0]['CabinClass'],
                    'pnr' => null,
                    'flight_json' => json_encode($outboundFlightSession),
                    'flight_from' => Session::get('outbound_flight')['Segments'][0][0]['Origin']['Airport']['CityName'],
                    'flight_to' => Session::get('outbound_flight')['Segments'][0][count(Session::get('outbound_flight')['Segments'][0]) - 1]['Destination']['Airport']['CityName'],
                    'departure_time' => Session::get('outbound_flight')['Segments'][0][0]['Origin']['DepTime'],
                    'arrival_time' => Session::get('outbound_flight')['Segments'][0][count(Session::get('outbound_flight')['Segments'][0]) - 1]['Destination']['ArrTime'],

                    'first_tax_rate' => $first_tax_rate ?? 0,
                    'first_tax_amount' => $first_tax_amount ?? 0,

                    'second_tax_rate' => $second_tax_rate ?? 0,
                    'second_tax_amount' => $second_tax_amount ?? 0,

                    'administrative_tax_amount' => $administrative_tax_amount ?? 0,
                    'administrative_tax_rate' => $administrative_tax_rate ?? 0,

                    'price_without_tax' => $price_without_tax ?? 0,
                    'price_with_tax' => $price_without_tax + $tax_amount,
                    'tax_amount' => $tax_amount ?? 0,
                ]);

                foreach (Session::get('outbound_flight')['Segments'][0] as $key => $value) {
                    $segment = $outbound_flight->segments()->create([
                        'flight_id' => $outbound_flight->id,
                        'baggage' => $value['Baggage'] ?? '0 KG',
                        'cabin_baggage' => $value['CabinBaggage'] ?: '0 KG',
                        'cabin_class' => $value['CabinClass'],
                        'trip_indicator' => $value['TripIndicator'],
                        'segment_indicator' => $value['SegmentIndicator'],
                        'duration' => $value['Duration'],
                        'no_of_seat_available' => isset($value['NoOfSeatAvailable']) ? $value['NoOfSeatAvailable'] : 0,
                    ]);

                    $segment->origin()->create([
                        'flight_segment_id' => $segment->id,
                        'airport_code' => $value['Origin']['Airport']['AirportCode'],
                        'airport_name' => $value['Origin']['Airport']['AirportName'],
                        'city_code' => $value['Origin']['Airport']['CityCode'],
                        'city_name' => $value['Origin']['Airport']['CityName'],
                        'country_code' => $value['Origin']['Airport']['CountryCode'],
                        'country_name' => $value['Origin']['Airport']['CountryName'],
                        'dep_time' => $value['Origin']['DepTime'],
                    ]);

                    $segment->destination()->create([
                        'flight_segment_id' => $segment->id,
                        'airport_code' => $value['Destination']['Airport']['AirportCode'],
                        'airport_name' => $value['Destination']['Airport']['AirportName'],
                        'city_code' => $value['Destination']['Airport']['CityCode'],
                        'city_name' => $value['Destination']['Airport']['CityName'],
                        'country_code' => $value['Destination']['Airport']['CountryCode'],
                        'country_name' => $value['Destination']['Airport']['CountryName'],
                        'arr_time' => $value['Destination']['ArrTime'],
                    ]);

                    $segment->airline()->create([
                        'flight_segment_id' => $segment->id,
                        'airline_code' => $value['Airline']['AirlineCode'],
                        'airline_name' => $value['Airline']['AirlineName'],
                        'flight_number' => $value['Airline']['FlightNumber'],
                        'fare_class' => $value['Airline']['FareClass'],
                    ]);
                }
            }
            if (Session::get('inbound_flight')||count(Session::get('outbound_flight')['Segments']) > 1) {
                $segment_index=Session::get('inbound_flight')?0:1;
                $outbound_id=!Session::get('inbound_flight')?$outbound_flight->id:0;
                $inboundFlight=Session::get('inbound_flight')?:Session::get('outbound_flight');
                // Use the dedicated inbound amount when a separate inbound flight was chosen;
                // fall back to the outbound amount for round-trip tickets stored as Segments[1].
                $inboundPriceBase = Session::get('inbound_flight')
                    ? ($flight['inbound_flight_amount'] ?? $flight['flight_amount'] ?? 0)
                    : ($flight['flight_amount'] ?? 0);

                $first_tax_rate = $reservationType == 'inside' ? 15 : 0;
                $first_tax_amount = $inboundPriceBase * $first_tax_rate / 100;

                $administrative_tax_rate = (int)$flight_commession;
                $administrative_tax_amount = $inboundPriceBase * $administrative_tax_rate / 100;

                $second_tax_rate = 15;
                $second_tax_amount = $administrative_tax_amount * $second_tax_rate / 100;

                $price_without_tax = $inboundPriceBase;
                $tax_amount = $first_tax_amount + $administrative_tax_amount + $second_tax_amount;

                $inbound_flight = $reservation->flight()->create([
                    'reservation_id' => $reservation->id,
                    'result_index' => $inboundFlight['ResultIndex'],
                    SupplierFieldMap::FLIGHT_TRACE_ATTR => Session::get(SupplierFieldMap::FLIGHT_TRACE_SESSION_KEY),
                    'is_lcc' => $inboundFlight['IsLCC'],
                    'is_refundable' => $inboundFlight['IsRefundable'],
                    'last_ticket_date' => $inboundFlight['LastTicketDate'],
                    'total_price' => $inboundPriceBase,
                    'is_direct' => count(value: $inboundFlight['Segments'][$segment_index]) == 1 ? true : false,
                    'flight_class' => $inboundFlight['Segments'][$segment_index][0]['CabinClass'],
                    'pnr' => null,
                    'flight_json' => json_encode($inboundFlight),
                    'flight_from' => $inboundFlight['Segments'][$segment_index][0]['Origin']['Airport']['CityName'],
                    'flight_to' => $inboundFlight['Segments'][$segment_index][count($inboundFlight['Segments'][$segment_index]) - 1]['Destination']['Airport']['CityName'],
//                    'flight_to' => $inboundFlight['Segments'][$segment_index][0]['Destination']['Airport']['CityName'],
                    'departure_time' => $inboundFlight['Segments'][$segment_index][0]['Origin']['DepTime'],
                    // 'arrival_time' => Session::get('inbound_flight')['Segments'][0][count(Session::get('outbound_flight')['Segments']) - 1]['Destination']['ArrTime'],
                    'arrival_time' => $inboundFlight['Segments'][$segment_index][0]['Destination']['ArrTime'],

                    'first_tax_rate' => $first_tax_rate ?? 0,
                    'first_tax_amount' => $first_tax_amount ?? 0,

                    'second_tax_rate' => $second_tax_rate ?? 0,
                    'second_tax_amount' => $second_tax_amount ?? 0,

                    'administrative_tax_amount' => $administrative_tax_amount ?? 0,
                    'administrative_tax_rate' => $administrative_tax_rate ?? 0,

                    'price_without_tax' => $price_without_tax ?? 0,
                    'price_with_tax' => $price_without_tax + $tax_amount,
                    'tax_amount' => $tax_amount ?? 0,
                    'outbound_id'=>$outbound_id
                ]);

                foreach ($inboundFlight['Segments'][$segment_index] as $key => $value) {
                    $segment = $inbound_flight->segments()->create([
                        'flight_id' => $inbound_flight->id,
                        'baggage' => $value['Baggage'] ?? '0 KG',
                        'cabin_baggage' => $value['CabinBaggage'] ?: '0 KG',
                        'cabin_class' => $value['CabinClass'],
                        'trip_indicator' => $value['TripIndicator'],
                        'segment_indicator' => $value['SegmentIndicator'],
                        'duration' => $value['Duration'],
                        'no_of_seat_available' => $value['NoOfSeatAvailable'] ?? 1,
                    ]);

                    $segment->origin()->create([
                        'flight_segment_id' => $segment->id,
                        'airport_code' => $value['Origin']['Airport']['AirportCode'],
                        'airport_name' => $value['Origin']['Airport']['AirportName'],
                        'city_code' => $value['Origin']['Airport']['CityCode'],
                        'city_name' => $value['Origin']['Airport']['CityName'],
                        'country_code' => $value['Origin']['Airport']['CountryCode'],
                        'country_name' => $value['Origin']['Airport']['CountryName'],
                        'dep_time' => $value['Origin']['DepTime'],
                    ]);

                    $segment->destination()->create([
                        'flight_segment_id' => $segment->id,
                        'airport_code' => $value['Destination']['Airport']['AirportCode'],
                        'airport_name' => $value['Destination']['Airport']['AirportName'],
                        'city_code' => $value['Destination']['Airport']['CityCode'],
                        'city_name' => $value['Destination']['Airport']['CityName'],
                        'country_code' => $value['Destination']['Airport']['CountryCode'],
                        'country_name' => $value['Destination']['Airport']['CountryName'],
                        'arr_time' => $value['Destination']['ArrTime'],
                    ]);

                    $segment->airline()->create([
                        'flight_segment_id' => $segment->id,
                        'airline_code' => $value['Airline']['AirlineCode'],
                        'airline_name' => $value['Airline']['AirlineName'],
                        'flight_number' => $value['Airline']['FlightNumber'],
                        'fare_class' => $value['Airline']['FareClass'],
                    ]);
                }
            }

        }

        foreach (Session::get('passengers', []) as $passenger) {
            $reservation->passengers()->create([
                'first_name'          => $passenger['first_name'] ?? '',
                'last_name'           => $passenger['last_name'] ?? '',
                'pax_type'            => $passenger['pax_type'] ?? 1,
                'birth_date'          => $passenger['birth_date'] ?? null,
                'email'               => $passenger['email'] ?? null,
                'phone'               => $passenger['phone'] ?? null,
                'nationality'         => $passenger['nationality'] ?? null,
                'gender'              => $passenger['gender'] ?? null,
                'address'             => $passenger['address'] ?? null,
                'passport_number'     => $passenger['passport_number'] ?? '',
                'passport_expiry_date' => $passenger['passport_expiry_date'] ?? '1900-01-01',
                'title'               => $passenger['title'] ?? null,
            ]);
        }

        return $reservation;
    }

    public static function completeBooking($id, ?string $correlationId = null)
    {
        $reservation = Reservation::where('id', $id)->first();
        if (! $reservation) {
            return [
                'success' => false,
                'errors' => ['Reservation not found'],
            ];
        }

        $lockState = DB::transaction(function () use ($id) {
            $lockedReservation = Reservation::lockForUpdate()->find($id);

            if (! $lockedReservation) {
                return ['allowed' => false, 'error' => 'Reservation not found'];
            }

            if ($lockedReservation->booking_completed_at !== null) {
                return ['allowed' => false, 'already_completed' => true];
            }

            $isFinalizedReservation = (int) $lockedReservation->status === 1
                || ((int) $lockedReservation->status === 2 && ! self::hasPendingSupplierProcessing($lockedReservation));

            if ($isFinalizedReservation) {
                return [
                    'allowed' => false,
                    'finalized_status' => (int) $lockedReservation->status,
                ];
            }

            $lockWindowIsActive = $lockedReservation->booking_in_progress
                && $lockedReservation->booking_processing_started_at
                && $lockedReservation->booking_processing_started_at->gt(now()->subMinutes(15));

            if ($lockWindowIsActive) {
                return ['allowed' => false, 'already_processing' => true];
            }

            $lockedReservation->update([
                'booking_in_progress' => true,
                'booking_processing_started_at' => now(),
            ]);

            return ['allowed' => true];
        });

        if (! ($lockState['allowed'] ?? false)) {
            if ($lockState['already_completed'] ?? false) {
                return ['success' => true, 'errors' => []];
            }

            if (array_key_exists('finalized_status', $lockState)) {
                $isSucceeded = (int) $lockState['finalized_status'] === 1;

                return [
                    'success' => $isSucceeded,
                    'errors' => $isSucceeded ? [] : ['Booking already finalized as failed'],
                ];
            }

            if ($lockState['already_processing'] ?? false) {
                return ['success' => false, 'errors' => ['Booking is already being processed']];
            }

            return ['success' => false, 'errors' => [$lockState['error'] ?? 'Booking lock failed']];
        }

        $bookingSuccess = true; // Track overall booking success
        $errorMessages = []; // Collect error messages

        $correlationId = $correlationId ?: ('res-' . $id . '-pay-' . ($reservation->payment_id ?? Str::uuid()->toString()));
        Log::info('completeBooking start', ['correlation_id' => $correlationId, 'reservation_id' => $id]);

        try {
            if ($reservation->type == 1 || $reservation->type == 2) {
                if ($reservation->hotel && ($reservation->hotel->confirmation_number || in_array((int) $reservation->hotel->status, [1, 3], true))) {
                Log::info('Skipping hotel supplier booking; reservation hotel already processed', [
                    'reservation_id' => $reservation->id,
                    'hotel_reservation_id' => $reservation->hotel->id,
                ]);
            } else {
            $passengers = $reservation->passengers->toArray();
            $paxRooms = $reservation->search_request
            ? json_decode($reservation->search_request, true)
            : [['Adults' => 1, 'Children' => 0, 'ChildrenAges' => []]];
            $customer_details = [];
            $adultSkip = 0;
            $childrenSkip = 0;

            foreach ($paxRooms as $index => $room) {
                $adults = array_values(collect($passengers)->where('pax_type', 1)->skip($adultSkip)->take($room['Adults'])->all());
                $children = array_values(collect($passengers)->whereIn('pax_type', [2, 3])->skip($childrenSkip)->take($room['Children'])->all());

                $customer_details[$index]['CustomerNames'] = [];
                foreach ($adults as $adult) {
                    $customer_details[$index]['CustomerNames'][] = [
                        'Title' => $adult['title'] ?? '',
                        'FirstName' => $adult['first_name'],
                        'LastName' => $adult['last_name'],
                        'Type' => 'Adult',
                    ];
                    $adultSkip++;
                }
                foreach ($children as $child) {
                    $customer_details[$index]['CustomerNames'][] = [
                        'Title' => $child['title'] ?? '',
                        'FirstName' => $child['first_name'],
                        'LastName' => $child['last_name'],
                        'Type' => 'Child',
                    ];
                    $childrenSkip++;
                }
            }

            // Use existing booking reference ID or generate a new unique one with max length of 25 characters
            $bookingReferenceId = $reservation->booking_reference_id;
            if (empty($bookingReferenceId)) {
                $bookingReferenceId = 'BK' . time() . rand(100, 999);
                $bookingReferenceId = substr($bookingReferenceId, 0, 25);
                
                // Update the reservation with the new booking reference ID
                $reservation->update(['booking_reference_id' => $bookingReferenceId]);
            }

            request()->merge([
                'booking_code' => $reservation->hotel->booking_code,
                'customer_details' => $customer_details,
                'client_reference_id' => auth('web')->id(),
                'booking_reference_id' => $bookingReferenceId,
                'total_fare' => $reservation->hotel->price,
                'email' => $reservation->passengers()->first()?->email ?? '',
                'phone_number' => $reservation->passengers()->first()?->phone ?? '',
            ]);

            $book = (new TBOHotelBookingService)->bookingRoom(request());

            if ($book['Status']['Code'] == 200) {
                $details = (new TBOHotelBookingService)->bookingDetails($reservation->hotel);
                $reservation->hotel->update([
                    'confirmation_number' => $book['ConfirmationNumber'] ?? null,
                    'client_reference_id' => $bookingReferenceId,
                    'is_refundable' => $details['Rooms'][0]['IsRefundable'] ?? null,
                    'status' => 1,
                ]);
                
                // Dispatch job to fetch supplier data after 120 seconds
                FetchBookingDetailsJob::dispatch($reservation->hotel->id, $bookingReferenceId);
                
                Log::info('FetchBookingDetailsJob dispatched for booking reference: ' . $bookingReferenceId . ', hotel ID: ' . $reservation->hotel->id);
            } else {
                $bookingSuccess = false;
                $errorMessage = $book['Status']['Description'] ?? 'Hotel booking failed';
                $errorMessages[] = $errorMessage;
                Log::error('Hotel booking failed', ['correlation_id' => $correlationId, 'reservation_id' => $reservation->id, 'error' => $errorMessage, 'status_code' => $book['Status']['Code'] ?? null]);
            }

            Log::info('booking_room', ['correlation_id' => $correlationId, 'reservation_id' => $reservation->id, 'status_code' => $book['Status']['Code'] ?? null]);

            if ($reservation->type == 2 && $book['Status']['Code'] != 200) {
                $bookingSuccess = false;
                $errorMessages[] = $book['Status']['Description'] ?? 'Hotel booking failed';
            }
            }
        }

            if ($reservation->type == 1 || $reservation->type == 3) {
            $flights = $reservation->flights()->where('outbound_id',0)->orderBy('id', 'desc')->get();

           

            foreach ($flights as $flight) {
                if ($flight->pnr || in_array((int) $flight->status, [1, 5], true)) {
                    Log::info('Skipping supplier booking for already-processed flight', [
                        'reservation_id' => $reservation->id,
                        'flight_id' => $flight->id,
                        'status' => $flight->status,
                    ]);

                    continue;
                }

                // Parse flight JSON to get flight details
                $flightData = json_decode($flight->flight_json, true);
                
                // Check if flowref_id is still valid by calling FareQuote first
                $supplierReference = $flight->{SupplierFieldMap::FLIGHT_TRACE_ATTR};
                Log::info('Supplier flow start', LogRedactor::redact([
                    'flight_id' => $flight->id,
                    'supplier_ref' => $supplierReference,
                ]));
                
                $fareRuleData = null;
                $fareQuoteData = null;
                $validTraceId = $flight->{SupplierFieldMap::FLIGHT_TRACE_ATTR};
                
               
                // if (isset($sessionFlight['FareQuote']) && isset($sessionFlight['FareRules'])) {
                //     $fareQuoteData = $sessionFlight['FareQuote'];
                //     $fareRuleData = $sessionFlight['FareRules'];
                // } else {
                   
                    // First, try FareQuote with existing flowref_id
               
                // if ($flight && $flight->is_lcc) {
                //          try {
                //         $fareQuoteResponse = (new TBOFlightBookingService)->fareQuote($flight->flowref_id, $flight->result_index);
                //         if ($fareQuoteResponse && $fareQuoteResponse['Response'] &&  isset($fareQuoteResponse['Response']['ResponseStatus']) && $fareQuoteResponse['Response']['ResponseStatus'] == 1) {
                //             $fareQuoteData = $fareQuoteResponse['Response']['Results'];
                //         } else {
                            
                //             // If FareQuote fails, we need a fresh search
                //             // Extract search parameters from flight data to perform new search
                //             if (isset($flightData['Segments'][0][0])) {
                               
                                
                //                 // Try to get a fresh flowref_id using TBOFlightBookingService
                //                 try {
                //                     $tboService = new TBOFlightBookingService();
                //                     $freshTraceId = $tboService->getFreshTraceId();
                                    
                //                     if ($freshTraceId) {
                //                         $validTraceId = $freshTraceId;
                                        
                //                         // Now try FareQuote again with fresh flowref_id
                //                         $fareQuoteResponse = $tboService->fareQuote($freshTraceId, $flight->result_index);
                //                         if ($fareQuoteResponse && $fareQuoteResponse['Response'] &&  isset($fareQuoteResponse['Response']['ResponseStatus']) && $fareQuoteResponse['Response']['ResponseStatus'] == 1) {
                //                             $fareQuoteData = $fareQuoteResponse['Response']['Results'];
                //                         } else {
                //                             $bookingSuccess = false;
                //                             $errorMessages[] = 'Flight booking failed - unable to get valid fare quote even with fresh search for flight ID: ' . $flight->id;
                //                             continue;
                //                         }
                //                     } else {
                //                         $bookingSuccess = false;
                //                         $errorMessages[] = 'Flight booking failed - flowref_id expired and could not refresh for flight ID: ' . $flight->id;
                //                         continue;
                //                     }
                //                 } catch (\Exception $e) {
                //                     $bookingSuccess = false;
                //                     $errorMessages[] = 'Flight booking failed - flowref_id refresh exception for flight ID: ' . $flight->id;
                //                     continue;
                //                 }
                //             } else {
                //                 $bookingSuccess = false;
                //                 $errorMessages[] = 'Flight booking failed - no flight segment data available for flight ID: ' . $flight->id;
                //                 continue;
                //             }
                //         }
                //     } catch (\Exception $e) {
                //         continue; // Skip this flight
                //     }

                //     // If we reach here, we have valid FareQuote data, now get FareRule
                //     try {
                //         $fareRuleResponse = (new TBOFlightBookingService)->fareRules($validTraceId, $flight->result_index);
                //         if ($fareRuleResponse  && $fareRuleResponse['Response']  && isset($fareRuleResponse['Response']['ResponseStatus']) && $fareRuleResponse['Response']['ResponseStatus'] == 1) {
                //             $fareRuleData = $fareRuleResponse['Response']['FareRules'];
                //         } else {
                //         }
                //     } catch (\Exception $e) {
                //     }
                // //}

                // // Update flight with FareRule and FareQuote data
                // $updateData = [];
                // if ($fareRuleData) {
                //     $updateData['fare_rule_json'] = json_encode($fareRuleData);
                // }
                // if ($fareQuoteData) {
                //     $updateData['fare_quote_json'] = json_encode($fareQuoteData);
                // }
                // if (!empty($updateData)) {
                //     $flight->update($updateData);
                // }

                // // Proceed with booking only if we have valid FareQuote data
                // if (!$fareQuoteData) {
                //     $bookingSuccess = false;
                //     $errorMessages[] = 'Flight booking failed - no valid fare data for flight ID: ' . $flight->id;
                //     continue;
                // }
                

                //     $inbound_flight_ticket = (new TBOFlightBookingService)->ticketLCC($validTraceId, $flightData, $fareRuleData, $fareQuoteData);
                    
                //     // Check for success using the correct result structure
                //     if ((isset($inbound_flight_ticket['IsSuccess']) && $inbound_flight_ticket['IsSuccess']) || 
                //         (isset($inbound_flight_ticket['Status']) && $inbound_flight_ticket['Status'] == 5)) {
                //         $pnr = $inbound_flight_ticket['PNR'] ?? $inbound_flight_ticket['Itinerary']['PNR'] ?? null;
                //         $flight->update(['pnr' => $pnr, 'booking_json' => json_encode($inbound_flight_ticket)]);
                //         if($outbound_flight = ReservationFlight::where('outbound_id', $flight->id)->first()){
                //             $outbound_flight->update(['pnr' => $pnr, 'booking_json' => json_encode($inbound_flight_ticket)]);
                //         }
                //     } else {
                //         $bookingSuccess = false;
                //         $errorMessage = $inbound_flight_ticket['Errors'][0]['UserMessage'] ?? 'LCC ticket booking failed';
                //         $errorMessages[] = $errorMessage . ' (Flight ID: ' . $flight->id . ')';
                //     }
                //     $message = $inbound_flight_ticket['Errors'][0]['UserMessage'] ?? 'No error message';
                // }

                 if ($flight && $flight->is_lcc) {
                    Log::info('Processing LCC flight booking for flight ID: ' . $flight->id);
                    $inbound_flight_ticket = (new TBOFlightBookingService)->bookAndTicket($validTraceId, $flightData);
                    $lccStatus = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Status'] ?? null;
                    $lccSuccess = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['IsSuccess'] ?? null;
                    $lccPnr = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['PNR'] ?? null;
                    $lccBookingId = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['BookingId'] ?? null;
                    Log::info('LCC supplier call', LogRedactor::redact(['correlation_id' => $correlationId, 'reservation_id' => $reservation->id, 'flight_id' => $flight->id, 'status' => $lccStatus, 'is_success' => $lccSuccess, 'pnr' => $lccPnr, 'booking_id' => $lccBookingId]));
                    
                    $message = $inbound_flight_ticket['Errors'][0]['UserMessage'] ?? 'No error message';
                    Log::info('flight_lcc', ['message' => $message]);
                    
                    // Check for InProgress status (Status = 5) first
                    if (isset($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Status']) && $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Status'] == 5) {
                        $pnr = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['PNR'] ?? $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Itinerary']['PNR'] ?? null;
                        $bookingId = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['BookingId'] ?? null;
                        
                        Log::info('LCC flight booking is InProgress (Status = 5) for flight ID: ' . $flight->id . ', PNR: ' . $pnr);
                        
                        // Update flight with InProgress status and PNR
                        $flight->update([
                            'pnr' => $pnr,
                            'status' => 5, // InProgress
                            'booking_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA] ?? [])),
                            'ticket_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA] ?? [])),
                            'tbo_booking_id' => $bookingId,
                            'is_in_progress' => true
                        ]);
                        
                        if($outbound_flight = ReservationFlight::where('outbound_id', $flight->id)->first()){
                            $outbound_flight->update([
                                'pnr' => $pnr,
                                'status' => 5, // InProgress
                                'booking_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA] ?? [])),
                                'ticket_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA] ?? [])),
                                'tbo_booking_id' => $bookingId,
                                'is_in_progress' => true
                            ]);
                        }
                        
                        // Dispatch job to fetch supplier data after 15 minutes (airlines usually update within 10-15 minutes)
                        FetchFlightBookingDetailsJob::dispatch($flight->id, $pnr, $bookingId, 15);
                        
                        Log::info('FetchFlightBookingDetailsJob dispatched for LCC flight PNR: ' . $pnr . ', flight ID: ' . $flight->id);
                        
                    } elseif ((isset($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['IsSuccess']) && $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['IsSuccess']) || 
                            (isset($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Status']) && $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Status'] == 1)) {
                        // Successful immediate confirmation
                        $pnr = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['PNR'] ?? $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Itinerary']['PNR'] ?? null;

                        $flight->update([
                            'pnr' => $pnr, 
                            'status' => 1, // Confirmed
                            'ticket_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA] ?? [])),
                            'booking_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA] ?? []))
                        ]);
                        if($outbound_flight = ReservationFlight::where('outbound_id', $flight->id)->first()){
                            $outbound_flight->update([
                                'pnr' => $pnr,
                                'status' => 1, // Confirmed
                                'ticket_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA] ?? [])),
                                'booking_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA] ?? []))
                            ]);
                        }
                        Log::info('LCC booking and ticketing completed successfully for flight ID: ' . $flight->id . ', PNR: ' . $pnr);
                    } else {
                        $bookingSuccess = false;
                        $errorMessage = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Errors'][0]['UserMessage'] ?? 'LCC flight ticketing failed';
                        $errorMessages[] = $errorMessage . ' (Flight ID: ' . $flight->id . ')';
                        $lccFailureStatus = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Status'] ?? null;
                        Log::error('LCC ticketing failed', LogRedactor::redact(['correlation_id' => $correlationId, 'reservation_id' => $reservation->id, 'flight_id' => $flight->id, 'status' => $lccFailureStatus, 'error' => $errorMessage]));
                    }
                }

                if ($flight && !$flight->is_lcc) {
                    Log::info('Processing Non-LCC flight booking for flight ID: ' . $flight->id);
                    $inbound_flight_ticket = (new TBOFlightBookingService)->bookAndTicket($validTraceId, $flightData);
                    $nonLccBookingStatus = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_BOOKING_DATA]['Status'] ?? null;
                    $nonLccTicketStatus = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Status'] ?? null;
                    $nonLccPnr = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['PNR'] ?? $inbound_flight_ticket[SupplierFieldMap::FLIGHT_BOOKING_DATA]['PNR'] ?? null;
                    $nonLccBookingId = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['BookingId'] ?? $inbound_flight_ticket[SupplierFieldMap::FLIGHT_BOOKING_DATA]['BookingId'] ?? null;
                    Log::info('Non-LCC supplier call', LogRedactor::redact(['correlation_id' => $correlationId, 'reservation_id' => $reservation->id, 'flight_id' => $flight->id, 'booking_status' => $nonLccBookingStatus, 'ticket_status' => $nonLccTicketStatus, 'pnr' => $nonLccPnr, 'booking_id' => $nonLccBookingId]));
                    
                    $message = $inbound_flight_ticket['Errors'][0]['UserMessage'] ?? 'No error message';
                    Log::info('flight_non_lcc', ['message' => $message]);
                    
                    // Check for InProgress status in booking result first (Status = 5)
                    if (isset($inbound_flight_ticket[SupplierFieldMap::FLIGHT_BOOKING_DATA]['Status']) && $inbound_flight_ticket[SupplierFieldMap::FLIGHT_BOOKING_DATA]['Status'] == 5) {
                        $pnr = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_BOOKING_DATA]['PNR'] ?? $inbound_flight_ticket[SupplierFieldMap::FLIGHT_BOOKING_DATA]['Itinerary']['PNR'] ?? null;
                        $bookingId = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_BOOKING_DATA]['BookingId'] ?? null;
                        
                        Log::info('Non-LCC flight booking is InProgress (Status = 5) for flight ID: ' . $flight->id . ', PNR: ' . $pnr);
                        
                        // Update flight with InProgress status and PNR
                        $flight->update([
                            'pnr' => $pnr,
                            'status' => 5, // InProgress
                            'booking_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_BOOKING_DATA] ?? [])),
                            'tbo_booking_id' => $bookingId,
                            'is_in_progress' => true
                        ]);
                        
                        if($outbound_flight = ReservationFlight::where('outbound_id', $flight->id)->first()){
                            $outbound_flight->update([
                                'pnr' => $pnr,
                                'status' => 5, // InProgress
                                'booking_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_BOOKING_DATA] ?? [])),
                                'tbo_booking_id' => $bookingId,
                                'is_in_progress' => true
                            ]);
                        }
                        
                        // Dispatch job to fetch supplier data after 15 minutes
                        FetchFlightBookingDetailsJob::dispatch($flight->id, $pnr, $bookingId, 15);
                        
                        Log::info('FetchFlightBookingDetailsJob dispatched for Non-LCC flight PNR: ' . $pnr . ', flight ID: ' . $flight->id);
                        
                    } elseif (isset($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Status']) && $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Status'] == 5) {
                        // Check for InProgress status in ticket result (Status = 5)
                        $pnr = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['PNR'] ?? $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Itinerary']['PNR'] ?? null;
                        $bookingId = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['BookingId'] ?? null;
                        
                        Log::info('Non-LCC flight ticketing is InProgress (Status = 5) for flight ID: ' . $flight->id . ', PNR: ' . $pnr);
                        
                        // Update flight with InProgress status and PNR
                        $flight->update([
                            'pnr' => $pnr,
                            'status' => 5, // InProgress
                            'ticket_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA] ?? [])),
                            'booking_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_BOOKING_DATA] ?? $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA] ?? [])),
                            'tbo_booking_id' => $bookingId,
                            'is_in_progress' => true
                        ]);
                        
                        if($outbound_flight = ReservationFlight::where('outbound_id', $flight->id)->first()){
                            $outbound_flight->update([
                                'pnr' => $pnr,
                                'status' => 5, // InProgress
                                'ticket_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA] ?? [])),
                                'booking_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_BOOKING_DATA] ?? $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA] ?? [])),
                                'tbo_booking_id' => $bookingId,
                                'is_in_progress' => true
                            ]);
                        }
                        
                        // Dispatch job to fetch supplier data after 15 minutes
                        FetchFlightBookingDetailsJob::dispatch($flight->id, $pnr, $bookingId, 15);
                        
                        Log::info('FetchFlightBookingDetailsJob dispatched for Non-LCC flight ticketing PNR: ' . $pnr . ', flight ID: ' . $flight->id);
                        
                    } elseif ((isset($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['IsSuccess']) && $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['IsSuccess']) || 
                            (isset($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Status']) && $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Status'] == 1)) {
                        // Successful immediate confirmation
                        $pnr = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['PNR'] ?? $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Itinerary']['PNR'] ?? null;

                        $flight->update([
                            'pnr' => $pnr, 
                            'status' => 1, // Confirmed
                            'ticket_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA] ?? [])),
                            'booking_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_BOOKING_DATA] ?? []))
                        ]);
                        if($outbound_flight = ReservationFlight::where('outbound_id', $flight->id)->first()){
                            $outbound_flight->update([
                                'pnr' => $pnr,
                                'status' => 1, // Confirmed
                                'ticket_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA] ?? [])),
                                'booking_json' => json_encode(self::minimizeFlightProviderPayload($inbound_flight_ticket[SupplierFieldMap::FLIGHT_BOOKING_DATA] ?? []))
                            ]);
                        }
                        Log::info('Non-LCC booking and ticketing completed successfully for flight ID: ' . $flight->id . ', PNR: ' . $pnr);
                    } else {
                        $bookingSuccess = false;
                        $errorMessage = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Errors'][0]['UserMessage'] ?? 'Non-LCC flight ticketing failed';
                        $errorMessages[] = $errorMessage . ' (Flight ID: ' . $flight->id . ')';
                        $nonLccFailureStatus = $inbound_flight_ticket[SupplierFieldMap::FLIGHT_TICKET_DATA]['Status'] ?? null;
                        Log::error('Non-LCC ticketing failed', LogRedactor::redact(['correlation_id' => $correlationId, 'reservation_id' => $reservation->id, 'flight_id' => $flight->id, 'status' => $nonLccFailureStatus, 'error' => $errorMessage]));
                    }
                }
            }
            }

            // Log the overall booking result
            if (! $bookingSuccess) {
                Log::error('Supplier flow failed', LogRedactor::redact([
                    'reservation_id' => $reservation->id,
                    'errors_count' => count($errorMessages),
                    'first_error' => isset($errorMessages[0]) ? Str::limit((string) $errorMessages[0], 160) : null,
                ]));
            } else {
                Log::info('Booking/Ticketing completed successfully for reservation ID: ' . $reservation->id);
            }

            Session::put(['flight' => null, 'hotel' => null, 'passengers' => null, 'preferences' => null, SupplierFieldMap::FLIGHT_TRACE_SESSION_KEY => null, 'outbound_flight' => null, 'inbound_flight' => null]);

            return [
                'success' => $bookingSuccess,
                'errors' => $errorMessages,
            ];
        } catch (\Throwable $exception) {
            $bookingSuccess = false;

            Log::error('completeBooking exception', [
                'correlation_id' => $correlationId,
                'reservation_id' => $id,
                'error' => $exception->getMessage(),
            ]);

            return [
                'success' => false,
                'errors' => ['Booking processing exception'],
            ];
        } finally {
            Reservation::where('id', $id)->update([
                'booking_in_progress' => false,
                'booking_completed_at' => $bookingSuccess ? now() : null,
            ]);

            Log::info('completeBooking end', ['correlation_id' => $correlationId, 'reservation_id' => $id, 'success' => $bookingSuccess, 'errors_count' => count($errorMessages)]);
        }
    }


    private static function minimizeFlightProviderPayload(array $payload): array
    {
        return LogRedactor::redact([
            'status' => $payload['Status'] ?? null,
            'is_success' => $payload['IsSuccess'] ?? null,
            'pnr' => $payload['PNR'] ?? ($payload['Itinerary']['PNR'] ?? null),
            'booking_id' => $payload['BookingId'] ?? null,
            'error_code' => $payload['Error']['ErrorCode'] ?? ($payload['Errors'][0]['ErrorCode'] ?? null),
            'user_message' => isset($payload['Error']['ErrorMessage'])
                ? Str::limit((string) $payload['Error']['ErrorMessage'], 160)
                : (isset($payload['Errors'][0]['UserMessage']) ? Str::limit((string) $payload['Errors'][0]['UserMessage'], 160) : null),
        ]);
    }

    private static function hasPendingSupplierProcessing(Reservation $reservation): bool
    {
        $reservation->loadMissing(['hotel', 'flights']);

        $hotelPending = false;
        if (in_array((int) $reservation->type, [1, 2], true)) {
            $hotel = $reservation->hotel;
            $hotelPending = $hotel && ! ($hotel->confirmation_number || in_array((int) $hotel->status, [1, 3], true));
        }

        $flightPending = false;
        if (in_array((int) $reservation->type, [1, 3], true)) {
            $flightPending = $reservation->flights
                ->where('outbound_id', 0)
                ->contains(function ($flight) {
                    return ! ($flight->pnr || in_array((int) $flight->status, [1, 3, 5], true));
                });
        }

        return $hotelPending || $flightPending;
    }
}
