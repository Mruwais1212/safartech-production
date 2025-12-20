<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GooglePlaceService
{
    public function searchPlaces($query, $latitude = null, $longitude = null)
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
            'query' => $query,
            'location' => $latitude.','.$longitude,
            'key' => env('GOOGLE_MAPS_API_KEY'),
        ]);

        if ($response->json()['status'] != 'OK') {
            return null;
        }

        return 'https://maps.googleapis.com/maps/api/place/photo?maxwidth=940&photoreference='.$response->json()['results'][0]['photos'][0]['photo_reference'].'&key='.env('GOOGLE_MAPS_API_KEY');
    }

    public function getPlaceDetails($placeId)
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
            'place_id' => $placeId,
            'key' => env('GOOGLE_MAPS_API_KEY'),
        ]);

        return $response->json();
    }

    public function getPlacePhotos($photoReference)
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/place/photo', [
            'maxwidth' => 400,
            'photoreference' => $photoReference,
            'key' => env('GOOGLE_MAPS_API_KEY'),
        ]);

        return $response->body();
    }
}
