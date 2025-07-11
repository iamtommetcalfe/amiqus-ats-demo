<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Amiqus API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Amiqus API integration.
    | These values are used by the AmiqusOAuthService to connect to the API.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Amiqus API.
    |
    */
    'api_url' => env('AMIQUS_API_URL', 'https://api.amiqus.co'),

    /*
    |--------------------------------------------------------------------------
    | Authentication URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Amiqus authentication service.
    |
    */
    'auth_url' => env('AMIQUS_AUTH_URL', 'https://id.amiqus.co'),

    /*
    |--------------------------------------------------------------------------
    | OAuth Endpoints
    |--------------------------------------------------------------------------
    |
    | The endpoints for OAuth authorization and token requests.
    |
    */
    'oauth' => [
        'authorize_endpoint' => '/oauth/authorize',
        'token_endpoint' => '/oauth/token',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    |
    | The endpoints for various API operations.
    |
    */
    'endpoints' => [
        'user_info' => '/api/v2/me',
        'templates' => '/api/v2/templates/records',
        'clients' => '/api/v2/clients',
    ],
];
