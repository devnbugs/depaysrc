<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class TurnstileService
{
    private const TURNSTILE_VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    private const REQUEST_TIMEOUT = 10;

    /**
     * Verify Turnstile response token
     */
    public function verify(string $token, ?string $ip = null): bool
    {
        try {
            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->post(self::TURNSTILE_VERIFY_URL, [
                    'secret' => config('services.cloudflare.turnstile_secret_key'),
                    'response' => $token,
                    'remoteip' => $ip ?? request()->ip(),
                ])
                ->json();

            return $response['success'] ?? false;
        } catch (\Exception $e) {
            \Log::error('Turnstile verification failed', [
                'error' => $e->getMessage(),
                'ip' => $ip ?? request()->ip(),
            ]);
            return false;
        }
    }

    /**
     * Verify Turnstile and check for spam/abuse
     */
    public function verifyWithProtection(
        string $token,
        string $action = 'default',
        ?string $ip = null,
        int $maxAttempts = 5,
        int $decayMinutes = 1
    ): bool {
        $ip = $ip ?? request()->ip();

        // Check rate limiting
        if (!$this->checkRateLimit($ip, $action, $maxAttempts, $decayMinutes)) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => trans('Too many requests. Please try again later.'),
            ]);
        }

        // Verify token
        if (!$this->verify($token, $ip)) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => trans('Security verification failed. Please try again.'),
            ]);
        }

        return true;
    }

    /**
     * Check rate limit for IP address
     */
    public function checkRateLimit(
        string $ip,
        string $action = 'default',
        int $maxAttempts = 5,
        int $decayMinutes = 1
    ): bool {
        $key = "turnstile:{$action}:{$ip}";
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return false;
        }

        RateLimiter::hit($key, $decayMinutes * 60);
        return true;
    }

    /**
     * Reset rate limit for IP
     */
    public function resetRateLimit(string $ip, string $action = 'default'): void
    {
        $key = "turnstile:{$action}:{$ip}";
        RateLimiter::clear($key);
    }

    /**
     * Get Turnstile site key
     */
    public function getSiteKey(): string
    {
        return config('services.cloudflare.turnstile_site_key', '');
    }

    /**
     * Check if Turnstile is enabled
     */
    public function isEnabled(): bool
    {
        return !empty($this->getSiteKey()) && 
               !empty(config('services.cloudflare.turnstile_secret_key'));
    }

    /**
     * Get Turnstile theme preference (auto, light, dark)
     */
    public function getTheme(): string
    {
        return config('services.cloudflare.turnstile_theme', 'auto');
    }

    /**
     * Log security event
     */
    public function logSecurityEvent(string $event, array $data = []): void
    {
        \Log::info("Turnstile Security Event: {$event}", [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ]);
    }

    /**
     * Track suspension and block abuse
     */
    public function trackSuspiciousActivity(string $ip, string $reason, int $banMinutes = 15): void
    {
        $key = "suspicious:{$ip}";
        
        $count = \Cache::increment($key);
        if ($count === 1) {
            \Cache::put($key, $count, now()->addMinutes($banMinutes));
        }

        if ($count >= 3) {
            // Block the IP
            $this->blockIP($ip, $reason, $banMinutes);
        }

        $this->logSecurityEvent('Suspicious Activity Detected', [
            'reason' => $reason,
            'attempt_count' => $count,
            'ip' => $ip,
        ]);
    }

    /**
     * Block IP address
     */
    public function blockIP(string $ip, string $reason = '', int $minutes = 60): void
    {
        \Cache::put("blocked_ip:{$ip}", [
            'reason' => $reason,
            'blocked_at' => now(),
        ], now()->addMinutes($minutes));

        $this->logSecurityEvent('IP Blocked', [
            'ip' => $ip,
            'reason' => $reason,
            'duration_minutes' => $minutes,
        ]);
    }

    /**
     * Check if IP is blocked
     */
    public function isIPBlocked(string $ip): bool
    {
        return \Cache::has("blocked_ip:{$ip}");
    }

    /**
     * Get blocked IP reason
     */
    public function getBlockReason(string $ip): ?string
    {
        $blocked = \Cache::get("blocked_ip:{$ip}");
        return $blocked['reason'] ?? null;
    }
}
