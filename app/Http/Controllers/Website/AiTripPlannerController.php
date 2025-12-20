<?php

namespace App\Http\Controllers\Website;

use App\Http\Resources\TBOHotelResource;
use App\Models\Airport;
use App\Models\Panel\AiPlannerSection;
use App\Models\Panel\Setting;
use App\Models\Tour;
use App\Models\TravelInterest;
use App\Services\SearchService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiTripPlannerController extends Controller
{
    public function index()
    {
        $interests = TravelInterest::where('type','dalelah')->get();
        $items = AiPlannerSection::orderBy('sort', 'desc')->get();
        $settings_title_ar = Setting::where('code', 'ai_trip_planner_title_arabic')->first() ?: '';
        $settings_title_en = Setting::where('code', 'ai_trip_planner_title_english')->first() ?: '';
        $settings_content_en = Setting::where('code', 'ai_trip_planner_content_english')->first() ?: '';
        $settings_content_ar = Setting::where('code', 'ai_trip_planner_content_arabic')->first() ?: '';
        $settings_title = app()->getLocale() === 'ar' ? $settings_title_ar : $settings_title_en;
        $settings_content = app()->getLocale() === 'ar' ? $settings_content_ar : $settings_content_en;
        return view('website.ai-trip-planner', compact('interests', 'items', 'settings_title', 'settings_content'));
    }

    public function show(Request $request)
    {
        $datastart_date = Carbon::parse($request->startDate)->format('Y-m-d');
        $end_date = Carbon::parse($request->endDate)->format('Y-m-d');
        $city = $request->destination;
        $travelInterests = $request->interests;
        $groupType = $request->come_with;
        $food = $request->food;

        $city = $request->destination;
        $apiKey = env('OPEN_AI_API_KEY');
        $apiUrl = env('OPEN_AI_URL');

        $startDate = Carbon::parse($datastart_date);
        $endDate = Carbon::parse($end_date);
        $currentDate = $startDate;
        $data = [
            'city' => $city,
            'start_date' => $datastart_date,
            'end_date' => $end_date,
            'groupType' => $groupType,
        ];

        $airport = Airport::where('city', $city)->first();

        if (! $airport) {
            return back()->with('error', 'Sorry, This city is not available');
        }

        $city_code = SearchService::searchCities($airport->country_code, $city);

        if (! $city_code) {
            return back()->with('error', 'Sorry, This city is not available');
        }

        $request->merge([
            'check_in' => $datastart_date,
            'check_out' => $end_date,
            'guest_nationality' => 'EG', // $request->nationality'
            'rooms' => 1,
            'adults' => 1,
            'children' => 0,
            'infants' => 0,
        ]);

        session(['preferences' => [
            'destination_name' => $city,
            'destination_code' => $city_code,
            'start_date' => $datastart_date,
            'end_date' => $end_date,
            'rooms' => 1,
            'adults' => 1,
            'children' => 0,
            'infants' => 0,
        ]]);

        $hotels = SearchService::searchAvailableHotelHasRooms($request, $city_code);

        if (! $hotels) {
            $hotels = [];
        }

        $hotels = TBOHotelResource::collection($hotels);

        $hotels = json_encode($hotels);

        $city_data = Cache::remember('city_data' . implode('-', $data), 60 * 24, function () use ($city, $currentDate, $endDate, $apiKey, $apiUrl) {
            $city_data = $this->generateCity($city, $currentDate->format('Y-m-d'), $endDate->format('Y-m-d'), $apiKey, $apiUrl);
            return $city_data;
        });

        return view('website.ai-results', compact('hotels', 'datastart_date', 'end_date', 'city', 'travelInterests', 'groupType', 'food', 'city_data'));
    }

    public function store(Request $request)
    {
        $start_date = Carbon::parse($request->startDate)->format('Y-m-d');
        $end_date = Carbon::parse($request->endDate)->format('Y-m-d');
        $min_budget = $request->budget ? explode(',', $request->budget)[0] : 0;
        $max_budget = $request->budget ? explode(',', $request->budget)[1] : 1000000000000;
        $city = $request->destination;
        $budget = $max_budget;
        $travelInterests = $request->interests;
        $groupType = $request->come_with;
        $food = $request->food;
        $apiKey = env('OPEN_AI_API_KEY');
        $apiUrl = env('OPEN_AI_URL');

        $startDate = Carbon::parse($start_date);
        $endDate = Carbon::parse($end_date);
        $currentDate = $startDate;
        $data = [
            'city' => $city,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'groupType' => $groupType,
        ];

        return Cache::remember('itinerary' . implode('-', $data), 60 * 24, function () use ($city, $currentDate, $endDate, $travelInterests, $groupType, $food, $apiKey, $apiUrl) {
            $dayCount = 0;
            $itinerary = [];
            while ($currentDate != $endDate) {
                $nextDate = $currentDate->copy()->addDays(4);
                if ($nextDate->gt($endDate)) {
                    $nextDate = $endDate;
                }

                if ($currentDate >= $nextDate) {
                    break;
                }

                $itinerary[] = $this->generateItinerary($city, $currentDate->format('Y-m-d'), $nextDate->format('Y-m-d'), $travelInterests, $groupType, $food, $apiKey, $apiUrl);
                $currentDate = $nextDate->addDay();
                $dayCount++;
            }

            Log::info($itinerary, [$currentDate, $endDate, $dayCount]);


            if(auth()->user()){
                $cache = Tour::firstOrNew(
                    [
                        'user_id' => auth()->user()->id,
                        'city' => $city,
                        'date_from' => $currentDate,
                        'date_to' => $endDate,
                        'come_with' => $groupType
                    ],
                    [
                        'tour_data' => json_encode($itinerary)
                    ]
                );
                $cache->save();
            }

            return $itinerary;
        });
        // return to the view before returning the itinerary
        // return title with trip from start date to end date
        // return hotels for this city using Service SearchService::searchAvailableHotelHasRooms($request,$city_code)
        // return view to display the data
        // call api to get itinerary for each day and return the itinerary in view using front end preloading
    }

    private function generateItinerary($city, $start_date, $end_date, $travelInterests, $groupType, $food, $apiKey, $apiUrl)
    {
        if(app()->getLocale() == 'ar') {
            $prompt = [
                [
                    'role' => 'system',
                    'content' => "You are a travel assistant for a search engine.
                    Your task is to create a detailed daily itinerary for visiting attractions in a city in arabic.
                    Identify all dates in the following text and update them to the format YYYY-MM-DD. Preserve the surrounding text structure while ensuring all dates are correctly formatted.
                    For each day:
                    - Suggest restaurants for lunch and dinner, with names in Arabic, and their latitude/longitude.
                    - Include all days in the period from 'start_date' to 'end_date'. Ensure that each day in this period is covered.
                    - Include total daily cost ('estimated_daily_cost') with currency taken from the user, covering hotels, meals, and activities.
                    - Include the weather forecast for each day in the city.
                    - Adjust activity recommendations based on weather conditions (e.g., suggest indoor activities for rainy days).
                    - If the user is traveling with children, include a list of child-friendly attractions.
                    - If the user is traveling with a group, include a list of group-friendly attractions.
                    The response must be formatted as a JSON object with the following keys:
                        - 'city_name':  name of city in Arabic.
                        - 'days_count':  count of days.
                        - 'city_desc':  description of city in Arabic.
                        - 'lat':  The latitude of the city.
                        - 'lng':  The longitude of the city.
                        - 'itinerary': An array of objects representing each day's itinerary.
                        - Each object should have the following keys:
                        - 'date': The date in YYYY-MM-DD format.
                        - 'weather': An object containing weather information for the day in Arabic.
                        - 'temperature': The temperature in Celsius in Arabic.
                        - 'morning', 'afternoon', and 'evening': An array of attractions for the day.
                           - Each object should have the following keys:
                           - 'name': The name of the attraction in Arabic.
                           - 'description': The description of the attraction in Arabic.
                           - 'lat': The latitude of the attraction.
                           - 'lng': The longitude of the attraction.
                           - 'location_name': The location name of the attraction in Arabic.
                           - 'photo': The image URL of the attraction fetched using Google Places API.
                           - 'activities': An array of activities that can be done at the attraction.
                              - Each activity should have the following keys:
                              - 'name': The name of the activity in Arabic.
                        - 'breakfast', 'lunch', and 'dinner': An object representing restaurants for each meal.
                            - 'name': The name of the restaurant in Arabic.
                            - 'lat': The latitude of the restaurant.
                            - 'lng': The longitude of the restaurant.
                            - 'location_name': The location name of the restaurant in Arabic.
                            - 'photo': The image URL of the restaurant fetched using Google Places API.
                            - 'rating': The rating of the restaurant.
                    The response must be in valid JSON format and must cover each day from 'start_date' to 'end_date'.",
                ],
                [
                    'role' => 'user',
                    'content' => json_encode([
                        'city' => $city,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'travel_interests' => $travelInterests,
                        'group_type' => $groupType,
                        'currency' => 'SAR',
                        'food' => $food,
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                ],
            ];
        } else {
            $prompt = [
                [
                    'role' => 'system',
                    'content' => "You are a travel assistant for a search engine.
                    Your task is to create a detailed daily itinerary for visiting attractions in a city.
                    Identify all dates in the following text and update them to the format YYYY-MM-DD. Preserve the surrounding text structure while ensuring all dates are correctly formatted.
                    For each day:
                    - Suggest restaurants for lunch and dinner, with names in English and Arabic, and their latitude/longitude.
                    - Include all days in the period from 'start_date' to 'end_date'. Ensure that each day in this period is covered.
                    - Include total daily cost ('estimated_daily_cost') with currency taken from the user, covering hotels, meals, and activities.
                    - Include the weather forecast for each day in the city.
                    - Adjust activity recommendations based on weather conditions (e.g., suggest indoor activities for rainy days).
                    - If the user is traveling with children, include a list of child-friendly attractions.
                    - If the user is traveling with a group, include a list of group-friendly attractions.
                    The response must be formatted as a JSON object with the following keys:
                        - 'city_name':  name of city .
                        - 'days_count':  count of days .
                        - 'city_desc':  description of city .
                        - 'lat':  The latitude of the city.
                        - 'lng':  The longitude of the city.
                        - 'itinerary': An array of objects representing each day's itinerary.
                        - Each object should have the following keys:
                        - 'date': The date in YYYY-MM-DD format.
                        - 'weather': An object containing weather information for the day.
                        - 'temperature': The temperature in Celsius.
                        - 'morning', 'afternoon', and 'evening': An array of attractions for the day.
                           - Each object should have the following keys:
                           - 'name': The name of the attraction.
                           - 'description': The description of the attraction.
                           - 'lat': The latitude of the attraction.
                           - 'lng': The longitude of the attraction.
                           - 'location_name': The location name of the attraction.
                           - 'photo': The image URL of the attraction fetched using Google Places API.
                           - 'activities': An array of activities that can be done at the attraction.
                              - Each activity should have the following keys:
                              - 'name': The name of the activity.
                        - 'breakfast', 'lunch', and 'dinner': An object representing restaurants for each meal.
                            - 'name': The name of the restaurant in English.
                            - 'lat': The latitude of the restaurant.
                            - 'lng': The longitude of the restaurant.
                            - 'location_name': The location name of the restaurant.
                            - 'photo': The image URL of the restaurant fetched using Google Places API.
                            - 'rating': The rating of the restaurant.
                    The response must be in valid JSON format and must cover each day from 'start_date' to 'end_date'.",
                ],
                [
                    'role' => 'user',
                    'content' => json_encode([
                        'city' => $city,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'travel_interests' => $travelInterests,
                        'group_type' => $groupType,
                        'currency' => 'SAR',
                        'food' => $food,
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                ],
            ];
        }

        $start = microtime(true);

        $response = Http::withToken($apiKey)
            ->timeout(180)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($apiUrl, [
                'model' => 'gpt-4-turbo',
                'messages' => $prompt,
                'temperature' => 0.7,
                'max_tokens' => 4000,
                'top_p' => 1.0,
                'frequency_penalty' => 0.0,
                'presence_penalty' => 0.0,
            ]);
        $end = microtime(true);

        Log::info('Time taken: ' . ($end - $start));
        $content = $response['choices'][0]['message']['content'];
        
        // Log only content length and first few characters to avoid HTML in logs
        Log::info('AI Response received', [
            'content_length' => strlen($content),
            'content_preview' => substr(strip_tags($content), 0, 100) . '...'
        ]);

        $itinerary = json_decode($content, true);

        // Add photos to attractions and restaurants
        foreach ($itinerary['itinerary'] as &$day) {
            foreach (['morning', 'afternoon', 'evening'] as $time) {
                foreach ($day[$time] as &$attraction) {
                    $attraction['photo'] = $this->getGooglePhoto($attraction['name'], env('GOOGLE_MAPS_API_KEY'));
                }
            }

            foreach (['breakfast', 'lunch', 'dinner'] as $meal) {
                $day[$meal]['photo'] = $this->getGooglePhoto($day[$meal]['name'], env('GOOGLE_MAPS_API_KEY'));
            }
        }
        return $itinerary;
    }

    private function getGooglePhoto($placeName, $googleApiKey)
    {
        $placeSearchUrl = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=" . urlencode($placeName) . "&key=" . $googleApiKey;
        $response = Http::get($placeSearchUrl)->json();

        if (!empty($response['results'][0]['photos'][0]['photo_reference'])) {
            $photoReference = $response['results'][0]['photos'][0]['photo_reference'];
            return "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference={$photoReference}&key={$googleApiKey}";
        }

        return null;
    }

    // city data
    private function generateCity($city, $start_date, $end_date, $apiKey, $apiUrl)
    {
        if(app()->getLocale() == 'ar'){
            $prompt = [
                [
                    'role' => 'system',
                    'content' => "You are a travel assistant for a search engine.
                Your task is to create a detailed about city in arabic.
                The response must be formatted as a JSON object with the following keys:
                    - 'city_name': name of city in arabic.
                    - 'days_count': count of days.
                    - 'city_desc': description of city in arabic.
                    - 'lat': The latitude of the city.
                    - 'lng': The longitude of the city.
                The response must be in valid JSON format and must cover each day from 'start_date' to 'end_date'.",
                ],
                [
                    'role' => 'user',
                    'content' => json_encode([
                        'city' => $city,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                ],
            ];
        }else{
            $prompt = [
                [
                    'role' => 'system',
                    'content' => "You are a travel assistant for a search engine.
                Your task is to create a detailed about city.
                The response must be formatted as a JSON object with the following keys:
                    - 'city_name': name of city.
                    - 'days_count': count of days.
                    - 'city_desc': description of city.
                    - 'lat': The latitude of the city.
                    - 'lng': The longitude of the city.
                The response must be in valid JSON format and must cover each day from 'start_date' to 'end_date'.",
                ],
                [
                    'role' => 'user',
                    'content' => json_encode([
                        'city' => $city,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                ],
            ];
        }

        $start = microtime(true);

        $response = Http::withToken($apiKey)
            ->timeout(180)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($apiUrl, [
                'model' => 'gpt-4-turbo',
                'messages' => $prompt,
                'temperature' => 0.7,
                'max_tokens' => 4000,
                'top_p' => 1.0,
                'frequency_penalty' => 0.0,
                'presence_penalty' => 0.0,
            ]);

        $end = microtime(true);
        Log::info('Time taken to get city data: ' . ($end - $start));

        if ($response->successful()) {
            $content = $response['choices'][0]['message']['content'];
            $city_data = json_decode($content, true);

            if (isset($city_data['city_name'])) {
                $city_data['photos'] = $this->getCityPhotos($city_data['city_name'], env('GOOGLE_MAPS_API_KEY'));
            } else {
                Log::warning('City data does not contain city_name');
            }

            return $city_data;
        } else {
            return [];
        }
    }


    private function getCityPhotos($placeName, $apiKey)
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
            'query' => $placeName,
            'key' => $apiKey,
        ]);
        $places = $response->json();
        $photos = [];
        if (isset($places['results']) && count($places['results']) > 0) {
            $place = $places['results'][0];
            if (isset($place['photos']) && count($place['photos']) > 0) {
                $photoCount = 0;
                for ($i = 0; $i < count($place['photos']); $i++) {
                    if ($photoCount < 5) {
                        $photos[] = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference={$place['photos'][$i]['photo_reference']}&key={$apiKey}";
                        $photoCount++;
                    }
                }
            }
        }
        return $photos;
    }
}
