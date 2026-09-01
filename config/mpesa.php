<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Safaricom M-Pesa Daraja Configuration
    |--------------------------------------------------------------------------
    |
    | Credentials and URLs for Safaricom Daraja API (STK Push / Lipa Na M-Pesa Online).
    | Environment: 'sandbox' or 'live'
    |
    */

    'env' => env('MPESA_ENV', 'sandbox'),

    'consumer_key' => env('MPESA_CONSUMER_KEY', 'M30YC9fPjtZFnZmEBqQhIVHKiUfVU6kP'),

    'consumer_secret' => env('MPESA_CONSUMER_SECRET', 'IfaCQF73peu6xpBa'),

    'shortcode' => env('MPESA_SHORTCODE', '174379'), // Default Safaricom Sandbox Shortcode

    'passkey' => env('MPESA_PASSKEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'), // Sandbox Passkey

    'callback_url' => env('MPESA_CALLBACK_URL', 'https://zoyswfjujvc7.shares.zrok.io/api/mpesa/callback'),

    'transaction_type' => env('MPESA_TRANSACTION_TYPE', 'CustomerPayBillOnline'),

    'sandbox_base_url' => 'https://sandbox.safaricom.co.ke',

    'live_base_url' => 'https://api.safaricom.co.ke',

];
