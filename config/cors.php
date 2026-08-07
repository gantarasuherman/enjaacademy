<?php

declare(strict_types=1);

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register'],

    'allowed_methods' => ['*'],

    /*
    | The React dev server and the built SPA. Add production origins here (or
    | via FRONTEND_URLS) — wildcards would defeat `supports_credentials`.
    */
    'allowed_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('FRONTEND_URLS', 'http://localhost:5173,http://127.0.0.1:5173,http://localhost:3000')),
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 3600,

    // Required for Sanctum's cookie-based SPA authentication.
    'supports_credentials' => true,
];
