#!/usr/bin/env php
<?php
/**
 * COMPREHENSIVE TURNSTILE VERIFICATION SCRIPT
 * 
 * This script tests:
 * 1. .env configuration
 * 2. TurnstileService functionality
 * 3. Configuration loading
 * 4. Blade views
 * 5. Middleware
 * 6. Livewire components
 * 7. Validation rules
 */

// Bootstrap Laravel
require __DIR__ . '/bootstrap/app.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║        CLOUDFLARE TURNSTILE - COMPREHENSIVE VERIFICATION              ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

$passed = 0;
$failed = 0;
$total = 0;

// Helper function to print test result
function test_result(string $name, bool $result): void {
    global $passed, $failed, $total;
    $total++;
    if ($result) {
        echo "   ✅ $name\n";
        $passed++;
    } else {
        echo "   ❌ $name\n";
        $failed++;
    }
}

// ============================================================================
// TEST 1: Environment Variables
// ============================================================================
echo "TEST 1: Environment Variables\n";
echo str_repeat("─", 72) . "\n";

$site_key = env('CLOUDFLARE_TURNSTILE_SITE_KEY');
$secret_key = env('CLOUDFLARE_TURNSTILE_SECRET_KEY');
$enabled = env('TURNSTILE_ENABLED');
$managed = env('MANAGED_WIDGET_ENABLED');
$invisible = env('INVISIBLE_WIDGET_ENABLED');

test_result("CLOUDFLARE_TURNSTILE_SITE_KEY is set", !empty($site_key));
test_result("CLOUDFLARE_TURNSTILE_SECRET_KEY is set", !empty($secret_key));
test_result("TURNSTILE_ENABLED=true", $enabled === true || $enabled === 'true');
test_result("MANAGED_WIDGET_ENABLED=true", $managed === true || $managed === 'true');
test_result("INVISIBLE_WIDGET_ENABLED=true", $invisible === true || $invisible === 'true');

if (!empty($site_key)) {
    echo "   📝 Site Key: " . substr($site_key, 0, 10) . "..." . substr($site_key, -5) . "\n";
}
if (!empty($secret_key)) {
    echo "   📝 Secret Key: " . substr($secret_key, 0, 10) . "..." . substr($secret_key, -5) . "\n";
}

// ============================================================================
// TEST 2: Service Configuration
// ============================================================================
echo "\n\nTEST 2: Service Configuration\n";
echo str_repeat("─", 72) . "\n";

$config_site = config('services.cloudflare.turnstile_site_key');
$config_secret = config('services.cloudflare.turnstile_secret_key');
$config_theme = config('services.cloudflare.turnstile_theme', 'auto');

test_result("Site key loaded in config", !empty($config_site));
test_result("Secret key loaded in config", !empty($config_secret));
test_result("Theme loaded in config", !empty($config_theme));

echo "   📝 Theme: $config_theme\n";

// ============================================================================
// TEST 3: TurnstileService
// ============================================================================
echo "\n\nTEST 3: TurnstileService\n";
echo str_repeat("─", 72) . "\n";

try {
    $service = app(\App\Services\TurnstileService::class);
    test_result("TurnstileService instantiated", true);
    
    // Test methods
    test_result("isEnabled() method works", method_exists($service, 'isEnabled'));
    test_result("Turnstile is enabled", $service->isEnabled());
    test_result("getSiteKey() returns value", !empty($service->getSiteKey()));
    test_result("getTheme() returns value", !empty($service->getTheme()));
    
    echo "   📝 Site Key (from service): " . substr($service->getSiteKey(), 0, 10) . "...\n";
    echo "   📝 Theme (from service): " . $service->getTheme() . "\n";
} catch (\Exception $e) {
    test_result("TurnstileService instantiated", false);
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// ============================================================================
// TEST 4: Middleware
// ============================================================================
echo "\n\nTEST 4: Middleware\n";
echo str_repeat("─", 72) . "\n";

try {
    test_result("TurnstileDashboard middleware exists", 
        class_exists('App\Http\Middleware\TurnstileDashboard'));
} catch (\Exception $e) {
    test_result("TurnstileDashboard middleware exists", false);
}

// ============================================================================
// TEST 5: Livewire Components
// ============================================================================
echo "\n\nTEST 5: Livewire Components\n";
echo str_repeat("─", 72) . "\n";

try {
    test_result("InvisibleTurnstile component exists", 
        class_exists('App\Livewire\InvisibleTurnstile'));
} catch (\Exception $e) {
    test_result("InvisibleTurnstile component exists", false);
}

// ============================================================================
// TEST 6: Validation Rules
// ============================================================================
echo "\n\nTEST 6: Validation Rules\n";
echo str_repeat("─", 72) . "\n";

try {
    test_result("ValidTurnstileToken rule exists", 
        class_exists('App\Rules\ValidTurnstileToken'));
} catch (\Exception $e) {
    test_result("ValidTurnstileToken rule exists", false);
}

// ============================================================================
// TEST 7: Form Requests
// ============================================================================
echo "\n\nTEST 7: Form Requests\n";
echo str_repeat("─", 72) . "\n";

$form_requests = [
    'App\Http\Requests\LoginRequest',
    'App\Http\Requests\RegisterRequest',
    'App\Http\Requests\ContactFormRequest',
];

foreach ($form_requests as $request) {
    try {
        test_result(basename($request) . " exists", class_exists($request));
    } catch (\Exception $e) {
        test_result(basename($request) . " exists", false);
    }
}

// ============================================================================
// TEST 8: Blade Views
// ============================================================================
echo "\n\nTEST 8: Blade Views\n";
echo str_repeat("─", 72) . "\n";

$views = [
    'resources/views/user/partials/turnstile.blade.php',
    'resources/views/livewire/invisible-turnstile.blade.php',
];

foreach ($views as $view) {
    $path = base_path($view);
    test_result(basename($view) . " exists", file_exists($path));
    if (file_exists($path)) {
        $lines = count(file($path));
        echo "   📝 " . basename($view) . ": $lines lines\n";
    }
}

// ============================================================================
// TEST 9: Controllers
// ============================================================================
echo "\n\nTEST 9: Controllers\n";
echo str_repeat("─", 72) . "\n";

$controllers = [
    'App\Http\Controllers\ExampleTurnstileController',
    'App\Livewire\ExampleTurnstileComponents',
];

foreach ($controllers as $controller) {
    try {
        test_result(basename($controller) . " exists", class_exists($controller));
    } catch (\Exception $e) {
        test_result(basename($controller) . " exists", false);
    }
}

// ============================================================================
// TEST 10: Documentation
// ============================================================================
echo "\n\nTEST 10: Documentation\n";
echo str_repeat("─", 72) . "\n";

$docs = [
    'TURNSTILE_SETUP_GUIDE.md',
    'TURNSTILE_IMPLEMENTATION.md',
    '.env.turnstile.example',
    '.env.turnstile.local',
];

foreach ($docs as $doc) {
    $path = base_path($doc);
    test_result("$doc exists", file_exists($path));
}

// ============================================================================
// TEST 11: Rate Limiting & Security
// ============================================================================
echo "\n\nTEST 11: Rate Limiting & Security Methods\n";
echo str_repeat("─", 72) . "\n";

try {
    $service = app(\App\Services\TurnstileService::class);
    test_result("checkRateLimit() method", method_exists($service, 'checkRateLimit'));
    test_result("trackSuspiciousActivity() method", method_exists($service, 'trackSuspiciousActivity'));
    test_result("blockIP() method", method_exists($service, 'blockIP'));
    test_result("isIPBlocked() method", method_exists($service, 'isIPBlocked'));
} catch (\Exception $e) {
    echo "   ❌ Could not test security methods: " . $e->getMessage() . "\n";
}

// ============================================================================
// SUMMARY
// ============================================================================
echo "\n\n";
echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         TEST SUMMARY                                  ║\n";
echo "╠════════════════════════════════════════════════════════════════════════╣\n";
echo "║ Total Tests:    $total\n";
echo "║ Passed:         $passed ✅\n";
echo "║ Failed:         $failed ❌\n";

$percentage = $total > 0 ? round(($passed / $total) * 100, 1) : 0;
echo "║ Success Rate:   {$percentage}%\n";

if ($failed === 0) {
    echo "║ Status:         ✅ ALL TESTS PASSED\n";
} else {
    echo "║ Status:         ⚠️  SOME TESTS FAILED\n";
}
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

// Return exit code
exit($failed === 0 ? 0 : 1);
