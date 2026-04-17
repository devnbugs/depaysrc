#!/usr/bin/env php
<?php
/**
 * QUICK TURNSTILE & COUNTLY VERIFICATION
 */

// Load Laravel
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║        CLOUDFLARE TURNSTILE & COUNTLY - VERIFICATION                  ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

// Test 1: Turnstile Config
echo "✓ TURNSTILE CONFIGURATION\n";
echo "  ├─ Site Key: " . (config('services.cloudflare.turnstile_site_key') ? "✅ SET" : "❌ NOT SET") . "\n";
echo "  ├─ Secret Key: " . (config('services.cloudflare.turnstile_secret_key') ? "✅ SET" : "❌ NOT SET") . "\n";
echo "  ├─ Theme: " . config('services.cloudflare.turnstile_theme', 'auto') . "\n";

// Test 2: TurnstileService
echo "\n✓ TURNSTILE SERVICE\n";
$service = app(\App\Services\TurnstileService::class);
echo "  ├─ isEnabled(): " . ($service->isEnabled() ? "✅ YES" : "❌ NO") . "\n";
echo "  ├─ Site Key: " . substr($service->getSiteKey(), 0, 15) . "...\n";
echo "  ├─ Theme: " . $service->getTheme() . "\n";

// Test 3: Security Methods
echo "\n✓ SECURITY FEATURES\n";
echo "  ├─ Rate Limiting: " . (method_exists($service, 'checkRateLimit') ? "✅ YES" : "❌ NO") . "\n";
echo "  ├─ IP Blocking: " . (method_exists($service, 'blockIP') ? "✅ YES" : "❌ NO") . "\n";
echo "  ├─ Suspicious Activity Tracking: " . (method_exists($service, 'trackSuspiciousActivity') ? "✅ YES" : "❌ NO") . "\n";

// Test 4: Components
echo "\n✓ COMPONENTS & VIEWS\n";
echo "  ├─ TurnstileDashboard Middleware: " . (class_exists('App\Http\Middleware\TurnstileDashboard') ? "✅ YES" : "❌ NO") . "\n";
echo "  ├─ InvisibleTurnstile Livewire: " . (class_exists('App\Livewire\InvisibleTurnstile') ? "✅ YES" : "❌ NO") . "\n";
echo "  ├─ ValidTurnstileToken Rule: " . (class_exists('App\Rules\ValidTurnstileToken') ? "✅ YES" : "❌ NO") . "\n";
echo "  ├─ Turnstile Blade View: " . (file_exists(base_path('resources/views/user/partials/turnstile.blade.php')) ? "✅ YES" : "❌ NO") . "\n";
echo "  └─ Invisible Turnstile View: " . (file_exists(base_path('resources/views/livewire/invisible-turnstile.blade.php')) ? "✅ YES" : "❌ NO") . "\n";

// Test 5: Countly
echo "\n✓ COUNTLY ANALYTICS\n";
echo "  ├─ Enabled: " . (config('countly.enabled') ? "✅ YES" : "❌ NO") . "\n";
echo "  ├─ Server URL: " . config('countly.server_url') . "\n";
echo "  ├─ App Key: " . (config('countly.app_key') ? "✅ SET" : "❌ NOT SET") . "\n";

// Test 6: Countly Events
echo "\n✓ COUNTLY EVENTS\n";
$events = config('countly.track_events', []);
foreach ($events as $event => $enabled) {
    $status = $enabled ? '✅' : '❌';
    echo "  ├─ $event: $status\n";
}

echo "\n";
echo "✅ VERIFICATION COMPLETE!\n\n";
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "NEXT STEPS:\n";
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "1. Clear browser cache and refresh pages\n";
echo "2. Visit: https://depay.local/user/login\n";
echo "3. You should see the Cloudflare Turnstile widget\n";
echo "4. Try submitting the form - it should work with CAPTCHA\n";
echo "5. Dashboard shows invisible verification working\n";
echo "\n";
