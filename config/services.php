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

    'whatsapp' => [
        'endpoint' => env('WHATSAPP_OTP_ENDPOINT'),
        'token' => env('WHATSAPP_OTP_TOKEN'),
        'recipient' => env('WHATSAPP_OTP_NUMBER', '6289516003000'),
        'device' => env('WHATSAPP_OTP_DEVICE'),
        'country_code' => env('WHATSAPP_OTP_COUNTRY_CODE', '62'),
        'delay' => env('WHATSAPP_OTP_DELAY'),
        'typing' => env('WHATSAPP_OTP_TYPING', false),
    ],

    'password_reset' => [
        'fallback_email' => env('PASSWORD_RESET_FALLBACK_EMAIL'),
    ],

    'brevo' => [
        'api_key' => env('BREVO_API_KEY'),
        'endpoint' => env('BREVO_API_ENDPOINT', 'https://api.brevo.com/v3/smtp/email'),
        'senders_endpoint' => env('BREVO_SENDERS_ENDPOINT', 'https://api.brevo.com/v3/senders'),
        'events_endpoint' => env('BREVO_EVENTS_ENDPOINT', 'https://api.brevo.com/v3/smtp/statistics/events'),
    ],

];
