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

    'razorpay' => [
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
    ],

    'social' => [
        'facebook' => [
            'page_id' => env('FACEBOOK_PAGE_ID'),
            'access_token' => env('FACEBOOK_ACCESS_TOKEN'),
        ],
        'instagram' => [
            'business_account_id' => env('INSTAGRAM_BUSINESS_ACCOUNT_ID', env('INSTAGRAM_PAGE_ID')),
            'access_token' => env('INSTAGRAM_ACCESS_TOKEN'),
        ],
        'whatsapp' => [
            'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
            'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
            'to_number' => env('WHATSAPP_TO_NUMBER'),
        ],
    ],

];
