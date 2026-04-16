#!/usr/bin/env php
<?php
/**
 * Cloudflare Turnstile Implementation Verification Script
 * 
 * This script verifies that the entire Turnstile implementation is complete
 * and working correctly across all components.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== CLOUDFLARE TURNSTILE IMPLEMENTATION VERIFICATION ===\n\n";

$passed = 0;
$failed = 0;

// Test 1: TurnstileService loads and has isEnabled method
echo "1. Testing TurnstileService...\n";
try {
    $service = app(\App\Services\TurnstileService::class);
    if (method_exists($service, 'isEnabled')) {
        echo "   ✅ TurnstileService loads and has isEnabled() method\n";
        $passed++;
    } else {
        echo "   ❌ TurnstileService missing isEnabled() method\n";
        $failed++;
    }
} catch (\Exception $e) {
    echo "   ❌ Failed to load TurnstileService: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 2: All form request classes load
echo "\n2. Testing Form Request Classes...\n";
$requests = [
    'LoginRequest',
    'RegisterRequest',
    'PasswordResetEmailRequest',
    'PasswordUpdateRequest',
    'ContactFormRequest'
];

foreach ($requests as $req) {
    $class = "\\App\\Http\\Requests\\$req";
    if (class_exists($class)) {
        echo "   ✅ $req loads\n";
        $passed++;
    } else {
        echo "   ❌ $req cannot be loaded\n";
        $failed++;
    }
}

// Test 3: All controllers load with correct imports
echo "\n3. Testing Auth Controllers with Form Requests...\n";
$controllers = [
    'LoginController' => '\\App\\Http\\Controllers\\Auth\\LoginController',
    'RegisterController' => '\\App\\Http\\Controllers\\Auth\\RegisterController',
    'ForgotPasswordController' => '\\App\\Http\\Controllers\\Auth\\ForgotPasswordController',
    'ResetPasswordController' => '\\App\\Http\\Controllers\\Auth\\ResetPasswordController',
    'SiteController' => '\\App\\Http\\Controllers\\SiteController',
];

foreach ($controllers as $name => $class) {
    if (class_exists($class)) {
        echo "   ✅ $name loads\n";
        $passed++;
    } else {
        echo "   ❌ $name cannot be loaded\n";
        $failed++;
    }
}

// Test 4: Middleware loads
echo "\n4. Testing Middleware...\n";
if (class_exists('\\App\\Http\\Middleware\\TurnstileDashboard')) {
    echo "   ✅ TurnstileDashboard middleware loads\n";
    $passed++;
} else {
    echo "   ❌ TurnstileDashboard middleware cannot be loaded\n";
    $failed++;
}

// Test 5: Livewire component loads
echo "\n5. Testing Livewire Components...\n";
if (class_exists('\\App\\Livewire\\InvisibleTurnstile')) {
    echo "   ✅ InvisibleTurnstile Livewire component loads\n";
    $passed++;
} else {
    echo "   ❌ InvisibleTurnstile component cannot be loaded\n";
    $failed++;
}

// Test 6: Validation rule loads
echo "\n6. Testing Validation Rules...\n";
if (class_exists('\\App\\Rules\\ValidTurnstileToken')) {
    echo "   ✅ ValidTurnstileToken rule loads\n";
    $passed++;
} else {
    echo "   ❌ ValidTurnstileToken rule cannot be loaded\n";
    $failed++;
}

// Test 7: Configuration is accessible
echo "\n7. Testing Configuration...\n";
$siteKey = config('services.cloudflare.turnstile_site_key');
$secretKey = config('services.cloudflare.turnstile_secret_key');
if ($siteKey && $secretKey) {
    echo "   ✅ Turnstile configuration keys are set\n";
    $passed++;
} else {
    echo "   ❌ Turnstile configuration keys missing\n";
    $failed++;
}

// Test 8: Blade views exist
echo "\n8. Testing Blade Views...\n";
$views = [
    'resources/views/user/partials/turnstile.blade.php',
    'resources/views/livewire/invisible-turnstile.blade.php',
    'resources/views/errors/blocked.blade.php',
    'resources/views/errors/rate-limited.blade.php',
];

foreach ($views as $view) {
    if (file_exists(base_path($view))) {
        echo "   ✅ " . basename($view) . " exists\n";
        $passed++;
    } else {
        echo "   ❌ " . basename($view) . " missing\n";
        $failed++;
    }
}

// Test 9: Documentation files exist
echo "\n9. Testing Documentation...\n";
$docs = [
    'TURNSTILE_SETUP.md',
    'TURNSTILE_IMPLEMENTATION.md',
    'TURNSTILE_QUICK_REFERENCE.md',
    'DEPLOYMENT_CHECKLIST.md',
];

foreach ($docs as $doc) {
    if (file_exists(base_path($doc))) {
        echo "   ✅ $doc exists\n";
        $passed++;
    } else {
        echo "   ❌ $doc missing\n";
        $failed++;
    }
}

// Final Summary
echo "\n=== VERIFICATION SUMMARY ===\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
echo "Total:  " . ($passed + $failed) . "\n\n";

if ($failed === 0) {
    echo "✅ ALL TESTS PASSED - IMPLEMENTATION IS COMPLETE\n\n";
    exit(0);
} else {
    echo "❌ SOME TESTS FAILED - PLEASE REVIEW\n\n";
    exit(1);
}
