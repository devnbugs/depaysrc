<?php

namespace App\Livewire;

use App\Services\TurnstileService;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Example 4: Bill Payment Component
 */
class BillPaymentExample extends Component
{
    protected TurnstileService $turnstileService;
    public string $billType = ''; // electricity, water, internet, etc.
    public string $provider = '';
    public string $customerReference = '';
    public float $amount = 0;
    public bool $isProcessing = false;

    protected array $billProviders = [
        'electricity' => ['PHCN', 'IBEDC', 'EKEDC'],
        'water' => ['Lagos Water Board'],
        'internet' => ['DStv', 'GOtv'],
    ];

    public function mount(): void
    {
        $this->turnstileService = app(TurnstileService::class);
    }

    public function render(): View
    {
        return view('livewire.bill-payment-example', [
            'providers' => $this->getProviders(),
        ]);
    }

    /**
     * Get providers for selected bill type
     */
    private function getProviders(): array
    {
        return $this->billProviders[$this->billType] ?? [];
    }

    /**
     * Process bill payment
     */
    public function payBill(string $token): void
    {
        $this->isProcessing = true;

        try {
            $this->validate([
                'billType' => ['required', 'in:electricity,water,internet'],
                'provider' => ['required', 'string'],
                'customerReference' => ['required', 'string'],
                'amount' => ['required', 'numeric', 'min:500'],
            ]);

            // Strict protection for bill payments
            if (!$this->turnstileService->checkRateLimit(
                request()->ip(),
                'bill_payment',
                5,  // max 5 attempts
                5   // per 5 minutes
            )) {
                throw new \Exception('Too many bill payment attempts.');
            }

            if (!$this->turnstileService->verify($token, request()->ip())) {
                throw new \Exception('Security verification failed.');
            }

            // Log payment
            $this->turnstileService->logSecurityEvent('Bill Payment', [
                'user_id' => auth()->id(),
                'bill_type' => $this->billType,
                'provider' => $this->provider,
                'amount' => $this->amount,
            ]);

            // Process payment via VTPass or similar provider
            // ... integration code ...

            $this->dispatch('billPaymentSuccess');
            $this->reset();

        } catch (\Exception $e) {
            $this->addError('payment', $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }
}
