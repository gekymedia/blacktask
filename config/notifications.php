<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your WhatsApp Business API credentials here.
    | Get your API credentials from WhatsApp Business Platform.
    |
    */
    'whatsapp' => [
        'enabled' => env('WHATSAPP_ENABLED', false),
        'api_url' => env('WHATSAPP_API_URL', 'https://api.whatsapp.com/send'),
        'token' => env('WHATSAPP_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your SMS service (Twilio, Nexmo, etc.) credentials here.
    |
    */
    'sms' => [
        'enabled' => env('SMS_ENABLED', false),
        'provider' => env('SMS_PROVIDER', 'twilio'), // twilio, nexmo, etc.
        'api_url' => env('SMS_API_URL'),
        'token' => env('SMS_TOKEN'),
        'from' => env('SMS_FROM_NUMBER'),
    ],

    /*
    |--------------------------------------------------------------------------
    | GeKyChat Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your GeKyChat API credentials here.
    |
    */
    'gekychat' => [
        'enabled' => env('GEKYCHAT_ENABLED', false),
        'api_url' => env('GEKYCHAT_API_URL', 'https://api.gekychat.com/messages'),
        'token' => env('GEKYCHAT_TOKEN'),
        'app_id' => env('GEKYCHAT_APP_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Defaults
    |--------------------------------------------------------------------------
    |
    | Default settings for notifications.
    |
    */
    'defaults' => [
        'daily_digest_time' => env('DAILY_DIGEST_TIME', '09:00'),
        'reminder_hours_before' => env('REMINDER_HOURS_BEFORE', 2),
    ],
];

