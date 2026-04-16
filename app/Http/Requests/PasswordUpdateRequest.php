<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
{
    use TurnstileValidationMixin;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge([
            'email' => ['required', 'email', 'exists:users,email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ], $this->turnstileRules());
    }

    public function messages(): array
    {
        return array_merge([
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ], $this->turnstileMessages());
    }

    public function prepareForValidation(): void
    {
        if (app(\App\Services\TurnstileService::class)->isEnabled()) {
            $this->verifyTurnstile(5, 1);
        }
    }
}
