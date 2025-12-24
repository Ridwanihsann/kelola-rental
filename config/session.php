<?php

use Illuminate\Support\Str;

return [
    // Session driver: file, cookie, database, redis, memcached, dynamodb, array
    'driver' => env('SESSION_DRIVER', 'database'),

    // Session lifetime in minutes
    'lifetime' => (int) env('SESSION_LIFETIME', 120),
    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    // Encrypt session data
    'encrypt' => env('SESSION_ENCRYPT', false),

    // File session location
    'files' => storage_path('framework/sessions'),

    // Database/Redis connection
    'connection' => env('SESSION_CONNECTION'),

    // Session database table
    'table' => env('SESSION_TABLE', 'sessions'),

    // Cache store for session
    'store' => env('SESSION_STORE'),

    // Session cleanup lottery (2 out of 100 requests)
    'lottery' => [2, 100],

    // Cookie settings
    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug((string) env('APP_NAME', 'laravel')) . '-session'
    ),
    'path' => env('SESSION_PATH', '/'),
    'domain' => env('SESSION_DOMAIN'),
    'secure' => env('SESSION_SECURE_COOKIE'),
    'http_only' => env('SESSION_HTTP_ONLY', true),
    'same_site' => env('SESSION_SAME_SITE', 'lax'),
    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),
];

