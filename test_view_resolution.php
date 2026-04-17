<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "activeTemplate(): " . activeTemplate() . PHP_EOL;
echo "View paths:" . PHP_EOL;
$paths = config('view.paths');
foreach ($paths as $path) {
    echo "  - " . $path . PHP_EOL;
}

echo "\nView file resolution for 'user.user.auth.login':" . PHP_EOL;
foreach ($paths as $path) {
    $file = $path . '/' . str_replace('.', '/', 'user.user.auth.login') . '.blade.php';
    echo "  - " . $file . " (exists: " . (file_exists($file) ? "YES" : "NO") . ")" . PHP_EOL;
}
