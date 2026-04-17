<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile
    |--------------------------------------------------------------------------
    |
    | Configure Cloudflare Turnstile keys and security behavior.
    |
    | Docs: https://developers.cloudflare.com/turnstile/
    |
    */
    'enabled' => (bool) env('CLOUDFLARE_TURNSTILE_ENABLED', true),

    'site_key' => env('CLOUDFLARE_TURNSTILE_SITE_KEY'),
    'secret_key' => env('CLOUDFLARE_TURNSTILE_SECRET_KEY'),

    'verify_url' => env('CLOUDFLARE_TURNSTILE_VERIFY_URL', 'https://challenges.cloudflare.com/turnstile/v0/siteverify'),

    /*
    | How long a successful "preclearance" lasts (session-based).
    */
    'preclearance_minutes' => (int) env('TURNSTILE_PRECLEARANCE_MINUTES', 12 * 60),

    /*
    | Basic abuse controls.
    */
    'rate_limit' => [
        'default_max_attempts' => (int) env('TURNSTILE_RATE_MAX_ATTEMPTS', 5),
        'default_decay_minutes' => (int) env('TURNSTILE_RATE_DECAY_MINUTES', 1),
    ],

    'blocking' => [
        'suspicious_threshold' => (int) env('TURNSTILE_SUSPICIOUS_THRESHOLD', 10),
        'block_minutes' => (int) env('TURNSTILE_BLOCK_MINUTES', 60),
    ],
];

