<?php

namespace App\Livewire;

use App\Services\TurnstileService;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Example 1: Payment Processing Component
 */
class PaymentProcessorExample extends Component
{
    protected TurnstileService $turnstileService;
    public float $amount = 0;
    public string $recipient = '';
    public string $description = '';
    public bool $isProcessing = false;
    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    public function mount(): void
    {
        $this->turnstileService = app(TurnstileService::class);
    }

    public function render(): View
    {
        return view('livewire.payment-processor-example', [
            'turnstileEnabled' => $this->turnstileService->isEnabled(),
        ]);
    }

    /**
     * Process payment with Turnstile protection
     */
    public function processPayment(string $token): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;
        $this->isProcessing = true;

        try {
            // Validate input
            $this->validate([
                'amount' => ['required', 'numeric', 'min:100'],
                'recipient' => ['required', 'email'],
                'description' => ['required', 'string', 'max:255'],
            ]);

            // Verify Turnstile with strict protection
            if (!$this->turnstileService->checkRateLimit(
                request()->ip(),
                'payment',
                3,  // max 3 attempts
                15  // per 15 minutes
            )) {
                throw new \Exception('Too many payment attempts. Please wait before trying again.');
            }

            if (!$this->turnstileService->verify($token, request()->ip())) {
                throw new \Exception('Security verification failed. Please try again.');
            }

            // Log the attempt
            $this->turnstileService->logSecurityEvent('Payment Processing', [
                'user_id' => auth()->id(),
                'amount' => $this->amount,
                'recipient' => $this->recipient,
            ]);

            // Process payment (simulated)
            // ... your payment gateway code here ...

            $this->successMessage = "Payment of NGN {$this->amount} sent to {$this->recipient}";
            $this->reset(['amount', 'recipient', 'description']);

            $this->dispatch('paymentSuccess', [
                'amount' => $this->amount,
                'recipient' => $this->recipient,
            ]);

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->turnstileService->trackSuspiciousActivity(
                request()->ip(),
                "Payment failed: {$e->getMessage()}"
            );
        } finally {
            $this->isProcessing = false;
        }
    }

    /**
     * Validate amount before processing
     */
    public function validateAmount(): void
    {
        if ($this->amount < 100) {
            $this->addError('amount', 'Minimum amount is NGN 100');
        } elseif ($this->amount > 999999) {
            $this->addError('amount', 'Maximum amount is NGN 999,999');
        }
    }
}
