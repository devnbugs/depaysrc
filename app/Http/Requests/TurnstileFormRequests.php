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

/**
 * Base Form Request with Turnstile Support
 */
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
        if (app(TurnstileService::class)->isEnabled()) {
            $this->verifyTurnstile(5, 1);
        }
    }
}

/**
 * Registration Form Request
 */
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
        if (app(TurnstileService::class)->isEnabled()) {
            $this->verifyTurnstile(5, 1);
        }
    }
}

/**
 * Password Reset Email Request
 */
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
        if (app(TurnstileService::class)->isEnabled()) {
            $this->verifyTurnstile(5, 1);
        }
    }
}

/**
 * Password Update Request
 */
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
        if (app(TurnstileService::class)->isEnabled()) {
            $this->verifyTurnstile(5, 1);
        }
    }
}

/**
 * Contact Form Request
 */
class ContactFormRequest extends FormRequest
{
    use TurnstileValidationMixin;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], $this->turnstileRules());
    }

    public function messages(): array
    {
        return array_merge([
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'subject.required' => 'Subject is required.',
            'message.required' => 'Message is required.',
            'message.min' => 'Message must be at least 10 characters.',
        ], $this->turnstileMessages());
    }

    public function prepareForValidation(): void
    {
        if (app(TurnstileService::class)->isEnabled()) {
            $this->verifyTurnstile(3, 5); // Stricter limits for contact form
        }
    }
}
