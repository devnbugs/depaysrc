<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    use TurnstileValidationMixin;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ], $this->turnstileRules());
    }

    public function messages(): array
    {
        return array_merge([
            'username.required' => 'Email or username is required.',
            'password.required' => 'Password is required.',
        ], $this->turnstileMessages());
    }

    public function prepareForValidation(): void
    {
        if (app(\App\Services\TurnstileService::class)->isEnabled()) {
            $this->verifyTurnstile(5, 1);
        }
    }
}
