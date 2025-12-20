<?php

namespace App\Services;

use App\Http\Resources\DestinationResource;
use App\Models\Airport;
use App\Models\City;
use App\Models\Country;
use App\Models\Destination;
use App\Models\TravelInterest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class CacheDestinationService
{
    public static function getDestinations($request)
    {
        $country = null;

        if ($request->country_id) {
            $country = Country::find($request->country_id);
        }

        $travelInterests = implode(',', TravelInterest::when($request->travel_interests, function ($query) use ($request) {
            $query->whereIn('travel_interests.id', $request->travel_interests);
        })->pluck('name_en')->toArray());

        $apiKey = config('services.openai.key');
        $apiUrl = config('services.openai.url');
        Log::info('apiKey: '.$apiKey);
        Log::info('apiUrl: '.$apiUrl);

        $input = ['from_date' => $request->start_date, 'to_date' => $request->end_date, 'departure_location' => @Airport::find($request->origin)->country.' , '.@Airport::find($request->origin)->city, 'to' => @$country->name_en, 'travel_interests' => $travelInterests, 'budget' => $request->max_budget, 'currency' => session('currency') ? session('currency') : 'SAR'];

        // $prompt = [
        //     [
        //         'role' => 'system',
        //         'content' => "You are a travel recommendation expert. Based on the user's inputs,
        //         you will generate a list of destinations for the user to visit.
        //         The user's inputs include:
        //         departure location, start date, end date, budget, number of travelers,
        //         and travel interests.
        //         based on the user's budget, and number of days of travel based he can do that based on his departure location.
        //         based on the user's travel interests, you should recommend destinations that match the user's interests.
        //         based on the user's departure location,
        //         Should not recommend destinations that are too far from the user's departure location if the time is not enough.
        //         And Not recommend destinations that are not Be not stable or not safe now.
        //         You should provide a list of at least 6 cities along with basic information for each city.
        //         Include the city's name, country name, latitude and longitude,
        //         get description in both Arabic and English in key name description_ar and description_en.
        //         get country name in arabic and english as key name country with country code as code
        //         get city name in arabic and english as key name city
        //         get List of Travel Interests as key
        //         name travel_interests in this city in arabic and english in different keys in one of this only from this list({{$travelInterests}}) as list as name_ar and name_en each key as array
        //         get list of places in arabic and english in different keys of places user can visit in this city as list as name_ar and name_en each key as array key name places
        //     The response must be in valid JSON format.in json format not ```json",
        //     ],
        //     [
        //         'role' => 'user',
        //         'content' => json_encode($input),
        //     ],
        // ];

        $prompt = [
            [
                'role' => 'system',
                'content' => "You are a world-class travel consultant. 
        Suggest 9 diverse and trending travel destinations for 1 traveler(s) departing from {$input['departure_location']} between {$input['from_date']} and {$input['to_date']}. 
        if found {$input['to']} then cities should be included in this country.
        The total budget is {$input['budget']} {$input['currency']}. 
        The traveler(s) are interested in: {$travelInterests}. 
        For each suggested destination, consider the following:
        1. Feasibility: Ensure the destination is feasible given the trip length and budget. Exclude destinations that would require travel time or costs beyond what the user's schedule or budget allows.
        2. Trending Destinations: Focus on popular or trending destinations during this travel season.
        3. Travel Restrictions: Exclude any destinations that have travel restrictions from the departure country ({$input['departure_location']}).
        4. Cultural and Seasonal Events: Suggest destinations that align with any unique cultural festivals, events, or seasonal attractions during the user's travel period.
        5. Sustainability Considerations: Highlight destinations known for sustainable tourism practices, especially if they align with the user's interests in eco-friendly travel.
        6. Local Experience Suggestions: Include suggestions for immersive local experiences or hidden gems that offer an authentic feel of the destination beyond typical tourist spots.
        7. Safety and Health Considerations: Provide up-to-date information about the safety and health situation at the destination, including any relevant COVID-19 protocols or political stability information.
        8. Diversity in Destination Types: Ensure the list includes a mix of city, beach, nature, and cultural experiences to give the user a range of options.
        The user's inputs include:
        departure location, start date, end date, budget, number of travelers,
        and travel interests.
        based on the user's budget, and number of days of travel based he can do that based on his departure location.
        based on the user's travel interests, you should recommend destinations that match the user's interests.
        based on the user's departure location,
        Should not recommend destinations that are too far from the user's departure location if the time is not enough.
        And Not recommend destinations that are not Be not stable or not safe now.
        You should provide a list of at least 6 cities along with basic information for each city.
        Include the city's name, country name, latitude and longitude, (latitude and longitude)
        get latitude and longitude in key name latitude and longitude (latitude and longitude)
        get description in both Arabic and English in key name description_ar and description_en. (name_ar and name_en)
        get country name in arabic and english as key name country with country code as code (name_ar and name_en , code)
        get city name in arabic and english as key name city (name_ar and name_en)
        get List of Travel Interests as key
        name travel_interests in this city in arabic and english in different keys in one of this only from this list({{$travelInterests}}) as list as name_ar and name_en each key as array
        get list of places in arabic and english in different keys of places user can visit in this city as list as name_ar and name_en each key as array key name places
        The response must be in valid JSON format.in json format not ```json",
            ],
            [
                'role' => 'user',
                'content' => json_encode($input),
            ],
        ];

        Log::info('destinations'.implode('-', $input));

        if (! Cache::has('destinations'.implode('-', $input))) {
            $response = Cache::remember('destinations'.implode('-', $input), 60 * 60 * 24, function () use ($apiKey, $apiUrl, $prompt) {
                $result = Http::withToken($apiKey)
                    ->timeout(3600)
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

                if ($result->successful()) {
                    return $result['choices'][0]['message']['content'];
                }
            });

            Log::info($response, [
                'time' => microtime(true) - LARAVEL_START,
            ]);

            if ($data = json_decode($response)) {
                try {
                    $data = is_object($data) ? $data->destinations : $data['destinations'];
                } catch (Throwable $th) {
                    $data = json_decode($response);
                    Log::info($th->getMessage());
                }

                foreach ($data as $key => $raw) {
                    $enLang = @$raw->country->english ? 'english' : (@$raw->country->en ? 'en' : 'name_en');
                    $arLang = @$raw->country->arabic ? 'arabic' : (@$raw->country->ar ? 'ar' : 'name_ar');

                    $country = Country::where('name_en', $raw->country->$enLang)->first();

                    if (! $country) {
                        $country = Country::create(['name_en' => $raw->country->$enLang, 'name_ar' => $raw->country->$arLang, 'code' => $raw->country->code]);
                    }

                    $city = City::where('name_en', $raw->city->$enLang)->where('country_id', $country->id)->first();

                    if (! $city) {
                        $city = City::create(['name_en' => $raw->city->$enLang, 'name_ar' => $raw->city->$arLang, 'country_id' => $country->id]);
                    }

                    $destination = Destination::where('country_id', $country->id)->where('city_id', $city->id)->first();

                    if (! $destination) {
                        $destination = Destination::create([
                            'description_en' => $raw->description_en,
                            'description_ar' => $raw->description_ar,
                            'country_id' => $country->id,
                            'city_id' => $city->id,
                            'estimated_cost' => 0,
                            'latitude' => $raw->latitude,
                            'longitude' => $raw->longitude,
                        ]);

                        $image = (new GooglePlaceService)->searchPlaces($city->name_en.' , '.$country->name_en, $raw->latitude, $raw->longitude);

                        if ($image) {
                            $contents = file_get_contents($image);
                            $userImage = 'destination-'.time().'-'.uniqid().'.jpg';
                            $save_path = 'uploads/'.$userImage;
                            file_put_contents($save_path, $contents);
                            $destination->update(['image' => $userImage]);
                        }
                    }

                    if (@$raw->places) {
                        $places = $raw->places->$enLang ?? $raw->places->name_en;

                        foreach ($places as $key => $value) {
                            $places_ar = isset($raw->places->$enLang) ? $raw->places->$arLang : $raw->places->name_ar;

                            $place = $destination->places()->updateOrCreate(['name_en' => $value], ['name_ar' => $places_ar[$key]]);

                            // if (! file_exists('uploads/'.$place->image)) {
                            $image = (new GooglePlaceService)->searchPlaces($value, $raw->latitude, $raw->longitude);
                            if ($image) {
                                $contents = file_get_contents($image);
                                $userImage = 'destination-'.time().'-'.uniqid().'.jpg';
                                $save_path = 'uploads/'.$userImage;
                                file_put_contents($save_path, $contents);
                                $place->update(['image' => $userImage]);
                            }
                            // }
                        }
                    }

                    foreach ($raw->travel_interests->$enLang as $key => $value) {
                        $travelInterest = TravelInterest::where('name_en', $value)->first();
                        if ($travelInterest) {
                            if (! $destination->travelInterests()->where('travel_interests.id', $travelInterest->id)->exists()) {
                                $destination->travelInterests()->attach($travelInterest->id);
                            }
                        }
                    }
                    $destinationIds[] = $destination->id;
                }
            }
        }

        $currentDestinations = Destination::with('country', 'city', 'travelInterests', 'places')
            ->when($request->country_id, function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })
            ->when($request->city_id, function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            })
            ->when($request->travel_interests, function ($query) use ($request) {
                $query->whereHas('travelInterests', function ($query) use ($request) {
                    $query->whereIn('travel_interests.id', $request->travel_interests);
                });
            })
            ->when(count(@$destinationIds ?: []) > 0, function ($query) use (&$destinationIds) {
                $query->whereIn('id', @$destinationIds ?? []);
            })
            ->latest()
            ->take(8)
            ->get();

        return response()->json(['data' => DestinationResource::collection($currentDestinations)]);
    }
}
