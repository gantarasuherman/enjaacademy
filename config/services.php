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

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-flash-latest'),
    ],

    // Alternative "Buat dengan AI" provider — https://docs.x.ai. Billed per
    // token (no free tier like Gemini). Which provider is active is an admin
    // Setting (`ai_provider`, see AppServiceProvider) — these env values are
    // only the bootstrap/fallback path, same convention as `gemini` above.
    'grok' => [
        'key' => env('XAI_API_KEY'),
        'model' => env('GROK_MODEL', 'grok-4.6'),
    ],

    // Free tier, no credit card — https://console.groq.com/keys
    'groq' => [
        'key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'openai/gpt-oss-120b'),
    ],

    // QRIS checkout — see https://tripay.co.id/developer. Sandbox registration
    // is free and needs no business entity. Credentials are normally set via
    // Pengaturan → Integrasi (DB, encrypted) — these env values are only the
    // bootstrap/fallback path, same convention as `gemini` above.
    'tripay' => [
        'sandbox' => env('TRIPAY_SANDBOX', true),
        'merchant_code' => env('TRIPAY_MERCHANT_CODE'),
        'api_key' => env('TRIPAY_API_KEY'),
        'private_key' => env('TRIPAY_PRIVATE_KEY'),
        // Must match a channel actually enabled in the merchant's Tripay
        // dashboard (API & Integrasi → Simulator → Merchant → Channel
        // Pembayaran for sandbox) — "QRIS", "QRISC" and "QRIS2" all exist.
        'method' => env('TRIPAY_QRIS_METHOD', 'QRIS2'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
