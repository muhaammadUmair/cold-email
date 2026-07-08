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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'ai' => [
        'provider' => env('AI_PROVIDER', 'gemini'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'temperature' => env('GEMINI_TEMPERATURE', 0.4),
        'max_output_tokens' => env('GEMINI_MAX_OUTPUT_TOKENS', 420),
        'timeout' => env('GEMINI_TIMEOUT', 30),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'temperature' => env('OPENAI_TEMPERATURE', 0.4),
        'max_output_tokens' => env('OPENAI_MAX_OUTPUT_TOKENS', 420),
        'timeout' => env('OPENAI_TIMEOUT', 30),
    ],

    'gmail' => [
        'client_id' => env('GMAIL_CLIENT_ID'),
        'client_secret' => env('GMAIL_CLIENT_SECRET'),
        'refresh_token' => env('GMAIL_REFRESH_TOKEN'),
        'sender_name' => env('GMAIL_SENDER_NAME', env('MAIL_FROM_NAME', env('APP_NAME', 'Laravel'))),
        'sender_email' => env('GMAIL_SENDER_EMAIL'),
        'subject' => env('GMAIL_EMAIL_SUBJECT', 'Quick question'),
        'timeout' => env('GMAIL_TIMEOUT', 30),
    ],

];
