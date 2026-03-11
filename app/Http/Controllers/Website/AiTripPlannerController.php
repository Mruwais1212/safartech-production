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
        $interests = TravelInterest::where('type', 'dalelah')->get();
        $items = AiPlannerSection::orderBy('sort', 'desc')->get();
        $settings_title_ar = Setting::where('code', 'ai_trip_planner_title_arabic')->first();
        $settings_title_en = Setting::where('code', 'ai_trip_planner_title_english')->first();
        $settings_content_en = Setting::where('code', 'ai_trip_planner_content_english')->first();
        $settings_content_ar = Setting::where('code', 'ai_trip_planner_content_arabic')->first();
        $settings_title = app()->getLocale() === 'ar' ? $settings_title_ar : $settings_title_en;
        $settings_content = app()->getLocale() === 'ar' ? $settings_content_ar : $settings_content_en;

        return view('website.ai-trip-planner', compact('interests', 'items', 'settings_title', 'settings_content'));
    }

    public function show(Request $request)
    {
        $request->validate([
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
            'destination' => ['required', 'string', 'max:255'],
        ]);

        $start_date = Carbon::parse($request->startDate)->format('Y-m-d');
        $end_date = Carbon::parse($request->endDate)->format('Y-m-d');
        $city = $request->destination;

        $travelInterests = $request->interests;
        $groupType = $request->come_with;
        $food = $request->food;

        $apiKey = config('services.openai.api_key');
        $apiUrl = config('services.openai.url');

        $startDate = Carbon::parse($start_date);
        $endDate = Carbon::parse($end_date);
        $currentDate = $startDate->copy();

        $airport = Airport::where('city', $city)->first();
        if (! $airport) {
            return back()->with('error', 'Sorry, This city is not available');
        }

        $city_code = SearchService::searchCities($airport->country_code, $city);
        if (! $city_code) {
            return back()->with('error', 'Sorry, This city is not available');
        }

        // Use the platform's primary market nationality as default; allow session override
        $guestNationality = session('preferences.nationality', 'SA');

        $request->merge([
            'check_in' => $start_date,
            'check_out' => $end_date,
            'guest_nationality' => $guestNationality,
            'rooms' => 1,
            'adults' => 1,
            'children' => 0,
            'infants' => 0,
        ]);

        session(['preferences' => [
            'destination_name' => $city,
            'destination_code' => $city_code,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'rooms' => 1,
            'adults' => 1,
            'children' => 0,
            'infants' => 0,
        ]]);

        $hotels = SearchService::searchAvailableHotelHasRooms($request, $city_code) ?? [];
        $hotels = json_encode(TBOHotelResource::collection($hotels));

        $cacheKey = 'city_data_'.md5($city.$start_date.$end_date);
        $city_data = Cache::remember($cacheKey, 60 * 24, fn () => $this->generateCity($city, $currentDate->format('Y-m-d'), $endDate->format('Y-m-d'), $apiKey, $apiUrl)
        );

        return view('website.ai-results', compact('hotels', 'start_date', 'end_date', 'city', 'travelInterests', 'groupType', 'food', 'city_data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
            'destination' => ['required', 'string', 'max:255'],
        ]);

        $start_date = Carbon::parse($request->startDate)->format('Y-m-d');
        $end_date = Carbon::parse($request->endDate)->format('Y-m-d');
        $city = $request->destination;

        $travelInterests = $request->interests;
        $groupType = $request->come_with;
        $food = $request->food;

        $apiKey = config('services.openai.api_key');
        $apiUrl = config('services.openai.url');

        $endDate = Carbon::parse($end_date);
        $currentDate = Carbon::parse($start_date);
        $cacheKey = 'itinerary_'.md5($city.$start_date.$end_date.(string) $groupType);

        return Cache::remember($cacheKey, 60 * 24, function () use ($city, $currentDate, $endDate, $travelInterests, $groupType, $food, $apiKey, $apiUrl) {
            $itinerary = [];

            while ($currentDate < $endDate) {
                $nextDate = $currentDate->copy()->addDays(4);
                if ($nextDate->gt($endDate)) {
                    $nextDate = $endDate->copy();
                }
                if ($currentDate >= $nextDate) {
                    break;
                }

                $chunk = $this->generateItinerary(
                    $city, $currentDate->format('Y-m-d'), $nextDate->format('Y-m-d'),
                    $travelInterests, $groupType, $food, $apiKey, $apiUrl
                );

                if ($chunk) {
                    $itinerary[] = $chunk;
                }

                $currentDate = $nextDate->addDay();
            }

            if (auth()->check()) {
                Tour::firstOrCreate(
                    ['user_id' => auth()->id(), 'city' => $city,
                        'date_from' => $currentDate->toDateString(), 'date_to' => $endDate->toDateString(), 'come_with' => $groupType],
                    ['tour_data' => json_encode($itinerary)]
                );
            }

            return $itinerary;
        });
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function generateItinerary($city, $start_date, $end_date, $travelInterests, $groupType, $food, $apiKey, $apiUrl): ?array
    {
        $isArabic = app()->getLocale() === 'ar';

        $systemPrompt = $isArabic
            ? 'You are a travel assistant. Create a detailed daily itinerary in Arabic for visiting attractions.
For each day include: attractions (morning/afternoon/evening) and restaurants (breakfast/lunch/dinner).
Each attraction/restaurant must include: name, description, lat, lng, location_name, photo (leave null), rating.
Return ONLY valid JSON with keys: city_name, days_count, city_desc, lat, lng, itinerary (array of day objects).'
            : 'You are a travel assistant. Create a detailed daily itinerary for visiting attractions.
For each day include: attractions (morning/afternoon/evening) and restaurants (breakfast/lunch/dinner).
Each attraction/restaurant must include: name, description, lat, lng, location_name, photo (leave null), rating.
Return ONLY valid JSON with keys: city_name, days_count, city_desc, lat, lng, itinerary (array of day objects).';

        $prompt = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => json_encode([
                'city' => $city,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'travel_interests' => $travelInterests,
                'group_type' => $groupType,
                'currency' => 'SAR',
                'food' => $food,
            ], JSON_UNESCAPED_UNICODE)],
        ];

        $t0 = microtime(true);
        $response = Http::withToken($apiKey)
            ->timeout(180)
            ->post($apiUrl, [
                'model' => 'gpt-4-turbo',
                'messages' => $prompt,
                'temperature' => 0.7,
                'max_tokens' => 4000,
                'top_p' => 1.0,
                'frequency_penalty' => 0.0,
                'presence_penalty' => 0.0,
            ]);
        Log::info('AI itinerary API time: '.round(microtime(true) - $t0, 2).'s');

        if (! $response->successful()) {
            Log::error('AI itinerary API failed', ['status' => $response->status(), 'body' => $response->body()]);

            return null;
        }

        $content = $response->json('choices.0.message.content');
        $itinerary = $content ? json_decode($content, true) : null;

        if (! is_array($itinerary)) {
            Log::error('AI itinerary JSON invalid', ['preview' => substr((string) $content, 0, 300)]);

            return null;
        }

        $googleKey = config('services.google_maps.api_key');

        if ($googleKey && isset($itinerary['itinerary']) && is_array($itinerary['itinerary'])) {
            foreach ($itinerary['itinerary'] as &$day) {
                foreach (['morning', 'afternoon', 'evening'] as $time) {
                    if (! is_array($day[$time] ?? null)) {
                        continue;
                    }
                    foreach ($day[$time] as &$attraction) {
                        $attraction['photo'] = $this->getGooglePhoto($attraction['name'] ?? '', $googleKey);
                    }
                    unset($attraction);
                }
                foreach (['breakfast', 'lunch', 'dinner'] as $meal) {
                    if (isset($day[$meal]['name'])) {
                        $day[$meal]['photo'] = $this->getGooglePhoto($day[$meal]['name'], $googleKey);
                    }
                }
            }
            unset($day);
        }

        return $itinerary;
    }

    private function generateCity($city, $start_date, $end_date, $apiKey, $apiUrl): array
    {
        $isArabic = app()->getLocale() === 'ar';

        $systemPrompt = $isArabic
            ? 'You are a travel assistant. Return ONLY a JSON object with keys: city_name (Arabic), days_count, city_desc (Arabic), lat, lng.'
            : 'You are a travel assistant. Return ONLY a JSON object with keys: city_name, days_count, city_desc, lat, lng.';

        $prompt = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => json_encode([
                'city' => $city,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ], JSON_UNESCAPED_UNICODE)],
        ];

        $t0 = microtime(true);
        $response = Http::withToken($apiKey)
            ->timeout(180)
            ->post($apiUrl, [
                'model' => 'gpt-4-turbo',
                'messages' => $prompt,
                'temperature' => 0.7,
                'max_tokens' => 1000,
                'top_p' => 1.0,
                'frequency_penalty' => 0.0,
                'presence_penalty' => 0.0,
            ]);
        Log::info('AI city data API time: '.round(microtime(true) - $t0, 2).'s');

        if (! $response->successful()) {
            Log::error('AI city API failed', ['status' => $response->status()]);

            return [];
        }

        $content = $response->json('choices.0.message.content');
        $city_data = $content ? json_decode($content, true) : null;

        if (! is_array($city_data)) {
            Log::error('AI city data JSON invalid', ['preview' => substr((string) $content, 0, 200)]);

            return [];
        }

        $googleKey = config('services.google_maps.api_key');
        if ($googleKey && isset($city_data['city_name'])) {
            $city_data['photos'] = $this->getCityPhotos($city_data['city_name'], $googleKey);
        }

        return $city_data;
    }

    private function getGooglePhoto(string $placeName, string $googleApiKey): ?string
    {
        if (empty($placeName) || empty($googleApiKey)) {
            return null;
        }
        try {
            $data = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
                'query' => $placeName,
                'key' => $googleApiKey,
            ])->json();
            $photoRef = $data['results'][0]['photos'][0]['photo_reference'] ?? null;
            if ($photoRef) {
                return "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference={$photoRef}&key={$googleApiKey}";
            }
        } catch (\Throwable $e) {
            Log::warning('Google Places photo failed', ['place' => $placeName, 'error' => $e->getMessage()]);
        }

        return null;
    }

    private function getCityPhotos(string $placeName, string $apiKey): array
    {
        if (empty($placeName) || empty($apiKey)) {
            return [];
        }
        try {
            $places = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
                'query' => $placeName,
                'key' => $apiKey,
            ])->json();
            $photos = [];
            foreach (array_slice($places['results'][0]['photos'] ?? [], 0, 5) as $p) {
                $photos[] = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference={$p['photo_reference']}&key={$apiKey}";
            }

            return $photos;
        } catch (\Throwable $e) {
            Log::warning('Google Places city photos failed', ['city' => $placeName, 'error' => $e->getMessage()]);

            return [];
        }
    }
}
