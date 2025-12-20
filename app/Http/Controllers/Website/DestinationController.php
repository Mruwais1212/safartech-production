<?php

namespace App\Http\Controllers\Website;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DestinationController extends Controller
{
    public function generateDestinations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'departure_location' => 'required|string|max:255',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'budget' => 'required|integer|min:0',
            'travelers' => 'required|integer|min:1|max:10',
            'travel_interests' => 'required|array|min:1',
            'travel_interests.*' => 'string|in:adventure_travel,cultural_exploration,food_and_culinary_tourism,wildlife_and_nature,beach_and_relaxation,historical_and_heritage_travel,art_and_architecture,backpacking_and_budget_travel,shopping,photography_and_visual_exploration',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        try {
            $destinations = $this->callOpenAI(
                $validatedData['departure_location'],
                $validatedData['start_date'],
                $validatedData['end_date'],
                $validatedData['budget'],
                $validatedData['travelers'],
                $validatedData['travel_interests']
            );

            return response()->json(['destinations' => $destinations]);
        } catch (\Exception $e) {
            Log::info($e->getMessage().' on line '.$e->getLine().' in '.$e->getFile());

            return response()->json(['error' => 'An error occurred while generating destinations.'], 500);
        }
    }

    private function callOpenAI($departure_location, $start_date, $end_date, $budget, $travelers, $travel_interests)
    {
        $cacheKey = md5(json_encode(func_get_args()));

        return Cache::remember($cacheKey, 3600, function () use ($departure_location, $start_date, $end_date, $budget, $travelers, $travel_interests) {
            $openai_api_key = config('services.openai.api_key');

            if (! $openai_api_key) {
                throw new \Exception('OpenAI API key is not set');
            }

            $start_date_formatted = Carbon::parse($start_date)->format('F d, Y');
            $end_date_formatted = Carbon::parse($end_date)->format('F d, Y');
            $interests_list = implode(', ', $travel_interests);

            $prompt = $this->buildPrompt($departure_location, $start_date_formatted, $end_date_formatted, $budget, $travelers, $interests_list);

            $response = Http::withToken($openai_api_key)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful travel assistant.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 3000,
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

    private function buildPrompt($departure_location, $start_date, $end_date, $budget, $travelers, $interests_list)
    {
        return "You are a world-class travel consultant. Suggest 9 diverse and trending travel destinations for {$travelers} traveler(s) departing from {$departure_location} between {$start_date} and {$end_date}. The total budget is {$budget} SAR. The traveler(s) are interested in: {$interests_list}. 

        For each suggested destination, consider the following:
        1. Feasibility: Ensure the destination is feasible given the trip length and budget. Exclude destinations that would require travel time or costs beyond what the user's schedule or budget allows.
        2. Trending Destinations: Focus on popular or trending destinations during this travel season.
        3. Travel Restrictions: Exclude any destinations that have travel restrictions from the departure country ({$departure_location}).
        4. Cultural and Seasonal Events: Suggest destinations that align with any unique cultural festivals, events, or seasonal attractions during the user's travel period.
        5. Sustainability Considerations: Highlight destinations known for sustainable tourism practices, especially if they align with the user's interests in eco-friendly travel.
        6. Local Experience Suggestions: Include suggestions for immersive local experiences or hidden gems that offer an authentic feel of the destination beyond typical tourist spots.
        7. Safety and Health Considerations: Provide up-to-date information about the safety and health situation at the destination, including any relevant COVID-19 protocols or political stability information.
        8. Diversity in Destination Types: Ensure the list includes a mix of city, beach, nature, and cultural experiences to give the user a range of options.

        For each destination, provide:
        1. Destination Name
        2. Country
        3. Primary Activity Type (choose the most relevant from the user's interests)
        4. Average Temperature (in Celsius)
        5. Brief Description (max 30 words, including why this destination is a good fit for the user's interests and season)
        6. Estimated Starting Price (in SAR)
        7. Three Top Attractions or Activities (aligned with user preferences)
        8. Best Time to Visit (within the travel period)
        9. Safety and Health Info (brief update on current situation)

        Format your response as a JSON array with the following structure:
        [
          {
            \"name\": \"Destination Name\",
            \"country\": \"Country Name\",
            \"primary_activity\": \"Primary Activity Type\",
            \"temperature\": \"X°C\",
            \"description\": \"Brief description including why it's a good fit\",
            \"starting_price\": \"X SAR\",
            \"top_attractions\": [\"Attraction/Activity 1\", \"Attraction/Activity 2\", \"Attraction/Activity 3\"],
            \"best_time\": \"Best time to visit within the period\",
            \"safety_info\": \"Brief safety and health update\"
          },
          ...
        ]";
    }

    private function parseResponse($content)
    {
        $decodedContent = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Failed to parse JSON response from OpenAI', ['content' => $content]);

            return ['error' => 'Failed to parse destination data'];
        }

        return $decodedContent;
    }
}
