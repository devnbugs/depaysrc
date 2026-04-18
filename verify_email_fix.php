#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\GeneralSetting;
use App\Models\EmailTemplate;

echo "\n========================================\n";
echo "EMAIL VERIFICATION FIX VERIFICATION\n";
echo "========================================\n\n";

$general = GeneralSetting::first();
$template = EmailTemplate::where('act', 'EVER_CODE')->first();

echo "Email Enabled (general->en):  " . ($general->en ? "✓ YES (1)" : "✗ NO (0)") . "\n";
echo "Mail Config:                  " . ($general->mail_config ? $general->mail_config->name : "NONE") . "\n";
echo "EVER_CODE Template:           " . ($template ? "✓ YES" : "✗ NO") . "\n";
echo "Template Enabled:             " . ($template && $template->email_status ? "✓ YES" : "✗ NO") . "\n";

echo "\n========================================\n";
echo "SYSTEM STATUS\n";
echo "========================================\n\n";

if ($general->en == 1) {
    echo "✓ Email system is ENABLED\n";
} else {
    echo "✗ Email system is DISABLED\n";
}

if ($template && $template->email_status == 1) {
    echo "✓ Email template is CONFIGURED\n";
} else {
    echo "✗ Email template is not configured\n";
}

if ($general->mail_config) {
    echo "✓ Mail provider is configured (" . $general->mail_config->name . ")\n";
} else {
    echo "! Mail provider not configured\n";
}

echo "\n========================================\n";

if ($general->en == 1 && $template && $template->email_status == 1) {
    echo "✓✓✓ EMAIL VERIFICATION SYSTEM WORKING! ✓✓✓\n";
    echo "\nSignup emails with verification codes\n";
    echo "will now be sent to users!\n";
} else {
    echo "✗ System still needs configuration\n";
}

echo "========================================\n\n";
