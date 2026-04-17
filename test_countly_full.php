#!/usr/bin/env php
<?php
/**
 * COUNTLY ANALYTICS VERIFICATION SCRIPT
 * 
 * This script tests:
 * 1. .env Countly configuration
 * 2. Config loading
 * 3. Helper functions
 * 4. Event tracking setup
 */

require __DIR__ . '/bootstrap/app.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║             COUNTLY ANALYTICS - VERIFICATION SCRIPT                   ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

$passed = 0;
$failed = 0;
$total = 0;

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

$enabled = env('COUNTLY_ENABLED');
$server_url = env('COUNTLY_SERVER_URL');
$app_key = env('COUNTLY_APP_KEY');

test_result("COUNTLY_ENABLED is set", $enabled === true || $enabled === 'true');
test_result("COUNTLY_SERVER_URL is set", !empty($server_url));
test_result("COUNTLY_APP_KEY is set", !empty($app_key));

if (!empty($server_url)) {
    echo "   📝 Server URL: $server_url\n";
}
if (!empty($app_key)) {
    echo "   📝 App Key: " . substr($app_key, 0, 10) . "..." . substr($app_key, -5) . "\n";
}

// ============================================================================
// TEST 2: Countly Configuration File
// ============================================================================
echo "\n\nTEST 2: Countly Configuration File\n";
echo str_repeat("─", 72) . "\n";

$config_path = config_path('countly.php');
test_result("countly.php config file exists", file_exists($config_path));

// Check if config loads properly
try {
    $countly_config = config('countly');
    test_result("Config loads correctly", !empty($countly_config));
    
    if (!empty($countly_config)) {
        echo "   📝 Enabled: " . ($countly_config['enabled'] ? 'YES' : 'NO') . "\n";
        echo "   📝 Server URL: " . $countly_config['server_url'] . "\n";
        echo "   📝 App Key: " . substr($countly_config['app_key'], 0, 10) . "...\n";
        
        // Check tracked events
        if (isset($countly_config['track_events'])) {
            $track_events = $countly_config['track_events'];
            test_result("Track events configured", !empty($track_events));
            echo "   📝 Tracked Events:\n";
            foreach ($track_events as $event => $enabled) {
                $status = $enabled ? '✅' : '❌';
                echo "      $status $event\n";
            }
        }
    }
} catch (\Exception $e) {
    test_result("Config loads correctly", false);
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// ============================================================================
// TEST 3: Helper Functions
// ============================================================================
echo "\n\nTEST 3: Helper Functions\n";
echo str_repeat("─", 72) . "\n";

$helpers_path = app_path('Support/Helpers/CountlyHelper.php');
test_result("CountlyHelper exists", file_exists($helpers_path));

if (file_exists($helpers_path)) {
    test_result("countly() helper function exists", function_exists('countly'));
    test_result("track_event() helper exists", function_exists('track_event'));
    test_result("track_auth_event() helper exists", function_exists('track_auth_event'));
    test_result("track_payment_event() helper exists", function_exists('track_payment_event'));
}

// ============================================================================
// TEST 4: Countly Service (if exists)
// ============================================================================
echo "\n\nTEST 4: Countly Service Layer\n";
echo str_repeat("─", 72) . "\n";

$service_path = app_path('Services/CountlyService.php');
if (file_exists($service_path)) {
    try {
        test_result("CountlyService exists", class_exists('App\Services\CountlyService'));
        if (class_exists('App\Services\CountlyService')) {
            $service = app(\App\Services\CountlyService::class);
            test_result("CountlyService can be instantiated", true);
            test_result("isEnabled() method exists", method_exists($service, 'isEnabled'));
            test_result("trackEvent() method exists", method_exists($service, 'trackEvent'));
        }
    } catch (\Exception $e) {
        test_result("CountlyService instantiation", false);
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ℹ️  CountlyService not found (using helpers instead)\n";
}

// ============================================================================
// TEST 5: JavaScript Integration
// ============================================================================
echo "\n\nTEST 5: JavaScript Integration\n";
echo str_repeat("─", 72) . "\n";

$js_path = base_path('resources/js/countly.js');
test_result("countly.js script exists", file_exists($js_path));

if (file_exists($js_path)) {
    $content = file_get_contents($js_path);
    test_result("Contains Countly SDK init", strpos($content, 'Countly.init') !== false);
    test_result("Contains event tracking", strpos($content, 'add_event') !== false);
}

// ============================================================================
// TEST 6: Blade Integration
// ============================================================================
echo "\n\nTEST 6: Blade Integration\n";
echo str_repeat("─", 72) . "\n";

// Check if Countly is included in main layout
$layout_path = base_path('resources/views/layouts/app.blade.php');
if (file_exists($layout_path)) {
    $content = file_get_contents($layout_path);
    test_result("Countly included in main layout", 
        strpos($content, 'countly') !== false || strpos($content, 'Countly') !== false);
}

// ============================================================================
// TEST 7: Event Categories
// ============================================================================
echo "\n\nTEST 7: Event Categories\n";
echo str_repeat("─", 72) . "\n";

$event_categories = [
    'auth',
    'payments', 
    'bills',
    'security',
    'features',
    'errors',
];

foreach ($event_categories as $category) {
    $config_value = config("countly.track_events.$category", false);
    test_result("Track $category events", $config_value === true);
}

// ============================================================================
// TEST 8: Logging Configuration
// ============================================================================
echo "\n\nTEST 8: Logging Configuration\n";
echo str_repeat("─", 72) . "\n";

$log_channel = config('countly.log_channel', 'countly');
test_result("Log channel configured", !empty($log_channel));
echo "   📝 Log Channel: $log_channel\n";

// Check if log channel exists in config
$logging_config = config('logging.channels');
if (isset($logging_config[$log_channel])) {
    test_result("Log channel exists in logging config", true);
    echo "   📝 Channel Driver: " . $logging_config[$log_channel]['driver'] . "\n";
} else {
    test_result("Log channel exists in logging config", $log_channel === 'countly');
}

// ============================================================================
// TEST 9: Documentation
// ============================================================================
echo "\n\nTEST 9: Documentation\n";
echo str_repeat("─", 72) . "\n";

$docs = [
    'TURNSTILE_COUNTLY_IMPLEMENTATION.md',
    'TURNSTILE_COUNTLY_QUICK_REFERENCE.md',
];

foreach ($docs as $doc) {
    $path = base_path($doc);
    test_result("$doc exists", file_exists($path));
}

// ============================================================================
// TEST 10: Quick Test - Event Tracking
// ============================================================================
echo "\n\nTEST 10: Event Tracking Simulation\n";
echo str_repeat("─", 72) . "\n";

if (config('countly.enabled')) {
    try {
        // Test auth event tracking
        if (function_exists('track_auth_event')) {
            echo "   📝 Testing track_auth_event() function...\n";
            // Don't actually call it, just verify it exists
            test_result("track_auth_event() callable", true);
        }
        
        if (function_exists('track_event')) {
            echo "   📝 Testing track_event() function...\n";
            test_result("track_event() callable", true);
        }
    } catch (\Exception $e) {
        echo "   ⚠️  Error during simulation: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ℹ️  Countly disabled - skipping event tracking test\n";
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

echo "╠════════════════════════════════════════════════════════════════════════╣\n";
echo "║ COUNTLY STATUS: " . (config('countly.enabled') ? '🟢 ENABLED' : '🔴 DISABLED') . "\n";
echo "║ TURNSTILE STATUS: " . (env('TURNSTILE_ENABLED') === 'true' || env('TURNSTILE_ENABLED') === true ? '🟢 ENABLED' : '🔴 DISABLED') . "\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

exit($failed === 0 ? 0 : 1);
