<?php

return [
        /*
    |-------------------------------------------------------------------------- 
    | Webhook Timeout
    |--------------------------------------------------------------------------
    | Time in seconds to wait for a response from the webhook endpoint before timing out.
    */

    'webhook_timeout' => env('ALINMAPAY_WEBHOOK_TIMEOUT', 30),
    
    /*
    |--------------------------------------------------------------------------
    | Alinma Pay Environment
    |--------------------------------------------------------------------------
    */
    'environment' => env('ALINMAPAY_ENV', 'sandbox'), // sandbox | production

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    */
    'endpoints' => [
        'sandbox' => 'https://pg.alinmapay.com.sa/SB_Transactions/v2/payments/pay-request',
        'production' => 'https://pg.alinmapay.com.sa/Transactions/v2/payments/pay-request',
    ],

    /*
    |--------------------------------------------------------------------------
    | Merchant Credentials
    |--------------------------------------------------------------------------
    */
    'merchant_key' => env('ALINMAPAY_MERCHANT_KEY'),
    'terminal_id' => env('ALINMAPAY_TERMINAL_ID'),
    'terminal_password' => env('ALINMAPAY_TERMINAL_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */
    'webhook' => [
        'signature_header' => 'X-AlinmaPay-Signature',
        'verify_signature' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption Settings
    |--------------------------------------------------------------------------
    */
    'encryption' => [
        'algorithm' => 'AES-128-ECB',
        'encoding' => 'base64',
    ],
];