<?php

namespace App\Http\Controllers\Website;

use App\Http\Resources\TBOHotelResource;
use App\Models\Airport;
use App\Models\City;
use App\Models\Destination;
use App\Models\ReservationHotel;
use App\Models\TBOHotel;
use App\Services\SearchService;
use App\Services\TBOHotelBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rooms' => ['nullable', function ($attribute, $value, $fail) {
                if ($value && $value > 6) {
                    $fail('The number of rooms may not be greater than 6.');
                }
            }],
        ]);

        $validator->validate();
        Log::info('Search Request Dataaa: ', $request->all());
        SearchService::search($request, 2);

        return redirect('/hotels');
    }

    public function searchNew(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rooms' => ['nullable', function ($attribute, $value, $fail) {
                if ($value && $value > 6) {
                    $fail('The number of rooms may not be greater than 6.');
                }
            }],
        ]);

        $validator->validate();

        SearchService::searchNew($request, 2);
        Log::info("paxRooms mm", $request->all());
        return redirect('/hotels');
    }


    public function index(Request $request)
    {
        $preferences = session('preferences');

        if (! $preferences) {
            return redirect('/ai-trip')->with('error', 'Please select your destination first');
        }
        $priceRange = explode(',', $request->input('price_range')); // now it's an array
        if (isset($priceRange[0]) || isset($priceRange[1])) {
            $preferences['max_budget'] = isset($priceRange[1]) ? (int)$priceRange[1] : $preferences['max_budget'];
            $preferences['min_budget'] = isset($priceRange[0]) ? (int)$priceRange[0] : $preferences['min_budget'];
            session()->put('preferences', $preferences); // update the session

        }
        $city_code = SearchService::searchCities($preferences['destination_code'], $preferences['destination_name']);
        Log::info('City Code: ', ['city_code' => $city_code, 'preferences' => $preferences]);
        if (! $city_code) {
            return back()->with('error', 'Sorry, This city is not available');
        }
        $paxRooms[] = (object)[
                'Adults' => (int) $preferences['adults'],
                'Children' =>  (int) $preferences['children'],
                'ChildrenAges' => (int) $preferences['infants']
            ];
        request()->merge(input: [
            'destination' => $preferences['destination'],
            'checkin' => $preferences['start_date'],
            'checkout' => $preferences['end_date'],
            'check_in' => $preferences['start_date'],
            'check_out' => $preferences['end_date'],
            'guest_nationality' => $preferences['guest_nationality'] ?:'SA',
            'rooms' => (int) $preferences['rooms'],
            'adults' => (int) $preferences['adults'],
            'children' => (int) $preferences['children'],
            'infants' => (int) $preferences['infants'],
            'city_code' => $city_code,
            'refundable' => request()->refundable,
            'hotel_name' => request()->hotel_name,
            'min_budget' => $preferences['min_budget'],
            'max_budget' => $preferences['max_budget'],
            'paxRooms' => $preferences['paxRooms'] ?? $paxRooms,

        ]);
        Log::info('request(): ',request()->all());
        $hotels = SearchService::searchAvailableHotelHasRooms(request(), $city_code);
        Log::info('Hotels: '.json_encode($hotels));
        if (! $hotels) {
            $hotels = collect([]);

            return view('website.hotels', ['hotels' => json_encode($hotels),'preferences'=>$preferences]);
        }

        request()->merge(input: [
            'pages' => is_array($hotels) ? (count($hotels['Hotels']) / 10) : $hotels->lastPage(),
        ]);

        session(['city_code' => $city_code]);
        $hotels->{'hotels'} = TBOHotelResource::collection($hotels);
//        dd($preferences);
        return view('website.hotels', ['hotels' => json_encode($hotels),'preferences'=>$preferences]);
    }

    public function selectRoom($id)
    {
        try{
            $preBookRoom = (new TBOHotelBookingService)->perBook($id);

            // Log only essential information instead of the full response which may contain HTML
            Log::info('Hotel pre-booking initiated for ID: ' . $id, [
                'has_hotel_result' => array_key_exists('HotelResult', $preBookRoom),
                'response_keys' => array_keys($preBookRoom)
            ]);

            if (array_key_exists('HotelResult', $preBookRoom)) {
                $currency = $preBookRoom['HotelResult'][0]['Currency'];
                $TotalFare = $preBookRoom['HotelResult'][0]['Rooms'][0]['TotalFare'];
                $RateConditions = $preBookRoom['HotelResult'][0]['RateConditions'];
                session([
                    'hotel.TotalFareHotel' => $TotalFare,
                    'hotel.RateConditionsHotel' => $RateConditions,
                    'hotel.BookingCodeHotel' => $id,
                    'hotel.CurrencyHotel' => $currency,
                    "hotel.selected_room" => $preBookRoom['HotelResult'][0]['Rooms'][0],
                ]);
            } else {
                Cache::forget('rooms'.@session('HotelCode'));

                return back()->with('error', $preBookRoom['Status']['Description']);
            }

            if (request()->hotel) {
                $hotel = Cache::remember(key: 'hotel'.request()->hotel.app()->getLocale(), ttl: 60 * 24 * 60, callback: function () use ($id) {
                    return (new TBOHotelBookingService)->getHotelDetails(hotel_code: $id);
                });

                if (array_key_exists('HotelDetails', $hotel)) {
                    $hotel = $hotel['HotelDetails'][0];
                } else {
                    $hotel = TBOHotel::where('code', operator: request()->hotel)->first();
                }

                session(['hotel.selected_hotel' => json_encode(TBOHotelResource::make($hotel))]);
            }

            if (session('preferences')['trip_type'] == '1') {
                return redirect('flights');
            }

            return redirect(to: 'summary');
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return back()->with('error', 'Something went wrong please try again');
        }
    }

    public function show($id)
    {
        $hotel = TBOHotel::where('code', $id)->first();

        if (! $hotel || ($hotel && ! (app()->getLocale() == 'ar' ? $hotel->name_ar : $hotel->name_en)) || ($hotel && ($hotel->name_ar == $hotel->name_en))) {
            $cacheHotel = Cache::remember(key: 'hotel'.$id.app()->getLocale(), ttl: 60 * 24 * 60, callback: function () use ($id) {
                return (new TBOHotelBookingService)->getHotelDetails(hotel_code: $id);
            });

            if ($cacheHotel['Status']['Code'] == 200) {
                $hotel = array_key_exists('HotelDetails', array: $cacheHotel) ? $cacheHotel['HotelDetails'][0] : null;

                $location = explode('|', $hotel['Map']);
                $latitude = @$location[0];
                $longitude = @$location[1];

                $hotelSave = TBOHotel::where('code', $id)->first();

                TBOHotel::updateOrCreate(['code' => $hotel['HotelCode']], [
                    'name_ar' => app()->getLocale() == 'ar' && ! @$hotelSave->name_ar ? @$hotel['HotelName'] : @$hotelSave->name_ar,
                    'name_en' => app()->getLocale() == 'en' && ! @$hotelSave->name_en ? @$hotel['HotelName'] : @$hotelSave->name_en,
                    'description_ar' => app()->getLocale() == 'ar' && ! @$hotelSave->description_ar ? @$hotel['Description'] : @$hotelSave->description_ar,
                    'description_en' => app()->getLocale() == 'en' && ! @$hotelSave->description_en ? @$hotel['Description'] : @$hotelSave->description_en,
                    'address' => @$hotel['Address'],
                    'facilities_ar' => app()->getLocale() == 'ar' && ! @$hotelSave->facilities_ar ? json_encode(@$hotel['HotelFacilities']) : @$hotelSave->facilities_ar,
                    'facilities_en' => app()->getLocale() == 'en' && ! @$hotelSave->facilities_en ? json_encode(@$hotel['HotelFacilities']) : @$hotelSave->facilities_en,
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
                    
                ]);
            }
        }

        if (! $hotel || ! session()->has('preferences')) {
            return back()->with('error', 'Something went wrong please try again');
        }

        if ($hotel) {
            $data = [
                'check_in' => session('preferences')['start_date'],
                'check_out' => session('preferences')['end_date'],
                'hotel_codes' => $hotel->code ?? $hotel['HotelCode'],
                'guest_nationality' => session('preferences')['guest_nationality']?? 'SA',
                'paxRooms' => session('preferences')['paxRooms'] ?? [],

                'rooms' => (int) session('preferences')['rooms'],
                'adults' => (int) session('preferences')['adults'],
                'children' => (int) session('preferences')['children'],
                'infants' => (int) session('preferences')['infants'],
            ];
            Log::info('TBO Search Rooms Request data', ['request' => $data]);

            $rooms = (new TBOHotelBookingService)->searchRooms(request()->merge($data));
        }

        if (array_key_exists('HotelResult', $rooms)) {
            session(['HotelCode' => @$hotel->code ?? @$hotel['HotelCode']]);
            $rooms = $rooms['HotelResult'][0]['Rooms'];
        } else {
            $rooms = [];
        }

        $hotel = json_encode(TBOHotelResource::make($hotel));
        Log::info('Hotel Details: ', ['hotel' => $hotel, 'rooms' => $rooms]);
        return view('website.hotel-single', compact('hotel', 'rooms'));
    }

    public function choose($id)
    {
        $trip = Destination::find($id);

        if (! $trip) {
            return back()->with('error', 'Trip does not exist');
        }

        $city_code = SearchService::searchCities($trip->country->code, $trip->city->name_en);

        if (! $city_code) {
            return back()->with('error', 'This city is not available');
        }
        Log::info('trip:SESSION '.json_encode(@session('preferences')));
        request()->merge([
            'destination' => @$trip->city->name_en,
            'rooms' => @session('preferences')['rooms'],
            'adults' => @session('preferences')['adults'],
            'children' => @session('preferences')['children'],
            'infants' => @session('preferences')['infants'],
            'checkin' => @session('preferences')['start_date'],
            'checkout' => @session('preferences')['end_date'],
            'check_in' => session('preferences')['start_date'],
            'check_out' => session('preferences')['end_date'],
            "paxRooms" => @session('preferences')['paxRooms'] ?? [],
            'guest_nationality' => session('preferences')['guest_nationality'] ?? 'SA',
            'city_code' => $city_code,

        ]);
        Log::info('request.all: ',request()->all());
        Log::info('city_code: '.json_encode($city_code));
        $hotels = SearchService::searchAvailableHotelHasRooms(request(), $city_code);
        Log::info('hotels: '.json_encode($hotels));

        if (! $hotels) {
            return back()->with('error', 'Sorry, No Hotels Found');
        } Log::info('Hotels: '.json_encode($hotels));

        session([
            'preferences.destination_code' => @Airport::where('city', 'like', '%'.Str::limit($trip->city->name_en, 4, '').'%')->where('country', $trip->country->name_en)->first()->country_code,
            'preferences.destination_name' => @Airport::where('city', 'like', '%'.Str::limit($trip->city->name_en, 4, '').'%')->where('country', $trip->country->name_en)->first()->city,
            'preferences.destination' => @Airport::where('city', 'like', '%'.Str::limit($trip->city->name_en, 4, '').'%')->where('country', $trip->country->name_en)->first()->city_code,
            'preferences.trip_id' => $trip->id,
        ]);

        $hotels->{'hotels'} = TBOHotelResource::collection($hotels);
       
        return view('website.hotels', ['hotels' => json_encode($hotels), 'trip' => $trip]);
    }

    public function searchCity(Request $request)
    {
        $searchTerm = trim($request->city_name);
        
        // Check if the search term contains Arabic characters
        $isArabic = preg_match('/[\x{0600}-\x{06FF}]/u', $searchTerm);
        
        // Return empty if search term is too short (1 char for Arabic, 2 for English)
        $minLength = $isArabic ? 1 : 2;
        if (mb_strlen($searchTerm, 'UTF-8') < $minLength) {
            return response()->json([]);
        }
        
        Log::info('City Search Input: ', [
            'search_term' => $searchTerm,
            'is_arabic' => $isArabic,
            'request' => $request->all()
        ]);

        $query = City::query();
        
        if ($isArabic) {
            // For Arabic search, prioritize Arabic field but also search English
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name_ar', 'like', '%'.$searchTerm.'%')
                  ->orWhere('name_en', 'like', '%'.$searchTerm.'%');
            });
            // Order Arabic matches first
            $query->orderByRaw("CASE WHEN name_ar LIKE ? THEN 1 ELSE 2 END", ['%'.$searchTerm.'%']);
        } else {
            // For English/Latin search, prioritize English field but also search Arabic
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name_en', 'like', '%'.$searchTerm.'%')
                  ->orWhere('name_ar', 'like', '%'.$searchTerm.'%');
            });
            // Order English matches first
            $query->orderByRaw("CASE WHEN name_en LIKE ? THEN 1 ELSE 2 END", ['%'.$searchTerm.'%']);
        }
        
        $cities = $query->take(5)->get();
        
        Log::info('City Search Results: ', [
            'search_term' => $searchTerm,
            'is_arabic' => $isArabic,
            'results_count' => $cities->count(),
            'results' => $cities->toArray()
        ]);
        
        return response()->json($cities);
    }

    public function searchAirport(Request $request)
    {
        $airports = Airport::where(function ($query) use ($request) {
            $query->where('city', 'like', $request->city_name.'%')
                ->orWhere('city_code', 'like', $request->city_name.'%');
        })
            ->take(5)->get();

        return response()->json($airports);
    }

    public function searchAirportCity(Request $request)
    {
        $airports = Airport::where(function ($query) use ($request) {
            $query->where('city', 'like', $request->city_name.'%')
                ->orWhere('country', 'like', $request->city_name.'%');
        })
            ->groupBy('city', 'country')
            ->take(5)->get();

        return response()->json($airports);
    }

    public function cancel_hotel($booking_code)
    {
        $hotel_reservation = ReservationHotel::where('booking_code', $booking_code)
            ->where('status', 1)
            ->first();

        if (! $hotel_reservation) {
            abort(404);
        }

        if ((int) optional($hotel_reservation->reservation)->user_id !== (int) auth('web')->id()) {
            abort(403);
        }

        (new TBOHotelBookingService)->cancel($hotel_reservation);

        return $hotel_reservation->is_refundable == 0
            ? redirect()->back()->with('success', __('dashboard.reservation_canceled'))
            : redirect()->back()->with('success', __('dashboard.cancel_pending'));
    }
}
