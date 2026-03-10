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
            'key' => config('services.google_maps.api_key'),
        ]);

        $json = $response->json();
        if (($json['status'] ?? '') !== 'OK' || empty($json['results'][0]['photos'][0]['photo_reference'])) {
            return null;
        }

        return 'https://maps.googleapis.com/maps/api/place/photo?maxwidth=940&photoreference='.$json['results'][0]['photos'][0]['photo_reference'].'&key='.config('services.google_maps.api_key');
    }

    public function getPlaceDetails($placeId)
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
            'place_id' => $placeId,
            'key' => config('services.google_maps.api_key'),
        ]);

        return $response->json();
    }

    public function getPlacePhotos($photoReference)
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/place/photo', [
            'maxwidth' => 400,
            'photoreference' => $photoReference,
            'key' => config('services.google_maps.api_key'),
        ]);

        return $response->body();
    }
}
