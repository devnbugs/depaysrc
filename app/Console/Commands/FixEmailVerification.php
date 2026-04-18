<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GeneralSetting;
use App\Models\EmailTemplate;

class FixEmailVerification extends Command
{
    protected $signature = 'fix:email-verification';
    protected $description = 'Fix signup email verification code not sending issue';

    public function handle()
    {
        $this->line("\n========================================");
        $this->line("EMAIL VERIFICATION CODE DIAGNOSTIC TEST");
        $this->line("========================================\n");

        // Check 1: Email Enabled Status
        $this->line("<info>1. EMAIL ENABLED STATUS</info>");
        $this->line("   ----------------------");
        
        $general = GeneralSetting::first();
        if (!$general) {
            $this->error("   ERROR: No general settings found!");
            return 1;
        }

        $this->line("   Email Enabled (en):    " . ($general->en ? "✓ YES" : "✗ NO - DISABLED"));
        $this->line("   SMS Required (sv):     " . ($general->sv ? "ENABLED" : "DISABLED"));
        $this->line("   Mail Config:           " . ($general->mail_config ? $general->mail_config->name : "NONE"));
        $this->line("");

        // Check 2: Email Template for EVER_CODE
        $this->line("<info>2. EMAIL TEMPLATE CHECK</info>");
        $this->line("   ----------------------");
        
        $template = EmailTemplate::where('act', 'EVER_CODE')->first();
        if (!$template) {
            $this->error("   ERROR: EVER_CODE template not found!");
        } else {
            $this->line("   Template Found:        ✓ YES");
            $this->line("   Subject:               " . $template->subj);
            $this->line("   Status:                " . ($template->email_status ? "ENABLED" : "DISABLED"));
            $this->line("   Body Length:           " . strlen($template->email_body) . " chars\n");
        }

        // Check 3: The Problem
        $this->line("<info>3. ROOT CAUSE ANALYSIS</info>");
        $this->line("   --------------------");

        if ($general->en != 1) {
            $this->warn("   PROBLEM FOUND: Email is DISABLED!");
            $this->warn("   The sendEmail() function blocks all emails when en != 1");
        } else {
            $this->line("   ✓ Email is enabled");
        }

        if (!$template || !$template->email_status) {
            $this->warn("   PROBLEM FOUND: Email template is disabled or missing!");
        } else {
            $this->line("   ✓ EVER_CODE template exists and is enabled");
        }

        $this->line("");

        // Check 4: Apply fixes
        $this->line("<info>4. APPLYING FIXES</info>");
        $this->line("   ----------------");

        $fixed = false;

        // Enable email
        if ($general->en != 1) {
            $general->en = 1;
            $general->save();
            $this->line("   ✓ Email enabled (en = 1)");
            $fixed = true;
        }

        // Enable or create EVER_CODE template
        if ($template && !$template->email_status) {
            $template->email_status = 1;
            $template->save();
            $this->line("   ✓ EVER_CODE template enabled");
            $fixed = true;
        } elseif (!$template) {
            $this->line("   ! EVER_CODE template not found - creating...");
            $newTemplate = new EmailTemplate();
            $newTemplate->act = 'EVER_CODE';
            $newTemplate->subj = 'Email Verification Code';
            $newTemplate->email_body = 'Your email verification code is: {{code}}';
            $newTemplate->email_status = 1;
            $newTemplate->save();
            $this->line("   ✓ EVER_CODE template created and enabled");
            $fixed = true;
        }

        $this->line("");
        $this->line("========================================");
        if ($fixed) {
            $this->info("✓ FIX COMPLETE - Emails should now send!");
        } else {
            $this->info("✓ All settings are already correct!");
        }
        $this->line("========================================\n");

        return 0;
    }
}
