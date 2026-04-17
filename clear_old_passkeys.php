<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Delete all passkeys (they're domain-specific and won't work after domain migration)
$deleted = DB::table('passkeys')->delete();
echo "Deleted $deleted passkey(s) from the old domain.\n";
echo "Users will need to re-register passkeys on the new domain (depay.name.ng).\n";
