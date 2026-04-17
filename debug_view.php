<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

// Simulate a request
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

try {
    echo "=== View Resolution Debug ===" . PHP_EOL . PHP_EOL;
    
    // Test 1: Check activeTemplate function
    echo "1. Testing activeTemplate():" . PHP_EOL;
    $template = activeTemplate();
    echo "   Result: '" . $template . "'" . PHP_EOL;
    echo "   Type: " . gettype($template) . PHP_EOL;
    
    // Test 2: Check view paths
    echo "\n2. View paths from config:" . PHP_EOL;
    $paths = config('view.paths');
    foreach ($paths as $path) {
        echo "   - " . $path . PHP_EOL;
    }
    
    // Test 3: Try to find the actual file
    echo "\n3. Looking for login view file:" . PHP_EOL;
    $viewName = 'user.user.auth.login';
    $relativePath = str_replace('.', '/', $viewName) . '.blade.php';
    echo "   View name: '" . $viewName . "'" . PHP_EOL;
    echo "   Relative path: '" . $relativePath . "'" . PHP_EOL;
    
    foreach ($paths as $path) {
        $fullPath = $path . '/' . $relativePath;
        $exists = file_exists($fullPath);
        echo "   - Full path: " . $fullPath . PHP_EOL;
        echo "     Exists: " . ($exists ? 'YES' : 'NO') . PHP_EOL;
    }
    
    // Test 4: Try to use Laravel's view function
    echo "\n4. Testing Laravel View facade:" . PHP_EOL;
    try {
        $view = view('user.user.auth.login', ['pageTitle' => 'Login']);
        echo "   View created successfully" . PHP_EOL;
        echo "   View path: " . $view->getPath() . PHP_EOL;
    } catch (Exception $e) {
        echo "   ERROR: " . $e->getMessage() . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
