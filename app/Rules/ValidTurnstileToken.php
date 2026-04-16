<?php

namespace App\Rules;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTurnstileToken implements ValidationRule
{
    protected TurnstileService $turnstileService;
    protected ?string $ip;
    protected int $maxAttempts = 5;
    protected int $decayMinutes = 1;

    public function __construct(
        ?string $ip = null,
        int $maxAttempts = 5,
        int $decayMinutes = 1
    ) {
        $this->turnstileService = app(TurnstileService::class);
        $this->ip = $ip;
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $decayMinutes;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip if Turnstile is not enabled
        if (!$this->turnstileService->isEnabled()) {
            return;
        }

        // Check rate limiting first
        if (!$this->turnstileService->checkRateLimit(
            $this->ip ?? request()->ip(),
            'validation_rule',
            $this->maxAttempts,
            $this->decayMinutes
        )) {
            $fail('Too many verification attempts. Please try again later.');
            return;
        }

        // Verify token
        if (!$this->turnstileService->verify($value, $this->ip)) {
            $fail('Security verification failed. Please try again.');
        }
    }
}
