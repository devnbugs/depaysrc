<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Validate Payment Confirmation Request with PIN/2FA
 */
class PaymentConfirmationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = Auth::user();
        $rules = [
            'type' => ['required', 'integer', 'in:1,2,3,4'],
            'phone' => ['required', 'string', 'min:10'],
            'amount' => ['required', 'numeric', 'min:1'],
            'reference' => ['required', 'string', 'max:191'],
            'bundle_name' => ['nullable', 'string', 'max:191'],
            'payment_data' => ['nullable', 'json'],
        ];

        // Add PIN validation if enabled
        if ((int) $user->pin_state === 1) {
            $rules['pin_code'] = ['required', 'string', 'size:4'];
        } elseif ((int) $user->two_factor_enabled === 1) {
            $rules['authenticator_code'] = ['required', 'string', 'size:6'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'pin_code.size' => 'PIN must be exactly 4 digits.',
            'authenticator_code.size' => '2FA code must be exactly 6 digits.',
            'type.in' => 'Invalid payment type selected.',
        ];
    }

    /**
     * Get payment data to be passed to the job
     */
    public function getPaymentData(): array
    {
        $data = $this->validated();

        return [
            'type' => (int) $data['type'],
            'phone' => (string) $data['phone'],
            'amount' => (float) $data['amount'],
            'reference' => (string) $data['reference'],
            'bundle_name' => $data['bundle_name'] ?? null,
            'payment_data' => $data['payment_data'] ? json_decode($data['payment_data'], true) : [],
        ];
    }
}
