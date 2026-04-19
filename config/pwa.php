<?php

/**
 * PWA Configuration for OneTera Application
 * 
 * This configuration file handles all PWA-related settings
 */

return [
    /*
    |--------------------------------------------------------------------------
    | PWA Name and Description
    |--------------------------------------------------------------------------
    */
    'name' => 'OneTera',
    'short_name' => 'OneTera',
    'description' => 'Digital payment and financial services platform for seamless transactions, bill payments, and wealth management',
    
    /*
    |--------------------------------------------------------------------------
    | PWA Icons and Colors
    |--------------------------------------------------------------------------
    */
    'theme_color' => '#3b82f6',
    'background_color' => '#ffffff',
    'display' => 'standalone',
    'orientation' => 'portrait-primary',
    'scope' => '/',
    'start_url' => '/',
    
    /*
    |--------------------------------------------------------------------------
    | Service Worker Configuration
    |--------------------------------------------------------------------------
    */
    'service_worker' => [
        'path' => '/service-worker.js',
        'cache_name_prefix' => 'onetera-pwa',
        'cache_version' => 'v1',
        'update_interval' => 60000, // 1 minute in milliseconds
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Caching Strategy Configuration
    |--------------------------------------------------------------------------
    */
    'cache_strategies' => [
        'static_assets' => 'CACHE_FIRST', // CSS, JS, images, fonts
        'html_pages' => 'NETWORK_FIRST', // HTML for fresh content
        'api_calls' => 'NETWORK_FIRST', // API endpoints
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Offline Page
    |--------------------------------------------------------------------------
    */
    'offline_page' => '/offline.html',
    
    /*
    |--------------------------------------------------------------------------
    | Installation Prompt Settings
    |--------------------------------------------------------------------------
    */
    'install_prompt' => [
        'enabled' => true,
        'auto_show_after_visits' => 3,
        'show_delay' => 2000, // milliseconds
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Background Sync Configuration
    |--------------------------------------------------------------------------
    */
    'background_sync' => [
        'enabled' => true,
        'sync_tag_transfers' => 'sync-transfers',
        'sync_tag_payments' => 'sync-payments',
        'retry_interval' => 5 * 60 * 1000, // 5 minutes
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Performance Optimization
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'lazy_loading_enabled' => true,
        'lazy_loading_margin' => '50px',
        'minify_assets' => true,
        'gzip_compression' => true,
        'critical_assets' => [
            '/',
            '/manifest.json',
            '/offline.html',
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'enabled' => true,
        'badge' => '/assets/icons/badge-72x72.png',
        'icon' => '/assets/icons/icon-192x192.png',
        'default_timeout' => 5000, // milliseconds
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Analytics and Monitoring
    |--------------------------------------------------------------------------
    */
    'analytics' => [
        'monitor_core_web_vitals' => true,
        'monitor_performance' => true,
        'track_offline_usage' => true,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Security Headers for PWA
    |--------------------------------------------------------------------------
    */
    'security' => [
        'require_https' => true,
        'service_worker_script_policies' => [
            'script-src' => "'self'",
            'default-src' => "'self'",
        ],
    ],
];
