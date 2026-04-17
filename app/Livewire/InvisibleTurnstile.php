<?php

namespace App\Livewire;

use App\Services\TurnstileService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Invisible Turnstile Widget for Dashboard Security
 * 
 * Provides protection for:
 * - Multiple request detection (anti-spam)
 * - Payment/Transfer security
 * - Bill payment protection
 * - Airtime/Data purchase security
 * - PIN input protection
 * - Popup modal security
 */
class InvisibleTurnstile extends Component
{
    public string $turnstileToken = '';
    public string $protectionMode = 'invisible'; // invisible, managed
    public bool $isReady = false;
    public bool $showChallenge = false;
    public array $suspiciousActivities = [];

    public function mount(): void
    {
        // Check if user has recent suspicious activities
        $this->checkSuspiciousActivities();
    }

    public function render(): View
    {
        $turnstileService = app(TurnstileService::class);
        
        return view('livewire.invisible-turnstile', [
            'siteKey' => $turnstileService->getSiteKey(),
            'isEnabled' => $turnstileService->isEnabled(),
            'isReady' => $this->isReady,
            'showChallenge' => $this->showChallenge,
        ]);
    }

    /**
     * Initialize invisible Turnstile widget
     * Called after component mounts
     */
    public function initializeWidget(): void
    {
        $this->isReady = true;
        $turnstileService = app(TurnstileService::class);
        
        // Log initialization
        $turnstileService->logSecurityEvent('Invisible Turnstile Initialized', [
            'user_id' => auth()->id(),
            'protection_mode' => $this->protectionMode,
        ]);
    }

    /**
     * Handle token from Turnstile
     * Validates and processes security token
     */
    public function handleToken(string $token): void
    {
        $turnstileService = app(TurnstileService::class);
        
        try {
            if ($turnstileService->verify($token, request()->ip())) {
                $this->turnstileToken = $token;
                $this->dispatch('turnstileTokenReceived', ['token' => $token]);
                
                $turnstileService->logSecurityEvent('Invisible Turnstile Verified', [
                    'user_id' => auth()->id(),
                    'action' => 'token_verified',
                ]);
            } else {
                $turnstileService->trackSuspiciousActivity(
                    request()->ip(),
                    'Invalid Turnstile token in invisible mode'
                );
            }
        } catch (\Exception $e) {
            \Log::error('Invisible Turnstile verification failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
        }
    }

    /**
     * Detect multiple requests from same user
     * Protects against rapid-fire requests (spam/abuse)
     */
    public function detectMultipleRequests(string $action = 'default'): bool
    {
        $turnstileService = app(TurnstileService::class);
        $userId = auth()->id();
        $ip = request()->ip();
        $key = "user_requests:{$userId}:{$action}";
        
        $count = \Cache::increment($key);
        
        if ($count === 1) {
            \Cache::put($key, $count, now()->addMinutes(1));
        }

        // Alert if more than 5 requests per minute
        if ($count > 5) {
            $turnstileService->trackSuspiciousActivity(
                $ip,
                "Multiple rapid requests detected: {$count} requests in 1 minute for action: {$action}"
            );
            
            $this->dispatch('multipleRequestsDetected', [
                'count' => $count,
                'action' => $action,
            ]);
            
            return false; // Block request
        }

        return true; // Allow request
    }

    /**
     * Check for suspicious activities
     * Blocks users with excessive failed attempts
     */
    private function checkSuspiciousActivities(): void
    {
        $turnstileService = app(TurnstileService::class);
        $ip = request()->ip();
        
        if ($turnstileService->isIPBlocked($ip)) {
            $reason = $turnstileService->getBlockReason($ip);
            $this->suspiciousActivities[] = "Your access has been temporarily blocked: {$reason}";
            
            $this->dispatch('accessBlocked', ['reason' => $reason]);
        }
    }

    /**
     * Verify action with Turnstile protection
     * Used for critical operations (payments, transfers, etc.)
     */
    public function verifyAction(string $action, string $token): bool
    {
        $turnstileService = app(TurnstileService::class);
        
        // Check rate limits (10 attempts per 5 minutes)
        if (!$turnstileService->checkRateLimit(request()->ip(), $action, 10, 5)) {
            $turnstileService->trackSuspiciousActivity(
                request()->ip(),
                "Rate limit exceeded for action: {$action}"
            );
            
            $this->dispatch('rateLimitExceeded', ['action' => $action]);
            return false;
        }

        // Verify token
        if (empty($token) || !$turnstileService->verify($token, request()->ip())) {
            $turnstileService->trackSuspiciousActivity(
                request()->ip(),
                "Turnstile verification failed for action: {$action}"
            );
            
            return false;
        }

        // Log successful verification
        $turnstileService->logSecurityEvent('Action Verified with Invisible Turnstile', [
            'user_id' => auth()->id(),
            'action' => $action,
            'ip' => request()->ip(),
        ]);

        return true;
    }

    /**
     * Request protection for sensitive operations
     * Implements multiple layers of protection
     */
    public function protectSensitiveRequest(
        string $requestType,
        array $requestData = [],
        int $maxAttempts = 3,
        int $decayMinutes = 15
    ): bool {
        $turnstileService = app(TurnstileService::class);
        $ip = request()->ip();
        $userId = auth()->id();
        
        // 1. Check if IP is blocked
        if ($turnstileService->isIPBlocked($ip)) {
            return false;
        }

        // 2. Check rate limiting
        $key = "sensitive_request:{$userId}:{$requestType}";
        if (\Cache::has($key) && \Cache::get($key) >= $maxAttempts) {
            $turnstileService->trackSuspiciousActivity(
                $ip,
                "Max attempts exceeded for sensitive request: {$requestType}"
            );
            return false;
        }

        // 3. Increment attempt counter
        $attempts = \Cache::increment($key);
        if ($attempts === 1) {
            \Cache::put($key, $attempts, now()->addMinutes($decayMinutes));
        }

        // 4. Detect multiple requests
        if (!$this->detectMultipleRequests($requestType)) {
            return false;
        }

        // 5. Log the request
        $turnstileService->logSecurityEvent('Sensitive Request Protected', [
            'user_id' => $userId,
            'request_type' => $requestType,
            'attempt' => $attempts,
            'data_fields' => array_keys($requestData),
        ]);

        return true;
    }

    /**
     * Get security status for current user
     */
    public function getSecurityStatus(): array
    {
        $turnstileService = app(TurnstileService::class);
        $userId = auth()->id();
        $ip = request()->ip();

        return [
            'user_id' => $userId,
            'ip' => $ip,
            'is_blocked' => $turnstileService->isIPBlocked($ip),
            'is_enabled' => $turnstileService->isEnabled(),
            'suspicious_activities' => $this->suspiciousActivities,
            'token' => $this->turnstileToken,
        ];
    }
}
