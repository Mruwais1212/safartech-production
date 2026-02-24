<?php

return [
    'allowed_hosts' => [
        'maps.googleapis.com',
        'lh3.googleusercontent.com',
        '*.googleusercontent.com',
    ],
    'max_download_size' => env('MEDIA_MAX_DOWNLOAD_SIZE', 5 * 1024 * 1024),
    'allowed_content_types' => [
        'image/jpeg',
        'image/png',
        'image/webp',
    ],
    'timeout_seconds' => 10,
];
