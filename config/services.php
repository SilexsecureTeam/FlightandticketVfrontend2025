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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'paystack' => [
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
    ],
    'alatpay' => [
        'api_key' => env('ALATPAY_API_KEY'),
        'business_id' => env('ALATPAY_BUSINESS_ID'),
        'webhook_secret' => env('ALATPAY_WEBHOOK_SECRET'),
        'callback_url' => env('ALATPAY_CALLBACK_URL'),
        'webhook_url' => env('ALATPAY_WEBHOOK_URL'),
        'js_url' => env('ALATPAY_JS_URL', 'https://web.alatpay.ng/js/alatpay.js'),
        'whitelist_ips' => array_values(array_filter(array_map('trim', explode(',', (string) env('ALATPAY_WHITELIST_IPS', '74.178.162.156'))))),
        'notify_email' => env('ALATPAY_NOTIFY_EMAIL', 'mymoretravels@gmail.com'),
    ],
    'amadeus' => [
        'base_url' => env('AMADEUS_BASE_URL', 'https://test.travel.api.amadeus.com'),
        'client_id' => env('AMADEUS_API_KEY'),
        'client_secret' => env('AMADEUS_API_SECRET'),
        'verify_ssl' => env('AMADEUS_VERIFY_SSL', env('APP_ENV', 'production') === 'production'),
    ],

];
