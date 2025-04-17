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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'privy' => [
        'env' => env('PRIVY_ENV', 'staging'),
        'staging_url' => env('PRIVY_STAGING_URL'),
        'production_url' => env('PRIVY_PRODUCTION_URL'),
        'username' => env('PRIVY_USERNAME'),
        'password' => env('PRIVY_PASSWORD'),
        'api_key' => env('PRIVY_API_KEY'),
        'secret_key' => env('PRIVY_SECRET_KEY'),
    ],

];
