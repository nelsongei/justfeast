<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IntaSend Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Credentials and base URL for the IntaSend API.
    | Sandbox base URL: https://sandbox.intasend.com
    | Live base URL:    https://payment.intasend.com
    |
    */

    'publishable_key' => env('INTASEND_PUBLISHABLE_KEY'),

    'secret_key' => env('INTASEND_SECRET_KEY'),

    'base_url' => env('INTASEND_BASE_URL', 'https://sandbox.intasend.com'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Challenge
    |--------------------------------------------------------------------------
    |
    | This value is sent by IntaSend in every webhook payload as "challenge".
    | Set it in your IntaSend dashboard under Webhooks settings and match it
    | here to verify that the request truly came from IntaSend.
    |
    */

    'challenge' => env('INTASEND_WEBHOOK_CHALLENGE', ''),

];
