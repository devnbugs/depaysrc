<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\GeneralSetting;
use App\Models\EmailLog;
use Carbon\Carbon;

class TestEmailVerificationFlow extends Command
{
    protected $signature = 'test:email-verification-flow';
    protected $description = 'Test the complete email verification flow for signup';

    public function handle()
    {
        $this->line("\n========================================");
        $this->line("EMAIL VERIFICATION FLOW TEST");
        $this->line("========================================\n");

        // Create a test user (or use existing one)
        $this->line("<info>1. CREATE TEST USER</info>");
        $this->line("   ------------------");

        $testEmail = 'test_' . time() . '@example.com';
        
        $user = new User();
        $user->firstname = 'Test';
        $user->lastname = 'User';
        $user->email = $testEmail;
        $user->username = 'testuser_' . time();
        $user->password = bcrypt('Test@1234');
        $user->mobile = '2348123456789';
        $user->status = 1;
        $user->ev = 0;  // Email not verified
        $user->sv = 1;  // SMS verified
        $user->ts = 0;
        $user->tv = 1;
        $user->save();

        $this->line("   ✓ Test user created");
        $this->line("   Email:    $testEmail");
        $this->line("   Username: " . $user->username);
        $this->line("   Verified: " . ($user->ev ? "YES" : "NO"));
        $this->line("");

        // Simulate authorization form (like in RegisterController -> registered)
        $this->line("<info>2. GENERATE VERIFICATION CODE</info>");
        $this->line("   ----------------------------");

        $general = GeneralSetting::first();
        
        $user->ver_code = verificationCode(6);
        $user->ver_code_send_at = Carbon::now();
        $user->save();

        $this->line("   ✓ Verification code generated");
        $this->line("   Code:         " . $user->ver_code);
        $this->line("   Generated At: " . $user->ver_code_send_at);
        $this->line("");

        // Test sendEmail
        $this->line("<info>3. SEND VERIFICATION EMAIL</info>");
        $this->line("   -------------------------");

        $this->line("   Testing sendEmail() function...");
        
        sendEmail($user, 'EVER_CODE', [
            'code' => $user->ver_code
        ]);

        $this->line("   ✓ sendEmail() called");
        $this->line("");

        // Check email logs
        $this->line("<info>4. VERIFY EMAIL LOG</info>");
        $this->line("   ------------------");

        $emailLog = EmailLog::where('email_to', $testEmail)
            ->orderBy('id', 'desc')
            ->first();

        if ($emailLog) {
            $this->line("   ✓ Email logged to database");
            $this->line("   To:      " . $emailLog->email_to);
            $this->line("   Subject: " . $emailLog->subject);
            $this->line("   Status:  " . ($emailLog->message ? "Content Present" : "No Content"));
            
            // Check if code is in the email
            if (strpos($emailLog->message, $user->ver_code) !== false) {
                $this->line("   ✓ Verification code IS in email");
            } else {
                $this->warn("   ✗ Verification code NOT in email");
            }
        } else {
            $this->error("   ✗ No email log found");
        }

        $this->line("");

        // Summary
        $this->line("<info>5. SUMMARY</info>");
        $this->line("   -------");
        
        $general = GeneralSetting::first();
        $this->line("   Email Enabled:      " . ($general->en ? "✓ YES" : "✗ NO"));
        $this->line("   Mail Config:        " . ($general->mail_config ? $general->mail_config->name : "NONE"));
        $this->line("   Test User Created:  ✓ YES");
        $this->line("   Code Generated:     ✓ YES");
        $this->line("   Email Sent:         " . ($emailLog ? "✓ YES" : "✗ NO"));
        
        $this->line("");
        $this->line("========================================");
        $this->info("✓ TEST COMPLETE!");
        $this->line("========================================\n");

        if ($emailLog) {
            $this->line("<info>NEXT STEPS:</info>");
            $this->line("1. Check email logs in admin panel");
            $this->line("2. If emails aren't being delivered:");
            $this->line("   - Check SMTP credentials in General Settings");
            $this->line("   - Check spam/junk folder");
            $this->line("   - Check mail server logs");
            $this->line("3. Test with a real signup in the frontend\n");
        }

        return 0;
    }
}
