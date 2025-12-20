<?php

namespace App\Http\Controllers\Website;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class JourneyPlannerController extends Controller
{
    public function generateItinerary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'destination' => 'required|string|max:255',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'travel_group' => ['required', Rule::in(['solo', 'couple', 'family', 'friends'])],
            'selected_activities' => 'required|array|min:1|max:10',
            'selected_activities.*' => [
                'required',
                Rule::in([
                    'sightseeing_landmarks',
                    'outdoor_adventures',
                    'cultural_heritage_tours',
                    'food_drink_experiences',
                    'shopping_markets',
                    'relaxation_wellness',
                    'family_friendly_activities',
                    'nightlife_entertainment',
                    'water_based_activities',
                    'festivals_events',
                ]),
            ],
            'budget' => ['required', Rule::in(['low', 'medium', 'high'])],
            'food_preferences' => 'required|array|min:1',
            'food_preferences.*' => [
                'required',
                Rule::in([
                    'halal',
                    'vegetarian',
                    'vegan',
                    'pescatarian',
                    'gluten_free',
                    'dairy_free',
                    'no_restrictions',
                ]),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        try {
            $itinerary = $this->callOpenAI(
                $validatedData['destination'],
                $validatedData['start_date'],
                $validatedData['end_date'],
                $validatedData['travel_group'],
                $validatedData['selected_activities'],
                $validatedData['budget'],
                $validatedData['food_preferences']
            );

            return response()->json($itinerary);
        } catch (\Exception $e) {
            Log::info($e->getMessage().' on line '.$e->getLine().' in '.$e->getFile());

            return response()->json(['error' => 'An error occurred while generating the itinerary.'], 500);
        }
    }

    private function callOpenAI($destination, $start_date, $end_date, $travel_group, $selected_activities, $budget, $food_preferences)
    {
        $cacheKey = md5(json_encode(func_get_args()));

        return Cache::remember($cacheKey, 3600, function () use ($destination, $start_date, $end_date, $travel_group, $selected_activities, $budget, $food_preferences) {
            $openai_api_key = config('services.openai.api_key');

            if (! $openai_api_key) {
                throw new \Exception('OpenAI API key is not set');
            }

            $start_date_formatted = Carbon::parse($start_date)->format('F d, Y');
            $end_date_formatted = Carbon::parse($end_date)->format('F d, Y');
            $days = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date)) + 1;
            $activities_list = implode(', ', $selected_activities);
            $food_preferences_list = implode(', ', $food_preferences);

            $prompt = $this->buildPrompt($destination, $start_date_formatted, $end_date_formatted, $days, $travel_group, $activities_list, $budget, $food_preferences_list);

            $response = Http::withToken($openai_api_key)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful travel planner.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 4000,
                    'n' => 1,
                    'temperature' => 0.7,
                ]);

            if ($response->failed()) {
                Log::error('OpenAI API request failed', ['response' => $response->json()]);

                throw new \Exception('OpenAI API request failed: '.$response->body());
            }

            Log::info('OpenAI API Response', ['response' => $response->json()]);

            return $this->parseResponse($response->json()['choices'][0]['message']['content']);
        });
    }

    private function buildPrompt($destination, $start_date, $end_date, $days, $travel_group, $activities_list, $budget, $food_preferences_list)
    {
        return "Create a detailed {$days}-day itinerary for a {$travel_group} trip to {$destination} from {$start_date} to {$end_date}. The itinerary should focus on the following activities: {$activities_list}. The budget is {$budget}. Food preferences are {$food_preferences_list}. Include the following sections:

        1. Trip Overview:
           - Destination details with a brief description
           - Travel dates
           - A 7-day weather forecast summary
           - Brief trip description summarizing the theme

        2. Day-by-Day Schedule:
           For each day, provide:
           - Date and day number
           - Morning activities with time slots, descriptions, and locations
           - Lunch recommendations
           - Afternoon activities with time slots, descriptions, and locations
           - Evening plans including dinner recommendations and entertainment options

        3. Local Activities & Attractions:
           - List of top attractions and activities with descriptions, durations, and ratings
           - Categorize activities based on the provided activity types

        4. Dining & Cuisine:
           - Restaurant recommendations for each meal, categorized by cuisine and price
           - Nearby cafes and bars for quick stops

        Format your response in JSON with the following structure:
        {
          \"trip_overview\": {
            \"destination\": \"...\",
            \"description\": \"...\",
            \"start_date\": \"...\",
            \"end_date\": \"...\",
            \"weather_forecast\": [
              {
                \"date\": \"...\",
                \"temperature\": \"...\",
                \"condition\": \"...\"
              },
              ...
            ],
            \"trip_theme\": \"...\"
          },
          \"daily_schedule\": [
            {
              \"day\": 1,
              \"date\": \"...\",
              \"morning_activities\": [
                {
                  \"time\": \"...\",
                  \"description\": \"...\",
                  \"location\": \"...\"
                },
                ...
              ],
              \"lunch\": {
                \"restaurant\": \"...\",
                \"cuisine\": \"...\",
                \"price_range\": \"...\"
              },
              \"afternoon_activities\": [...],
              \"evening_plans\": {
                \"dinner\": {...},
                \"entertainment\": \"...\"
              }
            },
            ...
          ],
          \"activities_and_attractions\": [
            {
              \"name\": \"...\",
              \"category\": \"...\",
              \"description\": \"...\",
              \"duration\": \"...\",
              \"rating\": \"...\"
            },
            ...
          ],
          \"dining_options\": {
            \"restaurants\": [
              {
                \"name\": \"...\",
                \"cuisine\": \"...\",
                \"price_range\": \"...\",
                \"meal_type\": \"...\"
              },
              ...
            ],
            \"cafes_and_bars\": [
              {
                \"name\": \"...\",
                \"type\": \"...\",
                \"description\": \"...\"
              },
              ...
            ]
          }
        }";
    }

    private function parseResponse($content)
    {
        $decodedContent = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Failed to parse JSON response from OpenAI', ['content' => $content]);

            return ['error' => 'Failed to parse itinerary data'];
        }

        return $decodedContent;
    }
}
