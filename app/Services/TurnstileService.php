<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileService
{
    public function isEnabled(): bool
    {
        if (!config('turnstile.enabled')) {
            return false;
        }

        return (bool) $this->getSiteKey() && (bool) $this->getSecretKey();
    }

    public function getSiteKey(): string
    {
        return (string) (config('turnstile.site_key') ?? '');
    }

    public function getSecretKey(): string
    {
        return (string) (config('turnstile.secret_key') ?? '');
    }

    public function verify(string $token, ?string $ip = null): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }

        $token = trim($token);
        if ($token === '') {
            return false;
        }

        try {
            $payload = [
                'secret' => $this->getSecretKey(),
                'response' => $token,
            ];

            if ($ip) {
                $payload['remoteip'] = $ip;
            }

            $response = Http::asForm()
                ->timeout(10)
                ->post((string) config('turnstile.verify_url'), $payload);

            if (!$response->ok()) {
                $this->logSecurityEvent('Turnstile Verification HTTP Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'ip' => $ip,
                ]);

                return false;
            }

            $data = $response->json() ?? [];
            $success = (bool) ($data['success'] ?? false);

            if (!$success) {
                $this->logSecurityEvent('Turnstile Verification Failed', [
                    'ip' => $ip,
                    'error_codes' => $data['error-codes'] ?? [],
                ]);
            }

            return $success;
        } catch (\Throwable $e) {
            Log::warning('Turnstile verification exception', [
                'message' => $e->getMessage(),
                'ip' => $ip,
            ]);

            return false;
        }
    }

    public function verifyWithProtection(
        string $token,
        string $action,
        ?string $ip = null,
        int $maxAttempts = 5,
        int $decayMinutes = 1
    ): bool {
        $ip = $ip ?: request()->ip();

        if (!$this->checkRateLimit($ip, $action, $maxAttempts, $decayMinutes)) {
            $this->trackSuspiciousActivity($ip, "Rate limit exceeded for action: {$action}");
            return false;
        }

        $verified = $this->verify($token, $ip);
        if (!$verified) {
            $this->trackSuspiciousActivity($ip, "Turnstile token verification failed for action: {$action}");
        }

        return $verified;
    }

    public function checkRateLimit(
        string $ip,
        string $action,
        int $maxAttempts = null,
        int $decayMinutes = null
    ): bool {
        $maxAttempts = $maxAttempts ?? (int) config('turnstile.rate_limit.default_max_attempts', 5);
        $decayMinutes = $decayMinutes ?? (int) config('turnstile.rate_limit.default_decay_minutes', 1);

        $key = "turnstile:rate:{$action}:{$ip}";

        $count = Cache::increment($key);
        if ($count === 1) {
            Cache::put($key, $count, now()->addMinutes($decayMinutes));
        }

        return $count <= $maxAttempts;
    }

    public function trackSuspiciousActivity(string $ip, string $reason): void
    {
        $key = "turnstile:suspicious:{$ip}";
        $count = Cache::increment($key);

        if ($count === 1) {
            Cache::put($key, $count, now()->addMinutes(30));
        }

        $this->logSecurityEvent('Suspicious Activity', [
            'ip' => $ip,
            'reason' => $reason,
            'count' => $count,
        ]);

        $threshold = (int) config('turnstile.blocking.suspicious_threshold', 10);
        if ($count >= $threshold) {
            $this->blockIP($ip, $reason);
        }
    }

    public function isIPBlocked(string $ip): bool
    {
        return Cache::has($this->blockedKey($ip));
    }

    public function getBlockReason(string $ip): string
    {
        return (string) Cache::get($this->blockedKey($ip), 'Access temporarily blocked.');
    }

    public function blockIP(string $ip, string $reason): void
    {
        $minutes = (int) config('turnstile.blocking.block_minutes', 60);
        Cache::put($this->blockedKey($ip), $reason, now()->addMinutes($minutes));

        $this->logSecurityEvent('IP Blocked', [
            'ip' => $ip,
            'reason' => $reason,
            'minutes' => $minutes,
        ]);
    }

    public function logSecurityEvent(string $event, array $context = []): void
    {
        Log::info("Turnstile: {$event}", $context);
    }

    protected function blockedKey(string $ip): string
    {
        return "turnstile:blocked:{$ip}";
    }
}

