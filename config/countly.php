<?php

/**
 * Countly Analytics Configuration
 * 
 * Countly is an open-source product analytics platform that tracks
 * user behavior, events, and metrics in real-time
 * 
 * This is configured for authenticated users only (no admin panel)
 * and is adapted for fintech/payment platform use cases
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Countly Enable/Disable
    |--------------------------------------------------------------------------
    |
    | Enable or disable Countly analytics entirely
    | Set to false to completely disable analytics
    |
    */
    'enabled' => env('COUNTLY_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Countly Server URL
    |--------------------------------------------------------------------------
    |
    | The URL of your Countly server
    | Example: https://analytics.example.com
    | For cloud: https://cloud.countly.com
    |
    */
    'server_url' => env('COUNTLY_SERVER_URL', 'https://your-countly-server.com'),

    /*
    |--------------------------------------------------------------------------
    | Countly App Key
    |--------------------------------------------------------------------------
    |
    | Your Countly application key
    | Find this in: Countly Dashboard → Settings → Your Apps → App Key
    |
    */
    'app_key' => env('COUNTLY_APP_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Countly Log Channel
    |--------------------------------------------------------------------------
    |
    | Log channel for Countly-related logs
    | Recommended: 'countly' or 'single'
    |
    */
    'log_channel' => env('COUNTLY_LOG_CHANNEL', 'countly'),

    /*
    |--------------------------------------------------------------------------
    | Tracked Events
    |--------------------------------------------------------------------------
    |
    | Which event categories to track
    | For fintech apps: auth, payments, bills, security, features
    |
    */
    'track_events' => [
        'auth' => true,              // Login, register, logout, 2FA events
        'payments' => true,          // Deposits, withdrawals, transfers
        'bills' => true,             // Airtime, data, utilities, cables, exams
        'security' => true,          // Security events, suspicious activities
        'features' => true,          // Feature usage tracking
        'errors' => true,            // Error and exception tracking
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Configuration
    |--------------------------------------------------------------------------
    |
    | Countly session settings
    |
    */
    'session' => [
        'timeout' => env('COUNTLY_SESSION_TIMEOUT', 15), // minutes
        'auto_extend' => true,       // Auto-extend session on activity
    ],

    /*
    |--------------------------------------------------------------------------
    | Consent Management
    |--------------------------------------------------------------------------
    |
    | GDPR compliance - require consent before tracking
    | Set to true if you need explicit user consent
    |
    */
    'require_consent' => env('COUNTLY_REQUIRE_CONSENT', false),

    'consent_categories' => [
        'sessions' => true,          // Track sessions
        'events' => true,            // Track events
        'views' => true,             // Track page views
        'crashes' => true,           // Track errors/crashes
        'attribution' => false,      // Track attribution
        'users' => true,             // Track user data
    ],

    /*
    |--------------------------------------------------------------------------
    | User Segmentation
    |--------------------------------------------------------------------------
    |
    | Segment users based on custom properties
    | Useful for cohort analysis
    |
    */
    'user_segments' => [
        'account_type' => 'user.account_type',
        'verification_status' => 'user.verification_status',
        'total_transactions' => 'user.total_transactions',
        'lifetime_value' => 'user.balance',
    ],

    /*
    |--------------------------------------------------------------------------
    | Batch Configuration
    |--------------------------------------------------------------------------
    |
    | Events are batched and sent together for efficiency
    |
    */
    'batch' => [
        'size' => env('COUNTLY_BATCH_SIZE', 50),      // Events per batch
        'timeout' => env('COUNTLY_BATCH_TIMEOUT', 60), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | HTTP request timeout for Countly API calls
    |
    */
    'request_timeout' => env('COUNTLY_REQUEST_TIMEOUT', 10), // seconds

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    |
    | Enable debug logging for Countly operations
    | Set to true to get detailed logs in countly.log
    |
    */
    'debug' => env('COUNTLY_DEBUG', false),
];
