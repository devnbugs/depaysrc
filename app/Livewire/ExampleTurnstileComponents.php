<?php

namespace App\Livewire;

use App\Services\TurnstileService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Example Livewire Components Using Invisible Turnstile
 * 
 * These examples demonstrate how to integrate Turnstile protection
 * in your Livewire components for sensitive operations
 */

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

            // Detect multiple requests for same action
            if (!app(InvisibleTurnstile::class)->detectMultipleRequests('airtime_purchase')) {
                throw new \Exception('Too many purchase attempts. Please wait.');
            }

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
            if (!app(InvisibleTurnstile::class)->protectSensitiveRequest(
                'fund_transfer',
                [
                    'bank' => $this->recipientBank,
                    'amount' => $this->amount,
                ]
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

/**
 * USAGE IN YOUR BLADE VIEWS
 * ==========================
 * 
 * <!-- Load invisible Turnstile at top of layout -->
 * @livewire('invisible-turnstile')
 * 
 * <!-- Use component with x-data for Alpine integration -->
 * @livewire('payment-processor-example')
 * 
 * <!-- Dispatch Turnstile when needed -->
 * <button @click="$dispatch('execute-protected-action', {action: 'payment'})">
 *     Pay Now
 * </button>
 */
