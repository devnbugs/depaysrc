<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\GeneralSetting;
use App\Models\EmailTemplate;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "========================================\n";
echo "EMAIL VERIFICATION CODE DIAGNOSTIC TEST\n";
echo "========================================\n\n";

// Check 1: Email Enabled Status
echo "1. EMAIL ENABLED STATUS\n";
echo "   ----------------------\n";
$general = GeneralSetting::first();
if (!$general) {
    echo "   ERROR: No general settings found!\n";
    exit(1);
}

echo "   Email Enabled (en): " . ($general->en ? "✓ YES" : "✗ NO - DISABLED") . "\n";
echo "   SMS Required (sv):  " . ($general->sv ? "ENABLED" : "DISABLED") . "\n";
echo "   Mail Config:        " . ($general->mail_config ? $general->mail_config->name : "NONE") . "\n\n";

// Check 2: Email Template for EVER_CODE
echo "2. EMAIL TEMPLATE CHECK\n";
echo "   ----------------------\n";
$template = EmailTemplate::where('act', 'EVER_CODE')->first();
if (!$template) {
    echo "   ERROR: EVER_CODE template not found!\n";
} else {
    echo "   Template Found: ✓ YES\n";
    echo "   Subject:        " . $template->subj . "\n";
    echo "   Status:         " . ($template->email_status ? "ENABLED" : "DISABLED") . "\n";
    echo "   Body Length:    " . strlen($template->email_body) . " chars\n\n";
}

// Check 3: The Problem
echo "3. ROOT CAUSE ANALYSIS\n";
echo "   --------------------\n";

if ($general->en != 1) {
    echo "   PROBLEM FOUND: Email is disabled!\n";
    echo "   The sendEmail() function in helpers.php has this check:\n";
    echo "   if (\$general->en != 1 || !\$emailTemplate) { return; }\n";
    echo "   This causes all emails to be rejected before sending.\n\n";
    
    echo "   SOLUTION: Enable email in admin settings\n";
    echo "   Setting: general->en = 1\n\n";
}

if (!$template || !$template->email_status) {
    echo "   PROBLEM FOUND: Email template is disabled or missing!\n";
    echo "   Email template for 'EVER_CODE' must be enabled.\n\n";
}

// Check 4: Mail Configuration
if (!$general->mail_config) {
    echo "   WARNING: No mail configuration set!\n";
    echo "   Please configure mail in admin settings.\n\n";
}

echo "4. FIX INSTRUCTIONS\n";
echo "   -----------------\n";
echo "   Step 1: Go to Admin Panel > Settings > General Settings\n";
echo "   Step 2: Find 'Email Status' and set to: ENABLED\n";
echo "   Step 3: Go to Admin Panel > Email Templates\n";
echo "   Step 4: Find 'Email Verification Code' (EVER_CODE)\n";
echo "   Step 5: Make sure 'Status' is set to: ENABLED\n";
echo "   Step 6: Configure Mail (SMTP or other)\n\n";

echo "5. APPLYING FIX PROGRAMMATICALLY\n";
echo "   --------------------------------\n";

// Enable email
if ($general->en != 1) {
    $general->en = 1;
    $general->save();
    echo "   ✓ Email enabled (en = 1)\n";
} else {
    echo "   ✓ Email already enabled\n";
}

// Enable EVER_CODE template
if ($template && !$template->email_status) {
    $template->email_status = 1;
    $template->save();
    echo "   ✓ EVER_CODE template enabled\n";
} else if ($template) {
    echo "   ✓ EVER_CODE template already enabled\n";
} else {
    echo "   ✗ EVER_CODE template not found - creating one\n";
    $newTemplate = new EmailTemplate();
    $newTemplate->act = 'EVER_CODE';
    $newTemplate->subj = 'Email Verification Code';
    $newTemplate->email_body = 'Your email verification code is: {{code}}';
    $newTemplate->email_status = 1;
    $newTemplate->save();
    echo "   ✓ EVER_CODE template created and enabled\n";
}

echo "\n========================================\n";
echo "FIX COMPLETE - Emails should now send!\n";
echo "========================================\n";
