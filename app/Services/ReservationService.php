<?php

namespace App\Services;

use App\Helpers\PriceCalculationHelper;
use App\Jobs\FetchBookingDetailsJob;
use App\Jobs\FetchFlightBookingDetailsJob;
use App\Models\Airport;
use App\Models\Panel\Setting;
use App\Models\Reservation;
use App\Models\ReservationFlight;
use App\Traits\PriceCalculationTrait;
use Illuminate\Support\Facades\Log;

class ReservationService
{
    use PriceCalculationTrait;
    public static function add()
    {

        $hotel_commession = Setting::where('code', 'hotel_profit')->first() ? Setting::where('code', 'hotel_profit')->first()->value : 12;
        $flight_commession = Setting::where('code', 'flight_profit')->first() ? Setting::where('code', 'flight_profit')->first()->value : 7;
        $airPortFrom = Airport::find(session('searchParams.origin'));
        $airPortTo = Airport::find(session('searchParams.destination'));
        $airPortToo = Airport::where('city_code', session('preferences.destination'))->first();

        $preferences = session('preferences');
        $flight = session('flight');
        $hotel = session('hotel');
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
        $inboundAmount = session()->has('flight.inbound_flightSegment') ? $flight['inbound_flight_amount'] ?? 0 : 0;
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

//        if (session('preferences')['trip_type'] == 1) {
//            $price = session('hotel.TotalFareHotel') + (session('flight.flight_amount') + session('flight.inbound_flight_amount'));
//        }
//
//        if (session('preferences')['trip_type'] == 2) {
//            $price = session('hotel.TotalFareHotel');
//        }
//
//        if (session('preferences')['trip_type'] == 3) {
//            $price = session('flight.flight_amount') + session('flight.inbound_flight_amount');
//        }


        do {
            $integerId = rand(1, 9999999999);
        } while (Reservation::where('uuid', $integerId)->exists());
        
        // Generate initial booking reference ID with max length of 25 characters
        $bookingReferenceId = 'BK' . time() . rand(100, 999);
        $bookingReferenceId = substr($bookingReferenceId, 0, 25);
        
        $reservationData = [
            'type' => session('preferences.trip_type'),
            'trip_id' => session('preferences.trip_id'),
            'price' => number_format($price, 2, '.', '') ,
            'currency' => session('hotel.CurrencyHotel') ?? 'SAR',
            'unit_price' => $price,
            'uuid' => $integerId,
            'search_request'=> json_encode(session('paxRooms')),
            'booking_reference_id' => $bookingReferenceId,
        ];
        if ($airPortTo && $airPortTo->city) {
            $reservationData['to_id'] = session('searchParams.destination');
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
            $reservationData['from_id'] = session('searchParams.origin');
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
            $selected_hotel = json_decode(session('hotel.selected_hotel'));

            $first_tax_rate_hotel = $reservationType == 'inside' ? 15 : 0;
            $first_tax_amount_hotel = session('hotel.TotalFareHotel') * $first_tax_rate_hotel / 100;

            $administrative_tax_rate_hotel = (int)$hotel_commession;
            $administrative_tax_amount_hotel = session('hotel.TotalFareHotel') * $administrative_tax_rate_hotel / 100;

            $second_tax_rate_hotel = 15;
            $second_tax_amount_hotel = $administrative_tax_amount_hotel * $second_tax_rate_hotel / 100;

            $price_without_tax_hotel = session('hotel.TotalFareHotel');
            $tax_amount_hotel = $first_tax_amount_hotel + $administrative_tax_amount_hotel + $second_tax_amount_hotel;

            $reservation->hotel()->create([
                'reservation_id' => $reservation->id,
                'hotel_id' => $selected_hotel->code,
                'check_in' => session('preferences')['start_date'],
                'check_out' => session('preferences')['end_date'],
                'rooms' => session('preferences')['rooms'],
                'adults' => session('preferences')['adults'],
                'children' => session('preferences')['children'],
                'price' => session('hotel.TotalFareHotel'),
                'currency' => session('hotel.CurrencyHotel'),
                'hotel_name' => $selected_hotel->name_en ?: $selected_hotel->name_ar,
                'hotel_image' => isset($selected_hotel->images[0]) ? $selected_hotel->images[0] : null,
                'hotel_address' => $selected_hotel->address,
                'phone' => $selected_hotel->phone??'-',
                'hotel_rate' => $selected_hotel->rating,
                'booking_code' => session('hotel.BookingCodeHotel'),
                'confirmation_number' => null,
                'country_id' => @$selected_hotel->country_id,
                'city_id' => @$selected_hotel->city_id,
                'date_from' => session('preferences')['start_date'],
                'date_to' => session('preferences')['end_date'],


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
                'room_details' => session('hotel.selected_room') ? 
                    (is_string(session('hotel.selected_room')) ? 
                        session('hotel.selected_room') : 
                        json_encode(session('hotel.selected_room'))
                    ) : null,
            ]);
        }

//        Log::info('outbound_flight '.json_encode(session('outbound_flight')));
//        Log::info('inbound_flight '.json_encode(session('inbound_flight')));

        if ($tripType == 1 || $tripType == 3) {
            if (session('outbound_flight')) {
                $first_tax_rate = $reservationType == 'inside' ? 15 : 0;
                $first_tax_amount = $flight['flight_amount'] * $first_tax_rate / 100;

                $administrative_tax_rate = (int)$flight_commession;
                $administrative_tax_amount = $flight['flight_amount'] * $administrative_tax_rate / 100;

                $second_tax_rate = 15;
                $second_tax_amount = $administrative_tax_amount * $second_tax_rate / 100;

                $price_without_tax = $flight['flight_amount'];
                $tax_amount = $first_tax_amount + $administrative_tax_amount + $second_tax_amount;
                Log::alert(json_encode(session('outbound_flight')));
                $outbound_flight = $reservation->flight()->create([
                    'reservation_id' => $reservation->id,
                    'result_index' => session('outbound_flight')['ResultIndex'],
                    'trace_id' => session('traceId'),
                    'is_lcc' => session('outbound_flight')['IsLCC'],
                    'is_refundable' => session('outbound_flight')['IsRefundable'],
                    'last_ticket_date' => session('outbound_flight')['LastTicketDate'],
                    'total_price' => $flight['flight_amount'],
                    'is_direct' => count(session('outbound_flight')['Segments'][0]) == 1 ? true : false,
                    'flight_class' => session('outbound_flight')['Segments'][0][0]['CabinClass'],
                    'pnr' => null,
                    'flight_json' => json_encode(session('outbound_flight')),
                    'flight_from' => session('outbound_flight')['Segments'][0][0]['Origin']['Airport']['CityName'],
                    'flight_to' => session('outbound_flight')['Segments'][0][count(session('outbound_flight')['Segments'][0]) - 1]['Destination']['Airport']['CityName'],
                    'departure_time' => session('outbound_flight')['Segments'][0][0]['Origin']['DepTime'],
                    'arrival_time' => session('outbound_flight')['Segments'][0][count(session('outbound_flight')['Segments'][0]) - 1]['Destination']['ArrTime'],

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

                foreach (session('outbound_flight')['Segments'][0] as $key => $value) {
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
            if (session('inbound_flight')||count(session('outbound_flight')['Segments']) > 1) {
                $segment_index=session('inbound_flight')?0:1;
                $outbound_id=!session('inbound_flight')?$outbound_flight->id:0;
                $inboundFlight=session('inbound_flight')?:session('outbound_flight');
                $first_tax_rate = $reservationType == 'inside' ? 15 : 0;
                $first_tax_amount = session('flight')['flight_amount'] * $first_tax_rate / 100;

                $administrative_tax_rate = (int)$flight_commession;
                $administrative_tax_amount = session('flight')['flight_amount'] * $administrative_tax_rate / 100;

                $second_tax_rate = 15;
                $second_tax_amount = $administrative_tax_amount * $second_tax_rate / 100;

                $price_without_tax = session('flight')['flight_amount'];
                $tax_amount = $first_tax_amount + $administrative_tax_amount + $second_tax_amount;

                $inbound_flight = $reservation->flight()->create([
                    'reservation_id' => $reservation->id,
                    'result_index' => $inboundFlight['ResultIndex'],
                    'trace_id' => session('traceId'),
                    'is_lcc' => $inboundFlight['IsLCC'],
                    'is_refundable' => $inboundFlight['IsRefundable'],
                    'last_ticket_date' => $inboundFlight['LastTicketDate'],
                    'total_price' => session('flight')['flight_amount'],
                    'is_direct' => count(value: $inboundFlight['Segments'][$segment_index]) == 1 ? true : false,
                    'flight_class' => $inboundFlight['Segments'][$segment_index][0]['CabinClass'],
                    'pnr' => null,
                    'flight_json' => json_encode($inboundFlight),
                    'flight_from' => $inboundFlight['Segments'][$segment_index][0]['Origin']['Airport']['CityName'],
                    'flight_to' => $inboundFlight['Segments'][$segment_index][count($inboundFlight['Segments'][$segment_index]) - 1]['Destination']['Airport']['CityName'],
//                    'flight_to' => $inboundFlight['Segments'][$segment_index][0]['Destination']['Airport']['CityName'],
                    'departure_time' => $inboundFlight['Segments'][$segment_index][0]['Origin']['DepTime'],
                    // 'arrival_time' => session('inbound_flight')['Segments'][0][count(session('outbound_flight')['Segments']) - 1]['Destination']['ArrTime'],
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
                        'no_of_seat_available' => @$value['NoOfSeatAvailable']?:1,
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

        foreach (session('passengers') as $passenger) {
            $reservation->passengers()->create([
                'first_name' => $passenger['first_name'],
                'last_name' => $passenger['last_name'],
                'pax_type' => $passenger['pax_type'],
                'birth_date' => $passenger['birth_date'],
                'email' => $passenger['email'],
                'phone' => $passenger['phone'],
                'nationality' => $passenger['nationality'],
                'gender' => $passenger['gender'],
                'address' => $passenger['address'],
                'passport_number' => isset($passenger['passport_number']) ? $passenger['passport_number'] : '',
                'passport_expiry_date' => isset($passenger['passport_expiry_date']) ? $passenger['passport_expiry_date'] : '1900-01-01',
                'title' => @$passenger['title'],
            ]);
        }

        return $reservation;
    }

    public static function completeBooking($id)
    {
        $reservation = Reservation::where('id', $id)->first();
        $bookingSuccess = true; // Track overall booking success
        $errorMessages = []; // Collect error messages

        if ($reservation->type == 1 || $reservation->type == 2) {
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
                'email' => $reservation->passengers()->first()->email,
                'phone_number' => $reservation->passengers()->first()->phone,
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
                
                // Dispatch job to fetch booking details after 120 seconds
                FetchBookingDetailsJob::dispatch($reservation->hotel->id, $bookingReferenceId);
                
                Log::info('FetchBookingDetailsJob dispatched for booking reference: ' . $bookingReferenceId . ', hotel ID: ' . $reservation->hotel->id);
            } else {
                $bookingSuccess = false;
                $errorMessage = $book['Status']['Description'] ?? 'Hotel booking failed';
                $errorMessages[] = $errorMessage;
                Log::error('Hotel booking failed for reservation ID: ' . $reservation->id, ['error' => $errorMessage, 'response' => $book]);
            }

            Log::info('booking_room', $book);

            if ($reservation->type == 2 && $book['Status']['Code'] != 200) {
                $bookingSuccess = false;
                $errorMessages[] = $book['Status']['Description'] ?? 'Hotel booking failed';
            }
        }

        if ($reservation->type == 1 || $reservation->type == 3) {
            $flights = $reservation->flights()->where('outbound_id',0)->orderBy('id', 'desc')->get();

           

            foreach ($flights as $flight) {
                // Parse flight JSON to get flight details
                $flightData = json_decode($flight->flight_json, true);
                
                // Check if trace_id is still valid by calling FareQuote first
                Log::info('Starting booking process for flight ID: ' . $flight->id . ' with trace_id: ' . $flight->trace_id);
                
                $fareRuleData = null;
                $fareQuoteData = null;
                $validTraceId = $flight->trace_id;
                
               
                // if (isset($sessionFlight['FareQuote']) && isset($sessionFlight['FareRules'])) {
                //     Log::info('Using FareQuote and FareRules data from passenger page session for flight ID: ' . $flight->id);
                //     $fareQuoteData = $sessionFlight['FareQuote'];
                //     $fareRuleData = $sessionFlight['FareRules'];
                // } else {
                   
                    // First, try FareQuote with existing trace_id
               
                // if ($flight && $flight->is_lcc) {
                //          try {
                //         Log::info('Testing existing trace_id validity with FareQuote for flight ID: ' . $flight->id);
                //         $fareQuoteResponse = (new TBOFlightBookingService)->fareQuote($flight->trace_id, $flight->result_index);
                //         Log::info('fareQuoteResponse: '. json_encode($fareQuoteResponse));
                //         if ($fareQuoteResponse && $fareQuoteResponse['Response'] &&  isset($fareQuoteResponse['Response']['ResponseStatus']) && $fareQuoteResponse['Response']['ResponseStatus'] == 1) {
                //             $fareQuoteData = $fareQuoteResponse['Response']['Results'];
                //             //Log::info('Existing trace_id is valid - FareQuote successful for flight ID: ' . $flight->id);
                //         } else {
                //             Log::warning('Existing trace_id expired or invalid for flight ID: ' . $flight->id . '. Need to perform fresh search.');
                            
                //             // If FareQuote fails, we need a fresh search
                //             // Extract search parameters from flight data to perform new search
                //             if (isset($flightData['Segments'][0][0])) {
                               
                                
                //                 // Try to get a fresh trace_id using TBOFlightBookingService
                //                 try {
                //                     $tboService = new TBOFlightBookingService();
                //                     $freshTraceId = $tboService->getFreshTraceId();
                                    
                //                     if ($freshTraceId) {
                //                         Log::info('Got fresh trace_id: ' . $freshTraceId . ' for flight ID: ' . $flight->id);
                //                         $validTraceId = $freshTraceId;
                                        
                //                         // Now try FareQuote again with fresh trace_id
                //                         $fareQuoteResponse = $tboService->fareQuote($freshTraceId, $flight->result_index);
                //                         if ($fareQuoteResponse && $fareQuoteResponse['Response'] &&  isset($fareQuoteResponse['Response']['ResponseStatus']) && $fareQuoteResponse['Response']['ResponseStatus'] == 1) {
                //                             $fareQuoteData = $fareQuoteResponse['Response']['Results'];
                //                             Log::info('FareQuote successful with fresh trace_id for flight ID: ' . $flight->id);
                //                         } else {
                //                             Log::error('FareQuote still failed even with fresh trace_id for flight ID: ' . $flight->id);
                //                             $bookingSuccess = false;
                //                             $errorMessages[] = 'Flight booking failed - unable to get valid fare quote even with fresh search for flight ID: ' . $flight->id;
                //                             continue;
                //                         }
                //                     } else {
                //                         Log::error('Could not get fresh trace_id for flight ID: ' . $flight->id);
                //                         $bookingSuccess = false;
                //                         $errorMessages[] = 'Flight booking failed - trace_id expired and could not refresh for flight ID: ' . $flight->id;
                //                         continue;
                //                     }
                //                 } catch (\Exception $e) {
                //                     Log::error('Exception while trying to refresh trace_id for flight ID: ' . $flight->id . ', Error: ' . $e->getMessage());
                //                     $bookingSuccess = false;
                //                     $errorMessages[] = 'Flight booking failed - trace_id refresh exception for flight ID: ' . $flight->id;
                //                     continue;
                //                 }
                //             } else {
                //                 Log::error('Cannot proceed with booking - no flight segment data available for flight ID: ' . $flight->id);
                //                 $bookingSuccess = false;
                //                 $errorMessages[] = 'Flight booking failed - no flight segment data available for flight ID: ' . $flight->id;
                //                 continue;
                //             }
                //         }
                //     } catch (\Exception $e) {
                //         Log::error('FareQuote API exception for flight ID: ' . $flight->id . ', Error: ' . $e->getMessage());
                //         continue; // Skip this flight
                //     }

                //     // If we reach here, we have valid FareQuote data, now get FareRule
                //     try {
                //         Log::info('Fetching FareRule for flight ID: ' . $flight->id . ', ResultIndex: ' . $flight->result_index);
                //         $fareRuleResponse = (new TBOFlightBookingService)->fareRules($validTraceId, $flight->result_index);
                //         Log::info('fareRuleResponse: '. json_encode($fareRuleResponse));
                //         if ($fareRuleResponse  && $fareRuleResponse['Response']  && isset($fareRuleResponse['Response']['ResponseStatus']) && $fareRuleResponse['Response']['ResponseStatus'] == 1) {
                //             $fareRuleData = $fareRuleResponse['Response']['FareRules'];
                //             Log::info('FareRule API successful for flight ID: ' . $flight->id);
                //         } else {
                //             Log::warning('FareRule API failed for flight ID: ' . $flight->id, $fareRuleResponse ?: []);
                //         }
                //     } catch (\Exception $e) {
                //         Log::error('FareRule API exception for flight ID: ' . $flight->id . ', Error: ' . $e->getMessage());
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
                //     Log::info('Updated flight ID: ' . $flight->id . ' with FareRule/FareQuote data');
                // }

                // // Proceed with booking only if we have valid FareQuote data
                // if (!$fareQuoteData) {
                //     Log::error('Cannot proceed with booking - no valid FareQuote data for flight ID: ' . $flight->id);
                //     $bookingSuccess = false;
                //     $errorMessages[] = 'Flight booking failed - no valid fare data for flight ID: ' . $flight->id;
                //     continue;
                // }
                // Log::info('Flight Data: ' . json_encode($flight));
                

                //     Log::info('Processing LCC flight booking for flight ID: ' . $flight->id);
                //     $inbound_flight_ticket = (new TBOFlightBookingService)->ticketLCC($validTraceId, $flightData, $fareRuleData, $fareQuoteData);
                //     Log::info('LCC Ticket API Response for flight ID: ' . $flight->id, $inbound_flight_ticket ?: []);
                    
                //     // Check for success using the correct response structure
                //     if ((isset($inbound_flight_ticket['IsSuccess']) && $inbound_flight_ticket['IsSuccess']) || 
                //         (isset($inbound_flight_ticket['Status']) && $inbound_flight_ticket['Status'] == 5)) {
                //         $pnr = $inbound_flight_ticket['PNR'] ?? $inbound_flight_ticket['Itinerary']['PNR'] ?? null;
                //         $flight->update(['pnr' => $pnr, 'booking_json' => json_encode($inbound_flight_ticket)]);
                //         if($outbound_flight = ReservationFlight::where('outbound_id', $flight->id)->first()){
                //             $outbound_flight->update(['pnr' => $pnr, 'booking_json' => json_encode($inbound_flight_ticket)]);
                //         }
                //         Log::info('LCC ticket booking successful for flight ID: ' . $flight->id . ', PNR: ' . $pnr);
                //     } else {
                //         $bookingSuccess = false;
                //         $errorMessage = $inbound_flight_ticket['Errors'][0]['UserMessage'] ?? 'LCC ticket booking failed';
                //         $errorMessages[] = $errorMessage . ' (Flight ID: ' . $flight->id . ')';
                //         Log::error('LCC ticket booking failed for flight ID: ' . $flight->id, $inbound_flight_ticket ?: []);
                //     }
                //     $message = $inbound_flight_ticket['Errors'][0]['UserMessage'] ?? 'No error message';
                //     Log::info('flight_lcc', ['message' => $message]);
                // }

                 if ($flight && $flight->is_lcc) {
                    Log::info('Processing LCC flight booking for flight ID: ' . $flight->id);
                    $inbound_flight_ticket = (new TBOFlightBookingService)->bookAndTicket($validTraceId, $flightData);
                    Log::info('LCC Booking API Response for flight ID: ' . $flight->id, $inbound_flight_ticket ?: []);
                    
                    $message = $inbound_flight_ticket['Errors'][0]['UserMessage'] ?? 'No error message';
                    Log::info('flight_lcc', ['message' => $message]);
                    
                    // Check for InProgress status (Status = 5) first
                    if (isset($inbound_flight_ticket['responseTicketData']['Status']) && $inbound_flight_ticket['responseTicketData']['Status'] == 5) {
                        $pnr = $inbound_flight_ticket['responseTicketData']['PNR'] ?? $inbound_flight_ticket['responseTicketData']['Itinerary']['PNR'] ?? null;
                        $bookingId = $inbound_flight_ticket['responseTicketData']['BookingId'] ?? null;
                        
                        Log::info('LCC flight booking is InProgress (Status = 5) for flight ID: ' . $flight->id . ', PNR: ' . $pnr);
                        
                        // Update flight with InProgress status and PNR
                        $flight->update([
                            'pnr' => $pnr,
                            'status' => 5, // InProgress
                            'booking_json' => json_encode($inbound_flight_ticket['responseTicketData']),
                            'ticket_json' => json_encode($inbound_flight_ticket['responseTicketData']),
                            'tbo_booking_id' => $bookingId,
                            'is_in_progress' => true
                        ]);
                        
                        if($outbound_flight = ReservationFlight::where('outbound_id', $flight->id)->first()){
                            $outbound_flight->update([
                                'pnr' => $pnr,
                                'status' => 5, // InProgress
                                'booking_json' => json_encode($inbound_flight_ticket['responseTicketData']),
                                'ticket_json' => json_encode($inbound_flight_ticket['responseTicketData']),
                                'tbo_booking_id' => $bookingId,
                                'is_in_progress' => true
                            ]);
                        }
                        
                        // Dispatch job to fetch booking details after 15 minutes (airlines usually update within 10-15 minutes)
                        FetchFlightBookingDetailsJob::dispatch($flight->id, $pnr, $bookingId, 15);
                        
                        Log::info('FetchFlightBookingDetailsJob dispatched for LCC flight PNR: ' . $pnr . ', flight ID: ' . $flight->id);
                        
                    } elseif ((isset($inbound_flight_ticket['responseTicketData']['IsSuccess']) && $inbound_flight_ticket['responseTicketData']['IsSuccess']) || 
                            (isset($inbound_flight_ticket['responseTicketData']['Status']) && $inbound_flight_ticket['responseTicketData']['Status'] == 1)) {
                        // Successful immediate confirmation
                        $pnr = $inbound_flight_ticket['responseTicketData']['PNR'] ?? $inbound_flight_ticket['responseTicketData']['Itinerary']['PNR'] ?? null;

                        $flight->update([
                            'pnr' => $pnr, 
                            'status' => 1, // Confirmed
                            'ticket_json' => json_encode($inbound_flight_ticket['responseTicketData']),
                            'booking_json' => json_encode($inbound_flight_ticket['responseTicketData'])
                        ]);
                        if($outbound_flight = ReservationFlight::where('outbound_id', $flight->id)->first()){
                            $outbound_flight->update([
                                'pnr' => $pnr,
                                'status' => 1, // Confirmed
                                'ticket_json' => json_encode($inbound_flight_ticket['responseTicketData']),
                                'booking_json' => json_encode($inbound_flight_ticket['responseTicketData'])
                            ]);
                        }
                        Log::info('LCC booking and ticketing completed successfully for flight ID: ' . $flight->id . ', PNR: ' . $pnr);
                    } else {
                        $bookingSuccess = false;
                        $errorMessage = $inbound_flight_ticket['responseTicketData']['Errors'][0]['UserMessage'] ?? 'LCC flight ticketing failed';
                        $errorMessages[] = $errorMessage . ' (Flight ID: ' . $flight->id . ')';
                        Log::error('LCC ticketing failed for flight ID: ' . $flight->id, $inbound_flight_ticket ?: []);
                    }
                }

                if ($flight && !$flight->is_lcc) {
                    Log::info('Processing Non-LCC flight booking for flight ID: ' . $flight->id);
                    $inbound_flight_ticket = (new TBOFlightBookingService)->bookAndTicket($validTraceId, $flightData);
                    Log::info('Non-LCC Booking API Response for flight ID: ' . $flight->id, $inbound_flight_ticket ?: []);
                    
                    $message = $inbound_flight_ticket['Errors'][0]['UserMessage'] ?? 'No error message';
                    Log::info('flight_non_lcc', ['message' => $message]);
                    
                    // Check for InProgress status in booking response first (Status = 5)
                    if (isset($inbound_flight_ticket['responseBookingData']['Status']) && $inbound_flight_ticket['responseBookingData']['Status'] == 5) {
                        $pnr = $inbound_flight_ticket['responseBookingData']['PNR'] ?? $inbound_flight_ticket['responseBookingData']['Itinerary']['PNR'] ?? null;
                        $bookingId = $inbound_flight_ticket['responseBookingData']['BookingId'] ?? null;
                        
                        Log::info('Non-LCC flight booking is InProgress (Status = 5) for flight ID: ' . $flight->id . ', PNR: ' . $pnr);
                        
                        // Update flight with InProgress status and PNR
                        $flight->update([
                            'pnr' => $pnr,
                            'status' => 5, // InProgress
                            'booking_json' => json_encode($inbound_flight_ticket['responseBookingData']),
                            'tbo_booking_id' => $bookingId,
                            'is_in_progress' => true
                        ]);
                        
                        if($outbound_flight = ReservationFlight::where('outbound_id', $flight->id)->first()){
                            $outbound_flight->update([
                                'pnr' => $pnr,
                                'status' => 5, // InProgress
                                'booking_json' => json_encode($inbound_flight_ticket['responseBookingData']),
                                'tbo_booking_id' => $bookingId,
                                'is_in_progress' => true
                            ]);
                        }
                        
                        // Dispatch job to fetch booking details after 15 minutes
                        FetchFlightBookingDetailsJob::dispatch($flight->id, $pnr, $bookingId, 15);
                        
                        Log::info('FetchFlightBookingDetailsJob dispatched for Non-LCC flight PNR: ' . $pnr . ', flight ID: ' . $flight->id);
                        
                    } elseif (isset($inbound_flight_ticket['responseTicketData']['Status']) && $inbound_flight_ticket['responseTicketData']['Status'] == 5) {
                        // Check for InProgress status in ticket response (Status = 5)
                        $pnr = $inbound_flight_ticket['responseTicketData']['PNR'] ?? $inbound_flight_ticket['responseTicketData']['Itinerary']['PNR'] ?? null;
                        $bookingId = $inbound_flight_ticket['responseTicketData']['BookingId'] ?? null;
                        
                        Log::info('Non-LCC flight ticketing is InProgress (Status = 5) for flight ID: ' . $flight->id . ', PNR: ' . $pnr);
                        
                        // Update flight with InProgress status and PNR
                        $flight->update([
                            'pnr' => $pnr,
                            'status' => 5, // InProgress
                            'ticket_json' => json_encode($inbound_flight_ticket['responseTicketData']),
                            'booking_json' => json_encode($inbound_flight_ticket['responseBookingData'] ?? $inbound_flight_ticket['responseTicketData']),
                            'tbo_booking_id' => $bookingId,
                            'is_in_progress' => true
                        ]);
                        
                        if($outbound_flight = ReservationFlight::where('outbound_id', $flight->id)->first()){
                            $outbound_flight->update([
                                'pnr' => $pnr,
                                'status' => 5, // InProgress
                                'ticket_json' => json_encode($inbound_flight_ticket['responseTicketData']),
                                'booking_json' => json_encode($inbound_flight_ticket['responseBookingData'] ?? $inbound_flight_ticket['responseTicketData']),
                                'tbo_booking_id' => $bookingId,
                                'is_in_progress' => true
                            ]);
                        }
                        
                        // Dispatch job to fetch booking details after 15 minutes
                        FetchFlightBookingDetailsJob::dispatch($flight->id, $pnr, $bookingId, 15);
                        
                        Log::info('FetchFlightBookingDetailsJob dispatched for Non-LCC flight ticketing PNR: ' . $pnr . ', flight ID: ' . $flight->id);
                        
                    } elseif ((isset($inbound_flight_ticket['responseTicketData']['IsSuccess']) && $inbound_flight_ticket['responseTicketData']['IsSuccess']) || 
                            (isset($inbound_flight_ticket['responseTicketData']['Status']) && $inbound_flight_ticket['responseTicketData']['Status'] == 1)) {
                        // Successful immediate confirmation
                        $pnr = $inbound_flight_ticket['responseTicketData']['PNR'] ?? $inbound_flight_ticket['responseTicketData']['Itinerary']['PNR'] ?? null;

                        $flight->update([
                            'pnr' => $pnr, 
                            'status' => 1, // Confirmed
                            'ticket_json' => json_encode($inbound_flight_ticket['responseTicketData']),
                            'booking_json' => json_encode($inbound_flight_ticket['responseBookingData'])
                        ]);
                        if($outbound_flight = ReservationFlight::where('outbound_id', $flight->id)->first()){
                            $outbound_flight->update([
                                'pnr' => $pnr,
                                'status' => 1, // Confirmed
                                'ticket_json' => json_encode($inbound_flight_ticket['responseTicketData']),
                                'booking_json' => json_encode($inbound_flight_ticket['responseBookingData'])
                            ]);
                        }
                        Log::info('Non-LCC booking and ticketing completed successfully for flight ID: ' . $flight->id . ', PNR: ' . $pnr);
                    } else {
                        $bookingSuccess = false;
                        $errorMessage = $inbound_flight_ticket['responseTicketData']['Errors'][0]['UserMessage'] ?? 'Non-LCC flight ticketing failed';
                        $errorMessages[] = $errorMessage . ' (Flight ID: ' . $flight->id . ')';
                        Log::error('Non-LCC ticketing failed for flight ID: ' . $flight->id, $inbound_flight_ticket ?: []);
                    }
                }
            }
        }

        // Log the overall booking result
        if (!$bookingSuccess) {
            Log::error('Booking/Ticketing failed for reservation ID: ' . $reservation->id, [
                'errors' => $errorMessages,
                'reservation_id' => $reservation->id
            ]);
        } else {
            Log::info('Booking/Ticketing completed successfully for reservation ID: ' . $reservation->id);
        }

        session(['flight' => null, 'hotel' => null, 'passengers' => null, 'preferences' => null, 'traceId' => null, 'outbound_flight' => null, 'inbound_flight' => null]);
        
        return [
            'success' => $bookingSuccess,
            'errors' => $errorMessages
        ];
    }
}



