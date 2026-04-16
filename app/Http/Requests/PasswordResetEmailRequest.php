<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetEmailRequest extends FormRequest
{
    use TurnstileValidationMixin;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge([
            'type' => ['required', 'in:email,username'],
            'value' => ['required', 'string', 'max:255'],
        ], $this->turnstileRules());
    }

    public function messages(): array
    {
        return array_merge([
            'type.required' => 'Please select an option.',
            'value.required' => 'Please provide your email or username.',
        ], $this->turnstileMessages());
    }

    public function prepareForValidation(): void
    {
        if (app(\App\Services\TurnstileService::class)->isEnabled()) {
            $this->verifyTurnstile(5, 1);
        }
    }
}
