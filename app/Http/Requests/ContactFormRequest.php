<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        if (app(\App\Services\TurnstileService::class)->isEnabled()) {
            $this->verifyTurnstile(3, 5);
        }
    }
}
