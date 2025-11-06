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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'sms' => [
        'url' => env('SMS_PROVIDER_URL'),
        'key' => env('SMS_PROVIDER_KEY'),
        'sender' => env('SMS_SENDER', 'TheVirtualAcademy'),
    ],


    'zoom' => [
        'account_id'        => env('ZOOM_ACCOUNT_ID'),
        'client_id'         => env('ZOOM_CLIENT_ID'),
        'client_secret'     => env('ZOOM_CLIENT_SECRET'),
        'webhook_secret'    => env('ZOOM_WEBHOOK_SECRET'),
        'base_url'          => env('ZOOM_BASE_URL', 'https://api.zoom.us/v2'),
        'auth_url'          => env('ZOOM_AUTH_URL', 'https://zoom.us/oauth/token'),
        'default_timezone'  => env('ZOOM_DEFAULT_TIMEZONE', 'UTC'),
    ],

];
