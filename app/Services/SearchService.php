<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Airport;
use App\Models\TBOHotel;
use App\Models\Destination;
use App\Support\LogRedactor;
use Illuminate\Support\Str;
use App\Models\Panel\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Jobs\CacheTBOHotelInDatabase;
use Illuminate\Support\Facades\Cache;

class SearchService
{
    public static function search($request, $tripType = 1)
    {
        $data = self::prepareSearchData($request, $tripType);
        session(['passengers' => null]);
        session(['preferences' => $data]);
        session(['searchParams' => $request->all()]);
        session(['searchResults' => $data]);
        
        // Store guest nationality separately for easy access in forms
        if (!empty($request->guest_nationality)) {
            session(['guest_nationality' => $request->guest_nationality]);
        }
    }

     public static function searchNew($request, $tripType = 1)
    {
        $data = self::prepareSearchDataNew($request, $tripType);
        session(['passengers' => null]);
        session(['preferences' => $data]);
        session(['searchParams' => $request->all()]);
        session(['searchResults' => $data]);
        
        // Store guest nationality separately for easy access in forms
        if (!empty($request->guest_nationality)) {
            session(['guest_nationality' => $request->guest_nationality]);
        }
    }

    private static function prepareSearchDataNew($request, $tripType)
    {
       // Log::info('Preparing search data', ['request' => $request->all()]);

        Log::info("Search Request Trip", LogRedactor::redact(['request' => $request->all()]));
        $paxRooms = [];
        $roomCount = (int)($request['rooms'] ?? 1); // Get total rooms (e.g., 2)

        // Check if we have hotel room-specific data or general flight data
        $hasHotelRoomData = isset($request["hotel_room_1_adults"]);
        $hasRoomData = isset($request["room_1_adults"]);
        
        if ($hasHotelRoomData ) {
            // Case 1: Hotel room-specific data (hotel_room_{i}_adults, etc.)
            for ($i = 1; $i <= $roomCount; $i++) {
                $adults = (int)($request["hotel_room_{$i}_adults"] ?? 0);
                $children = (int)($request["hotel_room_{$i}_children"] ?? 0);
                $babies = (int)($request["hotel_room_{$i}_babies"] ?? 0);
                $childrenAges = $request["hotel_room_{$i}_child_ages"] ?? [];
                
                // Convert child ages from strings to integers
                $childrenAges = array_map('intval', $childrenAges);
                
                // Include babies as children with age 1
                $totalChildren = $children + $babies;
                
                // Add babies ages (default age 1 for each baby)
                for ($j = 0; $j < $babies; $j++) {
                    $childrenAges[] = 1;
                }
                
                $paxRooms[] = (object)[
                    'Adults' => $adults,
                    'Children' => $totalChildren > 0 ? $totalChildren : 0,
                    'ChildrenAges' => $childrenAges ?? []
                ];
            }
        } else if ($hasRoomData ) {
            // Case 1: Hotel room-specific data (hotel_room_{i}_adults, etc.)
            for ($i = 1; $i <= $roomCount; $i++) {
                $adults = (int)($request["room_{$i}_adults"] ?? 0);
                $children = (int)($request["room_{$i}_children"] ?? 0);
                $babies = (int)($request["room_{$i}_babies"] ?? 0);
                $childrenAges = $request["room_{$i}_child_ages"] ?? [];
                Log::info('Room Dataa Es', [
                    'adults' => $adults,
                    'children' => $children,
                    'babies' => $babies,
                    'childrenAges' => $childrenAges
                ]);
                // Convert child ages from strings to integers
                $childrenAges = array_map('intval', $childrenAges);
                
                // Include babies as children with age 1
                $totalChildren = $children + $babies;
                
                // Add babies ages (default age 1 for each baby)
                for ($j = 0; $j < $babies; $j++) {
                    $childrenAges[] = 1;
                }
                
                $paxRooms[] = (object)[
                    'Adults' => $adults,
                    'Children' => $totalChildren > 0 ? $totalChildren : 0,
                    'ChildrenAges' => $childrenAges ?? []
                ];
            }
        }else {
            // Case 2: General flight passenger data (adults, children, babies, flight_child_ages)
            $adults = (int)($request['adults'] ?? 1);
            $children = (int)($request['children'] ?? 0);
            $babies = (int)($request['babies'] ?? 0);
            $childrenAges = $request['flight_child_ages'] ?? [];
            
            // Convert child ages from strings to integers
            $childrenAges = array_map('intval', $childrenAges);
            
            // Include babies as children with age 1
            $totalChildren = $children + $babies;
            
            // Add babies ages (default age 1 for each baby)
            for ($j = 0; $j < $babies; $j++) {
                $childrenAges[] = 1;
            }
            
            // Create one room with all passengers
            $paxRooms[] = (object)[
                'Adults' => $adults,
                'Children' => $totalChildren > 0 ? $totalChildren : 0,
                'ChildrenAges' => $childrenAges ?? []
            ];
        }

        Log::info('paxRooms view', ['paxRooms' => $paxRooms]);

        // Calculate totals from paxRooms
        $totalAdults = 0;
        $totalChildren = 0;
        $totalInfants = 0;
        
        foreach ($paxRooms as $room) {
            $totalAdults += $room->Adults;
            $totalChildren += $room->Children;
            
            // Count children with age 1 as infants
            if (isset($room->ChildrenAges) && is_array($room->ChildrenAges)) {
                $infantsInRoom = count(array_filter($room->ChildrenAges, function($age) {
                    return $age == 1;
                }));
                $totalInfants += $infantsInRoom;
                $totalChildren -= $infantsInRoom; // Remove infants from children count
            }
        }

        $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
        $end_date = Carbon::parse($request->end_date)->format('Y-m-d');
        $priceRange = explode(',', $request->input('price_range')); // now it's an array


        $max_budget = isset($priceRange[1]) ? (int) $priceRange[1]:($request->budget ? explode(',', $request->budget)[1] : 500000);
        $min_budget = isset($priceRange[0]) ? (int) $priceRange[0]:($request->budget ? explode(',', $request->budget)[0] : 0);
        $trip = Destination::find($request->trip_id);
        
        // Handle three cases: numeric airport ID, city code, or city name
        if (is_numeric($request->destination)) {
            // Case 1: destination is an airport ID (e.g., "448")
            $destination = Airport::find($request->destination);
            
            // If not found by ID, try by city_code as fallback
            if (!$destination) {
                $destination = Airport::where('city_code', $request->destination)->first();
            }
        } else {
            // Case 2: destination is a city name - try trip city first, then request destination
            $destination = Airport::where('city', @$trip->city->name_en)->first();
            if (!$destination && $request->destination) {
                $destination = Airport::where('city', $request->destination)->first();
            }
        }
        
         

        $request->merge([
            'start_date' => $start_date,
            'end_date' => $end_date,
            'min_budget' => $min_budget,
            'max_budget' => $max_budget,
            'rooms' => $request->rooms,
            'adults' => $totalAdults,
            'children' => $totalChildren,
            'infants' => $totalInfants,
            'travel_interests' => $request->plans,
            'country_id' => $request->country_id,
            "paxRooms" => $paxRooms,
        ]);
        Log::info('Preparing search data afterr', ['$request->destination'=>$request->destination,'destinationVal'=> $destination ,
            'destination' => $destination ? @$destination->city_code : @Airport::where('city',$request->destination)->first()->city_code,
            'destination_name' => @$destination ? @$destination->city : $request->destination,
            'destination_code' => @$destination ? @$destination->country_code :  @Airport::where('city',$request->destination)->first()->country_code,]);
        return [
            'adults' => $totalAdults ?: 1,
            'children' => $totalChildren ?: 0,
            'guest_nationality' => $request->guest_nationality ?: 'SA',
            'infants' => $totalInfants ?: 0,
            'is_domestic' => $request->is_domestic ?: false,
            'one_stop' => $request->one_stop == 'on' ? true : false,
            'direct' => $request->direct == 'on' ? true : false,
            'journey_type' => $request->journey_type ?: 1,
            'currency' => session('currency') == 'SAR' ? 'SAR' : 'SAR',
            'origin' => @Airport::find($request->origin)->city_code,
            'origin_name' => @Airport::find($request->origin)->city,
            'destination' => $destination ? @$destination->city_code : @Airport::where('city',$request->destination)->first()->city_code,
            'destination_name' => @$destination ? @$destination->city : $request->destination,
            'destination_code' => @$destination ? @$destination->country_code :  @Airport::where('city',$request->destination)->first()->country_code,
            'flight_class' => $request->flight_class ?: 1,
            'start_date' => $start_date,
            'end_date' => $request->journey_type == 1 ? \Carbon\Carbon::parse($start_date)->addDay() :$end_date,
            'flight_time' => $request->flight_time ?: '00:00:00',
            'flight_time_return' => $request->flight_time_return ?: '00:00:00',
            'min_budget' => $min_budget,
            'max_budget' => $max_budget,
            'rooms' => $request->rooms ?: 1,
            'trip_type' => $tripType,
            'trip_id' => $request->trip_id,
            "paxRooms" => $paxRooms
        ];
    }

    private static function prepareSearchData($request, $tripType)
    {
        $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
        $end_date = Carbon::parse($request->end_date)->format('Y-m-d');
        $priceRange = explode(',', $request->input('price_range')); // now it's an array


        $max_budget = isset($priceRange[1]) ? (int) $priceRange[1]:($request->budget ? explode(',', $request->budget)[1] : 500000);
        $min_budget = isset($priceRange[0]) ? (int) $priceRange[0]:($request->budget ? explode(',', $request->budget)[0] : 0);
        $trip = Destination::find($request->trip_id);
        Log::info('Trip Details', ['trip' => $trip]);
        
        // Handle three cases: numeric airport ID, city code, or city name
        if (is_numeric($request->destination)) {
            // Case 1: destination is an airport ID (e.g., "448")
            $destination = Airport::find($request->destination);
            
            // If not found by ID, try by city_code as fallback
            if (!$destination) {
                $destination = Airport::where('city_code', $request->destination)->first();
            }
        } else {
            // Case 2: destination is a city name - try trip city first, then request destination
            $destination = Airport::where('city', @$trip->city->name_en)->first();
            if (!$destination && $request->destination) {
                $destination = Airport::where('city', $request->destination)->first();
            }
        }
        
        // Final fallback if no destination found
        if (!$destination) {
            $destination = Airport::find(448); // Default fallback by ID
        }

        $request->merge([
            'start_date' => $start_date,
            'end_date' => $end_date,
            'min_budget' => $min_budget,
            'max_budget' => $max_budget,
            'rooms' => $request->rooms ?: 1,
            'adults' => $request->adults ?: 1,
            'children' => $request->children ?: 0,
            'infants' => $request->babies ?: 0,
            'travel_interests' => $request->plans,
            'country_id' => $request->country_id ?? ($trip ? $trip->country_id : null),
        ]);

         Log::info('Preparing search data after', LogRedactor::redact(['request' => $request->all()]));

        return [
            'adults' => $request->adults ?: 1,
            'children' => $request->children ?: 0,
            'guest_nationality' => $request->guest_nationality ?: 'SA',
            'infants' => $request->infants ?: 0,
            'is_domestic' => $request->is_domestic ?: false,
            'one_stop' => $request->one_stop == 'on' ? true : false,
            'direct' => $request->direct == 'on' ? true : false,
            'journey_type' => $request->journey_type ?: 1,
            'currency' => session('currency') == 'SAR' ? 'SAR' : 'SAR',
            'origin' => @Airport::find($request->origin)->city_code,
            'origin_name' => @Airport::find($request->origin)->city,
            'destination' => $destination ? @$destination->city_code : @Airport::find($request->destination)->city_code,
            'destination_name' => @$destination ? @$destination->city : @Airport::find($request->destination)->city,
            'destination_code' => @$destination ? @$destination->country_code : @Airport::find($request->destination)->country_code,
            'flight_class' => $request->flight_class ?: 1,
            'start_date' => $start_date,
            'end_date' => $request->journey_type == 1 ? \Carbon\Carbon::parse($start_date)->addDay() :$end_date,
            'flight_time' => $request->flight_time ?: '00:00:00',
            'flight_time_return' => $request->flight_time_return ?: '00:00:00',
            'min_budget' => $min_budget,
            'max_budget' => $max_budget,
            'rooms' => $request->rooms ?: 1,
            'trip_type' => $tripType,
            'trip_id' => $request->trip_id,
        ];
    }

    public static function searchCities($country_code, $city_name)
    {
        try {
            Log::info('Searching for city', ['country_code' => $country_code, 'city_name' => $city_name]);
            $cities = Cache::remember('cities' . $country_code, 60 * 24 * 60, function () use ($country_code) {
                return (new TBOHotelBookingService)->getCityList(country_code: $country_code);
            });

            if ($cities['Status']['Code'] != 200) {
                Cache::forget('cities' . $country_code);
            }
        } catch (\Throwable $th) {
            Cache::forget('cities' . $country_code);
            Log::info($th->getMessage() . ' on line ' . $th->getLine() . ' in ' . $th->getFile());

            return false;
        }
       // Log::info('Cities fetched', ['city_name'=>$city_name,'cities' => $cities]);
        if (array_key_exists('CityList', $cities)) {
            $city_code = collect($cities['CityList'])->first(
                fn ($city) => Str::lower($city['Name']) === Str::lower($city_name)
            );

            return $city_code ? $city_code['Code'] : null;
        }

        return false;
    }

    public static function searchHotels($city_code)
    {
        $countHotelCache = TBOHotel::where('city_code', $city_code)->count();

        $setting = Setting::where('code', 'number_of_days_to_renew_hotel_cache')->first();

        if (
            $countHotelCache > 10 && TBOHotel::where('city_code', $city_code)
            ->where('cached_at', '<', now()->subDays(@$setting->value ?: 90))->count() > 10
        ) {
            return self::getHotelsFromCache($city_code);
        }

        if ($countHotelCache < 10) {
            Cache::forget('hotels' . $city_code);

            return self::getHotelsFromCache($city_code);
        }

        return TBOHotel::where('city_code', $city_code)->paginate(10);
    }

    private static function getHotelsFromCache($city_code)
    {
        $hotels = Cache::remember('hotels' . $city_code, 60 * 24, function () use ($city_code) {
            return (new TBOHotelBookingService)->getHotelCodeList(city_code: $city_code);
        });

        if ($hotels['Status']['Code'] != 200) {
            return false;
        }

        if (array_key_exists('Hotels', $hotels)) {
            $list = collect($hotels['Hotels'])->pluck('HotelCode')->chunk(50)->toArray();

            foreach ($list as $value) {
                CacheTBOHotelInDatabase::dispatch($value, app()->getLocale());
            }

            return TBOHotel::where('city_code', $city_code)->filterByRating()->paginate(10);
        }

        return collect([]);
    }

    public static function searchAvailableHotelHasRooms($request, $city_code)
    {
        $countHotelCache = TBOHotel::where('city_code', $city_code)->count();

        if ($countHotelCache > 10) {
            $hotels = TBOHotel::where('city_code', $city_code)->filterByRating()->get();
            $hotelsCodes = $hotels->pluck('code')->toArray();
        } else {
            $hotels = Cache::remember('hotels-without-details' . $city_code, 60 * 24, function () use ($city_code) {
                return (new TBOHotelBookingService)->getHotelCodeList(city_code: $city_code, is_detailed_response: true);
            });

            if ($hotels['Status']['Code'] != 200) {
                return false;
            }

            $list = collect($hotels['Hotels'])->pluck('HotelCode')->chunk(50)->toArray();

            foreach ($list as $value) {
                CacheTBOHotelInDatabase::dispatch($value, app()->getLocale());
            }

            $hotelsCodes = collect($hotels['Hotels'])->pluck('HotelCode')->toArray();
        }

        // Limit hotel codes to maximum 100 as per TBO API constraints
        $limitedHotelCodes = array_slice($hotelsCodes, 0, 100);
        
        // Log::info('Hotel search data', [
        //     'total_available_hotels' => count($hotelsCodes),
        //     'limited_hotels_sent' => count($limitedHotelCodes),
        //     'request_data' => $request->all()
        // ]);
        if(!isset($request['paxRooms']) || empty($request['paxRooms'])) {
           $paxRooms[] = (object)[
                'Adults' => (int) $request['adults'],
                'Children' =>  (int) $request['children'],
                'ChildrenAges' => (int) $request['infants']
            ];
        } else {
            $paxRooms = $request['paxRooms'];
        }
        
        $request->merge(['hotel_codes' => implode(',', $limitedHotelCodes),'paxRooms' => $paxRooms]);

        $data = [
            'hotel_codes' => implode(',', $limitedHotelCodes),
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'rooms' => (int) $request->rooms,
            'adults' => (int) $request->adults,
            'guest_nationality' => $request->guest_nationality ?? 'SA',
            'children' => (int) $request->children,
            'infants' => (int) $request->infants,
            'refundable' => $request->refundable,
            'meal_type' => $request->meal_type,
        ];

        // Log::info('Hotel search data with limited codes', [
        //     'total_available_hotels' => count($hotelsCodes),
        //     'limited_hotels_sent' => count($limitedHotelCodes),
        //     'data' => $data
        // ]);

        $rooms = Cache::remember('rooms' . implode('-', $data), 60 * 10, function () use ($request) {
            return (new TBOHotelBookingService)->searchAvailableHotelHasRooms($request);
        });

        // $rooms = (new TBOHotelBookingService)->searchAvailableHotelHasRooms($request);
        Log::info(' $rooms:', $rooms);
        if ($rooms['Status']['Code'] != 200) {
            return false;
        }

        $newRoom = self::prepareRoomData($rooms);

        return self::getHotelsWithRooms($hotels, $newRoom, $city_code);
    }




     public static function searchForHotelRooms($data)
    {
       $rooms = Cache::remember('rooms' . implode('-', $data), 60 * 10, function () use ($data) {
            return (new TBOHotelBookingService)->searchAvailableHotelHasRooms($data);
        });
        return $rooms;

         
        
    }

    private static function prepareRoomData($rooms)
    {
        $newRoom = [];

        if (array_key_exists('HotelResult', $rooms)) {
            foreach ($rooms['HotelResult'] as $value) {
                $newRoom[$value['HotelCode']] = [
                    "Rooms" => $value['Rooms'],
                    "Hotel" => $value,
                    'HotelCode' => $value['HotelCode'],
                    'Name' => $value['Rooms'][0]['Name'],
                    'Price' => $value['Rooms'][0]['TotalFare'],
                    'MealType' => $value['Rooms'][0]['MealType'],
                    'IsRefundable' => $value['Rooms'][0]['IsRefundable'],
                ];
            }
        }

        return $newRoom;
    }

    private static function getHotelsWithRooms($hotels, $newRoom, $city_code)
    {
        $destination = Destination::whereHas('city', function ($query) {
            $query->where('name_en', session()->get('preferences')['destination_name']);
        })->first();

        if (request()->sort) {
            $sort = explode('_', request()->sort);
            $sortKey = $sort[0];
            $sortOrder = $sort[1];
        }

        if ($hotels instanceof Collection) {
            $hotels = TBOHotel::where('city_code', $city_code)
                ->whereIn('code', array_keys($newRoom))
                ->filterByRating()
                ->when(request()->hotel_name, function ($query) {
                    $query->where(function ($query) {
                        $query->where('name_en', 'like', '%' . Str::lower(request()->hotel_name) . '%')
                            ->orWhere('name_ar', 'like', '%' . Str::lower(request()->hotel_name) . '%');
                    });
                })
                ->get()
                ->map(function ($hotel) use ($newRoom, $destination) {
                    $hotel['rooms'] = $newRoom[$hotel->code] ?? [];
                    $hotel['price'] = $newRoom[$hotel->code]['Price'];
                    $hotel['distance'] = (new DistanceService)->calculateDistance(@$hotel->latitude, @$hotel->longitude, @$destination->latitude, @$destination->longitude);

                    return $hotel;
                });
            if ((int)request()->min_budget >=0 && request()->max_budget) {
                $hotels = $hotels->
                where('price','>=',request()->min_budget)
                    ->where('price','<=',request()->max_budget)->values();
            }

            if (request()->sort) {
                $hotels = $hotels->sortBy(function ($hotel) use ($sortKey) {
                    return $hotel[$sortKey];
                }, SORT_REGULAR, $sortOrder == 'desc')->values();
            }

            $paginatedHotels = $hotels->paginate(10);

            $paginatedHotels->getCollection()->transform(function ($hotel) use ($newRoom, $destination) {
                $hotel['rooms'] = $newRoom[$hotel->code] ?? [];
                $hotel['price'] = $newRoom[$hotel->code]['Price'];
                $hotel['distance'] = (new DistanceService)->calculateDistance(@$hotel->latitude, @$hotel->longitude, @$destination->latitude, @$destination->longitude);

                return $hotel;
            });

            return $paginatedHotels;
        }

        if (is_array($hotels)) {
            $hotels = collect($hotels['Hotels'])->whereIn('HotelCode', array_keys($newRoom))
                ->when(request()->hotel_rating, fn($q) => $q->where('HotelRating', request()->hotel_rating))
                ->filter(function ($hotel) {
                    if (request()->hotel_name) {
                        return Str::contains(Str::lower($hotel['HotelName']), Str::lower(request()->hotel_name));
                    }

                    return true;
                })
                ->map(function ($hotel) use ($newRoom, $destination) {
                    $hotel['rooms'] = $newRoom[is_array($hotel) ? $hotel['HotelCode'] : $hotel->code] ?? [];
                    $hotel['price'] = $newRoom[$hotel['HotelCode']]['Price'];
                    $location = explode('|', $hotel['Map']);
                    $latitude = @$location[0];
                    $longitude = @$location[1];
                    $hotel['distance'] = (new DistanceService)->calculateDistance($latitude, $longitude, @$destination->latitude ?: 0, @$destination->longitude ?: 0);

                    return $hotel;
                });
            if ((int)request()->min_budget >=0 && request()->max_budget) {
                $hotels = $hotels->
                where('price','>=',request()->min_budget)
                    ->where('price','<=',request()->max_budget)->values();
            }

                if (request()->sort) {
                    $hotels = $hotels->sortBy(function ($hotel) use ($sortKey) {
                        return $hotel[$sortKey];
                    }, SORT_REGULAR, $sortOrder == 'desc')->values();
                }
                $hotels = $hotels->paginate(10);
        }
        Log::info('hotelsssss',[$hotels]);
        Log::info('requestsssss', LogRedactor::redact(['request' => request()->all()]));

        return $hotels;
    }
}
