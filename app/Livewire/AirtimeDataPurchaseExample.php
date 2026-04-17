<?php

namespace App\Livewire;

use App\Services\TurnstileService;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Example 2: Airtime/Data Purchase Component
 */
class AirtimeDataPurchaseExample extends Component
{
    protected TurnstileService $turnstileService;
    public string $serviceType = 'airtime'; // airtime, data
    public string $network = '';
    public string $amount = '';
    public string $phoneNumber = '';
    public bool $isProcessing = false;
    public array $networks = ['MTN', 'Airtel', 'Glo', '9Mobile'];
    public array $airtimeAmounts = ['100', '200', '500', '1000', '2000', '5000'];
    public array $dataPlans = [];

    public function mount(): void
    {
        $this->turnstileService = app(TurnstileService::class);
    }

    public function render(): View
    {
        return view('livewire.airtime-data-purchase-example');
    }

    /**
     * Handle service type change
     */
    public function updatedServiceType(): void
    {
        $this->amount = '';
        $this->dataPlans = $this->getDataPlans($this->network);
    }

    /**
     * Get available data plans
     */
    private function getDataPlans(string $network): array
    {
        // Map networks to their plans
        $plans = [
            'MTN' => [
                '100' => '50MB',
                '200' => '100MB',
                '500' => '1GB',
                '1000' => '2GB',
                '2000' => '5GB',
                '5000' => '20GB',
            ],
            'Airtel' => [
                '100' => '75MB',
                '200' => '150MB',
                '500' => '1GB',
                '1000' => '2.5GB',
                '2000' => '6GB',
                '5000' => '25GB',
            ],
        ];

        return $plans[$network] ?? [];
    }

    /**
     * Process airtime/data purchase
     */
    public function purchaseAirtime(string $token): void
    {
        $this->isProcessing = true;

        try {
            $this->validate([
                'network' => ['required', 'in:' . implode(',', $this->networks)],
                'amount' => ['required', 'numeric', 'min:100'],
                'phoneNumber' => ['required', 'string', 'regex:/^\d{10,11}$/'],
            ]);

            // Verify Turnstile
            if (!$this->turnstileService->verify($token, request()->ip())) {
                throw new \Exception('Security verification failed.');
            }

            // Log event
            $this->turnstileService->logSecurityEvent('Airtime Purchase', [
                'user_id' => auth()->id(),
                'network' => $this->network,
                'amount' => $this->amount,
                'phone' => $this->phoneNumber,
            ]);

            // Process purchase (integrate with VTPass or your provider)
            // ... your airtime/data API integration here ...

            $this->dispatch('purchaseSuccess', [
                'type' => $this->serviceType,
                'amount' => $this->amount,
                'network' => $this->network,
            ]);

            $this->reset(['network', 'amount', 'phoneNumber']);

        } catch (\Exception $e) {
            $this->addError('purchase', $e->getMessage());
            $this->turnstileService->trackSuspiciousActivity(
                request()->ip(),
                "Airtime purchase failed: {$e->getMessage()}"
            );
        } finally {
            $this->isProcessing = false;
        }
    }
}
