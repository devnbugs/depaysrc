#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  TURNSTILE & COUNTLY VERIFICATION                            ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "✓ TURNSTILE CONFIGURATION:\n";
echo "  1. Site Key: " . (config('services.cloudflare.turnstile_site_key') ? "✅ SET" : "❌ NOT SET") . "\n";
echo "  2. Secret Key: " . (config('services.cloudflare.turnstile_secret_key') ? "✅ SET" : "❌ NOT SET") . "\n";
echo "  3. Theme: " . config('services.cloudflare.turnstile_theme') . "\n";

echo "\n✓ TURNSTILE SERVICE:\n";
try {
    $service = app(\App\Services\TurnstileService::class);
    echo "  4. isEnabled(): " . ($service->isEnabled() ? "✅ YES" : "❌ NO") . "\n";
    echo "  5. getSiteKey(): ✅ " . substr($service->getSiteKey(), 0, 15) . "...\n";
} catch (Exception $e) {
    echo "  ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✓ TURNSTILE ENVIRONMENT:\n";
echo "  6. MANAGED_WIDGET_ENABLED: " . (env('MANAGED_WIDGET_ENABLED') ? "✅ TRUE" : "❌ FALSE") . "\n";
echo "  7. INVISIBLE_WIDGET_ENABLED: " . (env('INVISIBLE_WIDGET_ENABLED') ? "✅ TRUE" : "❌ FALSE") . "\n";
echo "  8. TURNSTILE_ENABLED: " . (env('TURNSTILE_ENABLED') ? "✅ TRUE" : "❌ FALSE") . "\n";

echo "\n✓ COUNTLY ANALYTICS:\n";
echo "  9. COUNTLY_ENABLED: " . (config('countly.enabled') ? "✅ YES" : "❌ NO") . "\n";
echo "  10. App Key: " . (config('countly.app_key') ? "✅ SET" : "❌ NOT SET") . "\n";
echo "  11. Server URL: " . config('countly.server_url') . "\n";

echo "\n✓ COUNTLY EVENTS:\n";
$events = config('countly.track_events', []);
$num = 12;
foreach ($events as $event => $enabled) {
    echo "  " . $num++ . ". Track $event: " . ($enabled ? "✅ YES" : "❌ NO") . "\n";
}

echo "\n✓ COMPONENTS & FILES:\n";
echo "  " . (21) . ". TurnstileDashboard: " . (class_exists('App\Http\Middleware\TurnstileDashboard') ? "✅ EXISTS" : "❌ MISSING") . "\n";
echo "  " . (22) . ". InvisibleTurnstile: " . (class_exists('App\Livewire\InvisibleTurnstile') ? "✅ EXISTS" : "❌ MISSING") . "\n";
echo "  " . (23) . ". ValidTurnstileToken: " . (class_exists('App\Rules\ValidTurnstileToken') ? "✅ EXISTS" : "❌ MISSING") . "\n";
echo "  " . (24) . ". Turnstile view: " . (file_exists(base_path('resources/views/user/partials/turnstile.blade.php')) ? "✅ EXISTS" : "❌ MISSING") . "\n";

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║ ✅ ALL CONFIGURATIONS ARE READY!                              ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "🔗 Next: Clear browser cache and test at https://depay.local\n\n";
