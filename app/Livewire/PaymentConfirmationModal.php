<?php

namespace App\Livewire;

use App\Http\Controllers\PaymentConfirmationController;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Payment Confirmation Modal with PIN Input
 *
 * Usage in blade:
 * @livewire('payment-confirmation-modal', [
 *     'type' => 1,
 *     'amount' => 1000,
 *     'phone' => '08012345678',
 *     'bundleName' => 'MTN Airtime',
 *     'reference' => 'TRX123456',
 *     'paymentData' => ['network' => 'mtn']
 * ])
 */
class PaymentConfirmationModal extends Component
{
    public int $type;
    public float $amount;
    public string $phone;
    public string $bundleName = '';
    public string $reference = '';
    public array $paymentData = [];

    public string $pin1 = '';
    public string $pin2 = '';
    public string $pin3 = '';
    public string $pin4 = '';
    public string $authenticatorCode = '';
    public bool $isProcessing = false;
    public ?string $errorMessage = null;
    public bool $showModal = false;

    public function mount()
    {
        $user = Auth::user();
        $this->showModal = ((int) $user->pin_state === 1 || (int) $user->two_factor_enabled === 1);
    }

    public function updatedPin($value, $pinNumber)
    {
        // Auto-advance to next field
        if (strlen($value) === 1) {
            match ($pinNumber) {
                '1' => $this->focus('pin2'),
                '2' => $this->focus('pin3'),
                '3' => $this->focus('pin4'),
                default => null,
            };
        }

        // Handle backspace
        if ($value === '') {
            match ($pinNumber) {
                '2' => $this->focus('pin1'),
                '3' => $this->focus('pin2'),
                '4' => $this->focus('pin3'),
                default => null,
            };
        }
    }

    public function confirmPayment()
    {
        $this->errorMessage = null;
        $this->isProcessing = true;

        try {
            $user = Auth::user();

            // Prepare request data
            $requestData = [
                'type' => $this->type,
                'phone' => $this->phone,
                'amount' => $this->amount,
                'reference' => $this->reference,
                'bundle_name' => $this->bundleName,
                'payment_data' => json_encode($this->paymentData),
            ];

            // Add PIN or 2FA code
            if ((int) $user->pin_state === 1) {
                $pin = $this->pin1 . $this->pin2 . $this->pin3 . $this->pin4;
                if (strlen($pin) !== 4) {
                    $this->errorMessage = 'Please enter your complete 4-digit PIN.';
                    $this->isProcessing = false;
                    return;
                }
                $requestData['pin_code'] = $pin;
            } elseif ((int) $user->two_factor_enabled === 1) {
                if (strlen($this->authenticatorCode) !== 6) {
                    $this->errorMessage = 'Please enter your complete 6-digit 2FA code.';
                    $this->isProcessing = false;
                    return;
                }
                $requestData['authenticator_code'] = $this->authenticatorCode;
            }

            // Call the payment confirmation controller
            $controller = app(PaymentConfirmationController::class);
            $request = new \Illuminate\Http\Request($requestData);
            $request->setMethod('POST');

            $response = $controller->confirm($request);

            if ($response->getData()->success ?? false) {
                // Payment confirmed successfully
                session()->flash('success', $response->getData()->message);

                // Emit event to close modal and redirect
                $this->dispatch('payment-confirmed', [
                    'redirect' => $response->getData()->redirect ?? null,
                ]);

                // Clear form
                $this->clearForm();
            } else {
                $this->errorMessage = $response->getData()->message ?? 'Payment confirmation failed.';
            }

        } catch (\Throwable $e) {
            \Log::error('Payment confirmation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->errorMessage = 'An error occurred. Please try again.';
        } finally {
            $this->isProcessing = false;
        }
    }

    public function closeModal()
    {
        $this->clearForm();
        $this->showModal = false;
    }

    private function clearForm()
    {
        $this->pin1 = '';
        $this->pin2 = '';
        $this->pin3 = '';
        $this->pin4 = '';
        $this->authenticatorCode = '';
        $this->errorMessage = null;
    }

    public function focus($field)
    {
        $this->dispatch('focus-field', field: $field);
    }

    public function render()
    {
        $user = Auth::user();
        $requirePin = (int) $user->pin_state === 1;
        $require2fa = (int) $user->two_factor_enabled === 1;

        $typeLabel = match ($this->type) {
            1 => 'Airtime Purchase',
            2 => 'Data Purchase',
            3 => 'Cable TV Payment',
            4 => 'Utility Payment',
            default => 'Payment',
        };

        return view('livewire.payment-confirmation-modal', [
            'user' => $user,
            'requirePin' => $requirePin,
            'require2fa' => $require2fa,
            'typeLabel' => $typeLabel,
            'general' => \App\Models\GeneralSetting::first(),
        ]);
    }
}
