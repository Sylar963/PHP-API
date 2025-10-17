<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
     * SECURITY: Restrict allowed methods to only what's necessary
     */
    'allowed_methods' => explode(',', env('CORS_ALLOWED_METHODS', 'GET,POST,PUT,DELETE,OPTIONS')),

    /*
     * SECURITY: Never use '*' in production - specify exact origins
     * For development, allow localhost variants
     */
    'allowed_origins' => env('APP_ENV') === 'production'
        ? explode(',', env('CORS_ALLOWED_ORIGINS', ''))
        : explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000,http://localhost:8080')),

    'allowed_origins_patterns' => [],

    /*
     * SECURITY: Restrict headers to only necessary ones
     */
    'allowed_headers' => explode(',', env('CORS_ALLOWED_HEADERS', 'Content-Type,Authorization,X-Requested-With,Accept,Origin')),

    /*
     * Expose custom headers to JavaScript if needed
     */
    'exposed_headers' => explode(',', env('CORS_EXPOSED_HEADERS', 'X-RateLimit-Limit,X-RateLimit-Remaining')),

    /*
     * Cache preflight requests for 1 hour
     */
    'max_age' => env('CORS_MAX_AGE', 3600),

    /*
     * SECURITY: Enable credentials support for Sanctum authentication
     */
    'supports_credentials' => filter_var(env('CORS_SUPPORTS_CREDENTIALS', 'true'), FILTER_VALIDATE_BOOLEAN),

];
