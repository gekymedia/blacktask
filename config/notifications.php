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
    | Push Notification Configuration
    |--------------------------------------------------------------------------
    |
    | Configure web push notifications (VAPID keys for browsers).
    |
    */
    'push' => [
        'enabled' => env('PUSH_ENABLED', false),
        'vapid_public_key' => env('VAPID_PUBLIC_KEY'),
        'vapid_private_key' => env('VAPID_PRIVATE_KEY'),
        'subject' => env('VAPID_SUBJECT', 'mailto:admin@blacktask.com'),
        'server_key' => env('FCM_SERVER_KEY'), // For mobile push
    ],

    /*
    |--------------------------------------------------------------------------
    | Telegram Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your Telegram Bot API credentials here.
    |
    */
    'telegram' => [
        'enabled' => env('TELEGRAM_ENABLED', false),
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'api_url' => env('TELEGRAM_API_URL', 'https://api.telegram.org/bot'),
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

