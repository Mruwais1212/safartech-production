<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'moyasar' => [
        'secret_key' => env('MOYASAR_SECRET_KEY'),
        'base_url'   => env('MOYASAR_BASE_URL', 'https://api.moyasar.com'),
    ],

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'flight' => [
        'baseUrl' => env('FLIGHT_BASE_URL'),
        'userId' => env('TRAVELOPRO_USER_ID'),
        'userPassword' => env('TRAVELOPRO_USER_PASSWORD'),
    ],

    'openai' => [
        'api_key' => env('OPEN_AI_API_KEY'),
        'key' => env('OPEN_AI_API_KEY'),
        'url' => env('OPEN_AI_URL'),
    ],

    'tbo' => [
        'username' => env('TBO_HOTEL_USERNAME'),
        'password' => env('TBO_HOTEL_PASSWORD'),
        'url' => env('TBO_HOTEL_URL'),
    ],

    'agoda' => [
        'url' => env('AGODA_HOTEL_URL'),
        'api_key' => env('AGODA_HOTEL_API_KEY'),
    ],

    'tbo_flight' => [
        'username' => env('TBO_FLIGHT_USERNAME'),
        'password' => env('TBO_FLIGHT_PASSWORD'),
        'url' => env('TBO_FLIGHT_URL'),
        'auth'=> env('Auth_Api_TOBO'),
        'after_booking' => env('TBO_FLIGHT_BOOKING_URL'),
        'RC_TBOINDIA_URL'=>env('RC_TBOINDIA_URL','https://api-stage.tboair.com/InternalAirService.svc/rest'),
        'search_url'=>env('TBO_FLIGHT_SEARCH_URL','https://apistaging.tboair.com/InternalAirService.svc/rest/Search/')
    ],
];
