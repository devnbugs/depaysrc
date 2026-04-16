<?php

namespace App\Http\Controllers;

use App\Http\Requests\TurnstileFormRequests;
use App\Services\TurnstileService;
use Illuminate\Http\Request;

/**
 * Example Controller Implementation with Turnstile
 * 
 * This file demonstrates how to use Turnstile in your controllers
 * It's provided as reference - adapt to your actual controllers
 */

class ExampleTurnstileController extends Controller
{
    protected TurnstileService $turnstileService;

    public function __construct(TurnstileService $turnstileService)
    {
        $this->turnstileService = $turnstileService;
    }

    /**
     * Example 1: Login with automatic request validation
     * 
     * This uses the LoginRequest which automatically verifies Turnstile
     * No additional code needed - validation is handled in the request class
     */
    public function loginExample(TurnstileFormRequests\LoginRequest $request)
    {
        // At this point, Turnstile has been verified automatically
        // Just process the login
        
        return response()->json(['message' => 'Login successful']);
    }

    /**
     * Example 2: Manual Turnstile verification in controller
     */
    public function manualVerificationExample(Request $request)
    {
        // Validate other fields
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'action' => ['required', 'string'],
        ]);

        // Manually verify Turnstile
        try {
            $this->turnstileService->verifyWithProtection(
                $request->input('cf-turnstile-response'),
                $validated['action'],  // action name for rate limiting
                $request->ip(),
                5,    // max attempts
                1     // decay minutes
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Security verification failed'], 422);
        }

        // Process the request
        return response()->json(['message' => 'Action completed']);
    }

    /**
     * Example 3: Payment processing with strict protection
     */
    public function processPaymentExample(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
            'recipient' => ['required', 'email'],
            'cf-turnstile-response' => ['required', 'string'],
        ]);

        $ip = $request->ip();

        // Step 1: Check if IP is blocked
        if ($this->turnstileService->isIPBlocked($ip)) {
            return response()->json([
                'error' => 'Access denied',
                'reason' => $this->turnstileService->getBlockReason($ip),
            ], 403);
        }

        // Step 2: Verify with strict rate limiting (3 attempts per 15 minutes)
        try {
            $this->turnstileService->verifyWithProtection(
                $validated['cf-turnstile-response'],
                'payment',
                $ip,
                3,    // max 3 attempts
                15    // per 15 minutes
            );
        } catch (\Exception $e) {
            // Log security event
            $this->turnstileService->logSecurityEvent('Payment Verification Failed', [
                'user_id' => auth()->id(),
                'amount' => $validated['amount'],
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);

            // Track suspicious activity
            $this->turnstileService->trackSuspiciousActivity(
                $ip,
                'Payment verification failed for ' . $validated['recipient']
            );

            return response()->json(['error' => 'Payment verification failed'], 422);
        }

        // Step 3: Process payment
        // ... your payment logic here ...

        // Log successful transaction
        $this->turnstileService->logSecurityEvent('Payment Processed', [
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'recipient' => $validated['recipient'],
            'ip' => $ip,
        ]);

        return response()->json(['message' => 'Payment processed successfully']);
    }

    /**
     * Example 4: Bulk operation with multiple request protection
     */
    public function bulkTransferExample(Request $request)
    {
        $validated = $request->validate([
            'transfers' => ['required', 'array', 'max:10'],
            'transfers.*.recipient' => ['required', 'email'],
            'transfers.*.amount' => ['required', 'numeric', 'min:100'],
            'cf-turnstile-response' => ['required', 'string'],
        ]);

        $ip = $request->ip();

        // Detect if user is making multiple bulk requests
        if (!$this->turnstileService->checkRateLimit($ip, 'bulk_transfer', 2, 60)) {
            $this->turnstileService->trackSuspiciousActivity(
                $ip,
                'Bulk transfer rate limit exceeded'
            );

            return response()->json([
                'error' => 'Too many bulk transfers. Please wait before trying again.',
            ], 429);
        }

        // Verify Turnstile
        try {
            $this->turnstileService->verify(
                $validated['cf-turnstile-response'],
                $ip
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Verification failed'], 422);
        }

        // Process transfers
        $totalAmount = array_sum(array_column($validated['transfers'], 'amount'));

        $this->turnstileService->logSecurityEvent('Bulk Transfer Initiated', [
            'user_id' => auth()->id(),
            'transfer_count' => count($validated['transfers']),
            'total_amount' => $totalAmount,
            'ip' => $ip,
        ]);

        // ... process bulk transfer ...

        return response()->json([
            'message' => 'Bulk transfer processed',
            'count' => count($validated['transfers']),
            'total' => $totalAmount,
        ]);
    }

    /**
     * Example 5: Using Turnstile validation rule
     */
    public function withValidationRuleExample(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'cf-turnstile-response' => [
                'required',
                new \App\Rules\ValidTurnstileToken(
                    $request->ip(),
                    5,   // max attempts
                    1    // decay minutes
                ),
            ],
        ]);

        return response()->json(['message' => 'Verified']);
    }

    /**
     * Example 6: Admin blocking an IP
     */
    public function blockIPExample(Request $request)
    {
        $ip = $request->input('ip');
        $reason = $request->input('reason', 'Admin block');

        $this->turnstileService->blockIP($ip, $reason, 1440); // 24 hours

        return response()->json([
            'message' => "IP {$ip} has been blocked",
            'reason' => $reason,
            'duration' => '24 hours',
        ]);
    }

    /**
     * Example 7: Get security status for monitoring
     */
    public function getSecurityStatusExample(Request $request)
    {
        $ip = $request->ip();

        return response()->json([
            'ip' => $ip,
            'is_blocked' => $this->turnstileService->isIPBlocked($ip),
            'block_reason' => $this->turnstileService->getBlockReason($ip),
            'is_enabled' => $this->turnstileService->isEnabled(),
            'site_key' => $this->turnstileService->getSiteKey(),
        ]);
    }
}

/**
 * QUICK REFERENCE FOR YOUR CONTROLLERS
 * =====================================
 * 
 * 1. LOGIN/REGISTRATION:
 *    Use: LoginRequest, RegisterRequest (auto validates Turnstile)
 * 
 * 2. PAYMENT/TRANSFER:
 *    Use: $turnstileService->verifyWithProtection() with strict limits
 * 
 * 3. CONTACT FORM:
 *    Use: ContactFormRequest (auto validates Turnstile)
 * 
 * 4. PASSWORD RESET:
 *    Use: PasswordResetEmailRequest, PasswordUpdateRequest
 * 
 * 5. CUSTOM ACTIONS:
 *    Use: $turnstileService->verify() or validation rule
 * 
 * 6. MONITORING:
 *    Use: logSecurityEvent(), trackSuspiciousActivity()
 */
