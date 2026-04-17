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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'paystack' => [
        'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co/'),
        'secret_key' => env('PAYSTACK_SECRET_KEY', env('PAYSTACK_SECRET')),
        'public_key' => env('PAYSTACK_PUBLIC'),
        'split_code' => env('PAYSTACK_SPLIT_CODE', 'SPL_tIaVNyQ7LX'),
        'charges' => (float) env('PAYSTACK_CHARGES', 0),
    ],

    'kora' => [
        'base_url' => env('KORA_BASE_URL', 'https://api.korapay.com/'),
        'secret_key' => env('KORA_SECRET_KEY'),
    ],

    'squad' => [
        'base_url' => env('SQUAD_BASE_URL', 'https://api-d.squadco.com/'),
        'secret_key' => env('SQUAD_SECRET_KEY'),
        'merchant_id' => env('SQUAD_MERCHANT_ID'),
    ],

    'budpay' => [
        'base_url' => env('BUDPAY_BASE_URL', 'https://api.budpay.com/api/v2/'),
        'secret_key' => env('BUDPAY_SECRET_KEY'),
        'public_key' => env('BUDPAY_PUBLIC_KEY'),
    ],

    'interswitch' => [
        'auth_url' => env('INTERSWITCH_AUTH_URL', 'https://passport-v2.k8.isw.la/passport/oauth/token'),
        'card_base_url' => env('INTERSWITCH_CARD_BASE_URL', 'https://fintech-card-management.k8.isw.la/'),
        'client_id' => env('INTERSWITCH_CLIENT_ID'),
        'client_secret' => env('INTERSWITCH_CLIENT_SECRET'),
    ],

    'easyaccess' => [
        'base_url' => env('EASYACCESS_BASE_URL', 'https://easyaccess.com.ng/api/'),
        'auth_token' => env('EASY_AUTH'),
        'airtime_url' => env('EASYACCESS_AIRTIME_URL', 'https://easyaccess.com.ng/api/airtime.php'),
        'data_url' => env('EASYACCESS_DATA_URL', 'https://easyaccess.com.ng/api/data.php'),
    ],

    'vtpass' => [
        'mode' => (int) env('MODE', 1),
        'base_url' => env('VTPASS_BASE_URL', 'https://vtpass.com/api/'),
        'sandbox_base_url' => env('VTPASS_SANDBOX_BASE_URL', 'https://sandbox.vtpass.com/api/'),
        'username' => env('VTPASSUSERNAME'),
        'password' => env('VTPASSPASSWORD'),
        'cable_charge' => (float) env('CABLECHARGE', 0),
        'power_charge' => (float) env('POWERCHARGE', 0),
        'waec_charge' => (float) env('WAECCHARGE', 0),
        'waec_result_charge' => (float) env('WAECRESULT', 0),
    ],

    'alrahuz' => [
        'base_url' => env('ALRAHUZ_BASE_URL', 'https://alrahuzdata.com/api/'),
        'api_key' => env('ALRAHUZ'),
    ],

    'gecharl_connect' => [
        'base_url' => env('GECHARL_CONNECT_BASE_URL'),
        'username' => env('GECHARL_CONNECT_USERNAME'),
        'api_key' => env('GECHARL_CONNECT_API_KEY'),
    ],

    'countly' => [
        'enabled' => env('COUNTLY_ENABLED', false),
        'server_url' => env('COUNTLY_SERVER_URL', 'https://your-countly-server.com'),
        'app_key' => env('COUNTLY_APP_KEY', ''),
        'debug' => env('COUNTLY_DEBUG', false),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/auth/google/callback'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Processing Configuration
    |--------------------------------------------------------------------------
    | 
    | Configure how payments are processed:
    | - 'queue': Use Laravel queue system (distributed, scalable)
    | - 'async': Use Spatie Async (local, immediate processing)
    |
    */
    'payment' => [
        // Processor: 'queue' or 'async'
        'processor' => env('PAYMENT_PROCESSOR', 'queue'),
        // Queue name (only used if processor = 'queue')
        'queue' => env('PAYMENT_QUEUE', 'payments'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Spatie Async Configuration
    |--------------------------------------------------------------------------
    |
    | Configure Spatie Async for high-performance local processing.
    | Only applies when PAYMENT_PROCESSOR=async
    |
    */
    'async' => [
        // Number of processes in the pool (default: CPU cores)
        'processes' => env('ASYNC_PROCESSES', 4),
        // Timeout for each process (seconds)
        'timeout' => env('ASYNC_TIMEOUT', 30),
        // Retry failed tasks
        'retries' => env('ASYNC_RETRIES', 3),
    ],

];
