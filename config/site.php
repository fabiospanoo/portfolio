<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Canonical domain
    |--------------------------------------------------------------------------
    |
    | Redirect any non-allowed host to the canonical domain (301) and build
    | SEO URLs (sitemap, canonical links) from these values. Using config()
    | instead of env() keeps this working after `php artisan config:cache`.
    |
    */

    'canonical_host' => env('CANONICAL_HOST'),

    'app_scheme' => env('APP_SCHEME', 'https'),

    'allowed_hosts' => array_values(array_filter(array_map('trim', explode(',', (string) env('ALLOWED_HOSTS', ''))))),

    'auto_migrate' => (bool) env('AUTO_MIGRATE', false),
];
