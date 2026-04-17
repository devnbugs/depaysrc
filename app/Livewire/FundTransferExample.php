<?php

namespace App\Livewire;

use App\Services\TurnstileService;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Example 3: Fund Transfer Component
 */
class FundTransferExample extends Component
{
    protected TurnstileService $turnstileService;
    public string $transferType = 'local'; // local, international
    public string $recipientBank = '';
    public string $accountNumber = '';
    public string $accountName = '';
    public float $amount = 0;
    public string $description = '';
    public bool $isProcessing = false;
    public bool $showConfirmation = false;
    public ?string $otp = null;

    public function mount(): void
    {
        $this->turnstileService = app(TurnstileService::class);
    }

    public function render(): View
    {
        return view('livewire.fund-transfer-example');
    }

    /**
     * Verify recipient bank details
     */
    public function verifyAccountDetails(): void
    {
        $this->validate([
            'recipientBank' => ['required', 'string'],
            'accountNumber' => ['required', 'string', 'regex:/^\d{10,12}$/'],
        ]);

        // Integrate with your bank verification API
        // Example: Monnify, Paystack, etc.

        // Simulate verification
        $this->accountName = "John Doe";
        $this->showConfirmation = true;
    }

    /**
     * Initiate transfer with Turnstile
     */
    public function initiateTransfer(string $token): void
    {
        $this->isProcessing = true;

        try {
            // Protect sensitive request
            if (!$this->turnstileService->checkRateLimit(
                request()->ip(),
                'fund_transfer',
                3,  // max 3 attempts
                15  // per 15 minutes
            )) {
                throw new \Exception('Transfer request rate limited. Please wait.');
            }

            // Verify Turnstile
            if (!$this->turnstileService->verifyWithProtection(
                $token,
                'fund_transfer',
                request()->ip(),
                3,  // max 3 attempts
                15  // per 15 minutes
            )) {
                throw new \Exception('Verification failed.');
            }

            // Log transfer initiation
            $this->turnstileService->logSecurityEvent('Fund Transfer Initiated', [
                'user_id' => auth()->id(),
                'bank' => $this->recipientBank,
                'amount' => $this->amount,
                'recipient_account' => substr($this->accountNumber, -4),
            ]);

            // Require OTP for additional security
            $this->dispatch('sendOTP');
            $this->dispatch('showOTPModal');

        } catch (\Exception $e) {
            $this->addError('transfer', $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    /**
     * Confirm transfer with OTP
     */
    public function confirmTransferWithOTP(): void
    {
        $this->validate(['otp' => ['required', 'string', 'size:6']]);

        try {
            // Verify OTP
            // ... your OTP verification logic ...

            // Log successful transfer
            $this->turnstileService->logSecurityEvent('Fund Transfer Completed', [
                'user_id' => auth()->id(),
                'bank' => $this->recipientBank,
                'amount' => $this->amount,
            ]);

            $this->dispatch('transferSuccess');
            $this->reset();

        } catch (\Exception $e) {
            $this->addError('otp', $e->getMessage());
        }
    }
}
