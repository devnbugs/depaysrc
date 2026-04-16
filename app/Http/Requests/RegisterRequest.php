<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    use TurnstileValidationMixin;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'mobile' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ], $this->turnstileRules());
    }

    public function messages(): array
    {
        return array_merge([
            'firstname.required' => 'First name is required.',
            'lastname.required' => 'Last name is required.',
            'username.unique' => 'This username is already taken.',
            'email.unique' => 'This email is already registered.',
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
