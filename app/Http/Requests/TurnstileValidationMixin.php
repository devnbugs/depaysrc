<?php

namespace App\Http\Requests;

use App\Services\TurnstileService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * Turnstile Validation Mixin
 * 
 * Add to any FormRequest to include Turnstile validation
 * Usage in FormRequest: use TurnstileValidationMixin;
 */
trait TurnstileValidationMixin
{
    /**
     * Get Turnstile validation rules
     */
    protected function turnstileRules(): array
    {
        if (!app(TurnstileService::class)->isEnabled()) {
            return [];
        }

        return [
            'cf-turnstile-response' => ['required', 'string'],
        ];
    }

    /**
     * Get Turnstile validation messages
     */
    protected function turnstileMessages(): array
    {
        return [
            'cf-turnstile-response.required' => 'Please complete the security verification.',
            'cf-turnstile-response.string' => 'Invalid security verification response.',
        ];
    }

    /**
     * Verify Turnstile token with protection
     */
    protected function verifyTurnstile(?int $maxAttempts = 5, ?int $decayMinutes = 1): bool
    {
        $turnstileService = app(TurnstileService::class);

        try {
            $turnstileService->verifyWithProtection(
                $this->input('cf-turnstile-response'),
                $this->route()?->getName() ?? 'default',
                $this->ip(),
                $maxAttempts,
                $decayMinutes
            );

            return true;
        } catch (ValidationException $e) {
            throw $e;
        }
    }
}
