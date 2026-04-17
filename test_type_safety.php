#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║  TURNSTILE SERVICE - TYPE SAFETY TEST                         ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

try {
    $service = app(\App\Services\TurnstileService::class);
    
    echo "Testing method returns...\n";
    
    $siteKey = $service->getSiteKey();
    echo "✅ getSiteKey() returns: " . gettype($siteKey) . " = " . substr($siteKey, 0, 15) . "...\n";
    
    $isEnabled = $service->isEnabled();
    echo "✅ isEnabled() returns: " . gettype($isEnabled) . " = " . ($isEnabled ? 'TRUE' : 'FALSE') . "\n";
    
    $theme = $service->getTheme();
    echo "✅ getTheme() returns: " . gettype($theme) . " = " . $theme . "\n";
    
    echo "\n╔════════════════════════════════════════════════════════════════╗\n";
    echo "║  ✅ NO TYPE ERRORS - SERVICE IS WORKING CORRECTLY             ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n\n";
    
} catch (TypeError $e) {
    echo "❌ TYPE ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
