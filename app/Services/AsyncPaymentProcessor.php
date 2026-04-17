<?php

namespace App\Services;

use App\Models\User;
use App\Models\Bill;
use Spatie\Async\Pool;
use Illuminate\Support\Facades\Log;

/**
 * AsyncPaymentProcessor - Process payments using Spatie Async
 * 
 * Benefits over queue system:
 * - No database required for job storage
 * - Lower latency (processes immediately)
 * - Better for small to medium workloads
 * - Simpler setup and debugging
 * - Processes in parallel using process pool
 */
class AsyncPaymentProcessor
{
    protected $pool;
    protected $maxProcesses;
    protected $timeout;
    protected $isAvailable;

    public function __construct()
    {
        $this->maxProcesses = config('services.async.processes', 4);
        $this->timeout = config('services.async.timeout', 30);
        
        // Check if Spatie\Async is available
        $this->isAvailable = class_exists('Spatie\Async\Pool');
        
        if ($this->isAvailable) {
            try {
                $this->pool = Pool::create()
                    ->maxProcesses($this->maxProcesses)
                    ->timeout($this->timeout);
            } catch (\Exception $e) {
                Log::warning('Failed to initialize Spatie Async Pool', ['error' => $e->getMessage()]);
                $this->isAvailable = false;
            }
        } else {
            Log::warning('Spatie Async package not available. Async processing disabled.');
        }
    }

    /**
     * Process payment asynchronously using Spatie Async
     * 
     * @param User $user
     * @param array $paymentData
     * @return string - Process ID/identifier
     */
    public static function process(User $user, array $paymentData): string
    {
        return (new self())->dispatch($user, $paymentData);
    }

    /**
     * Process payment and return immediately
     */
    public function dispatch(User $user, array $paymentData): string
    {
        try {
            $processId = $this->generateProcessId();
            
            // Add process tracking info
            $paymentData['process_id'] = $processId;
            $paymentData['started_at'] = now();
            
            // Store payment intent in cache (for tracking)
            cache()->put(
                "payment.async.{$processId}",
                [
                    'user_id' => $user->id,
                    'status' => 'processing',
                    'type' => $paymentData['type'],
                    'amount' => $paymentData['amount'],
                    'started_at' => now(),
                ],
                now()->addMinutes(5) // Auto-cleanup after 5 minutes
            );

            // Add to async pool
            $this->pool->add(function () use ($user, $paymentData, $processId) {
                return $this->executePayment($user, $paymentData, $processId);
            });

            // Start processing
            $this->pool->wait();

            return $processId;
        } catch (\Exception $e) {
            Log::error('Async payment dispatch failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'payment_data' => $paymentData,
            ]);
            throw $e;
        }
    }

    /**
     * Execute payment processing
     */
    protected function executePayment(User $user, array $paymentData, string $processId): array
    {
        try {
            Log::info('Starting async payment processing', [
                'process_id' => $processId,
                'user_id' => $user->id,
                'type' => $paymentData['type'],
            ]);

            $type = $paymentData['type'];
            $amount = (float) ($paymentData['amount'] ?? 0);
            $result = match ((int)$type) {
                1 => $this->processAirtime($user, $paymentData),
                2 => $this->processData($user, $paymentData),
                3 => $this->processCableTV($user, $paymentData),
                4 => $this->processUtility($user, $paymentData),
                default => throw new \Exception("Invalid payment type: {$type}"),
            };

            // Update cache with success status
            cache()->put(
                "payment.async.{$processId}",
                array_merge(
                    cache()->get("payment.async.{$processId}", []),
                    ['status' => 'completed', 'result' => $result]
                ),
                now()->addMinutes(5)
            );

            // Track successful payment to Countly
            $this->trackPaymentSuccess($user, $type, $amount, $paymentData);

            Log::info('Async payment completed successfully', [
                'process_id' => $processId,
                'result' => $result,
            ]);

            return $result;
        } catch (\Exception $e) {
            $type = $paymentData['type'] ?? 0;
            $amount = (float) ($paymentData['amount'] ?? 0);

            Log::error('Async payment execution failed', [
                'process_id' => $processId,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Update cache with error status
            cache()->put(
                "payment.async.{$processId}",
                array_merge(
                    cache()->get("payment.async.{$processId}", []),
                    ['status' => 'failed', 'error' => $e->getMessage()]
                ),
                now()->addMinutes(5)
            );

            // Track failed payment to Countly
            $this->trackPaymentFailure($user, $type, $amount, $e->getMessage());

            throw $e;
        }
    }

    /**
     * Track successful payment to Countly
     */
    protected function trackPaymentSuccess(User $user, int $type, float $amount, array $paymentData): void
    {
        try {
            $countlyService = app(CountlyService::class);
            if (!$countlyService->isEnabled()) {
                return;
            }

            $typeLabels = [
                1 => 'airtime',
                2 => 'data',
                3 => 'cable_tv',
                4 => 'utility',
            ];

            $countlyService->trackBillPaymentEvent(
                $typeLabels[$type] ?? 'unknown',
                'completed',
                [
                    'amount' => $amount,
                    'reference' => $paymentData['reference'] ?? null,
                ]
            );
        } catch (\Exception $e) {
            Log::warning('Failed to track payment success to Countly', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Track failed payment to Countly
     */
    protected function trackPaymentFailure(User $user, int $type, float $amount, string $error): void
    {
        try {
            $countlyService = app(CountlyService::class);
            if (!$countlyService->isEnabled()) {
                return;
            }

            $typeLabels = [
                1 => 'airtime',
                2 => 'data',
                3 => 'cable_tv',
                4 => 'utility',
            ];

            $countlyService->trackBillPaymentEvent(
                $typeLabels[$type] ?? 'unknown',
                'failed',
                [
                    'amount' => $amount,
                    'error' => $error,
                ]
            );
        } catch (\Exception $e) {
            Log::warning('Failed to track payment failure to Countly', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Process Airtime purchase
     */
    protected function processAirtime(User $user, array $paymentData): array
    {
        $billPaymentManager = app('App\Services\BillPaymentManager');
        
        $amount = $paymentData['amount'];
        $phone = $paymentData['phone'];
        $network = $paymentData['network'] ?? null;

        // Check balance
        if ($user->wallet->balance < $amount) {
            throw new \Exception('Insufficient balance for airtime purchase');
        }

        // Deduct from wallet
        $user->wallet->debit($amount);

        // Process with provider
        $result = $billPaymentManager->handleAirtimePurchase(
            $user,
            $phone,
            $network,
            $amount,
            $paymentData['reference'] ?? null
        );

        // Store bill record
        Bill::create([
            'user_id' => $user->id,
            'type' => 1,
            'status' => 'completed',
            'amount' => $amount,
            'phone' => $phone,
            'bundle_name' => $paymentData['bundle_name'] ?? 'Airtime',
            'response' => json_encode($result),
            'reference' => $paymentData['reference'] ?? null,
            'provider_response' => json_encode($result),
        ]);

        return [
            'success' => true,
            'message' => 'Airtime purchase completed',
            'type' => 'airtime',
            'amount' => $amount,
        ];
    }

    /**
     * Process Data purchase
     */
    protected function processData(User $user, array $paymentData): array
    {
        $billPaymentManager = app('App\Services\BillPaymentManager');
        
        $amount = $paymentData['amount'];
        $phone = $paymentData['phone'];
        $plan = $paymentData['plan'] ?? null;

        // Check balance
        if ($user->wallet->balance < $amount) {
            throw new \Exception('Insufficient balance for data purchase');
        }

        // Deduct from wallet
        $user->wallet->debit($amount);

        // Process with provider
        $result = $billPaymentManager->handleDataPurchase(
            $user,
            $phone,
            $plan,
            $amount,
            $paymentData['reference'] ?? null
        );

        // Store bill record
        Bill::create([
            'user_id' => $user->id,
            'type' => 2,
            'status' => 'completed',
            'amount' => $amount,
            'phone' => $phone,
            'bundle_name' => $paymentData['bundle_name'] ?? 'Data',
            'response' => json_encode($result),
            'reference' => $paymentData['reference'] ?? null,
            'provider_response' => json_encode($result),
        ]);

        return [
            'success' => true,
            'message' => 'Data purchase completed',
            'type' => 'data',
            'amount' => $amount,
        ];
    }

    /**
     * Process Cable TV subscription
     */
    protected function processCableTV(User $user, array $paymentData): array
    {
        $billPaymentManager = app('App\Services\BillPaymentManager');
        
        $amount = $paymentData['amount'];
        $smartcard = $paymentData['smartcard'] ?? null;
        $bundle = $paymentData['bundle'] ?? null;

        // Check balance
        if ($user->wallet->balance < $amount) {
            throw new \Exception('Insufficient balance for cable TV subscription');
        }

        // Deduct from wallet
        $user->wallet->debit($amount);

        // Process with provider
        $result = $billPaymentManager->handleCableTVPayment(
            $user,
            $smartcard,
            $bundle,
            $amount,
            $paymentData['reference'] ?? null
        );

        // Store bill record
        Bill::create([
            'user_id' => $user->id,
            'type' => 3,
            'status' => 'completed',
            'amount' => $amount,
            'smartcard' => $smartcard,
            'bundle_name' => $paymentData['bundle_name'] ?? 'Cable TV',
            'response' => json_encode($result),
            'reference' => $paymentData['reference'] ?? null,
            'provider_response' => json_encode($result),
        ]);

        return [
            'success' => true,
            'message' => 'Cable TV subscription completed',
            'type' => 'cable_tv',
            'amount' => $amount,
        ];
    }

    /**
     * Process Utility bills
     */
    protected function processUtility(User $user, array $paymentData): array
    {
        $billPaymentManager = app('App\Services\BillPaymentManager');
        
        $amount = $paymentData['amount'];
        $account = $paymentData['account'] ?? null;
        $service = $paymentData['service'] ?? null;

        // Check balance
        if ($user->wallet->balance < $amount) {
            throw new \Exception('Insufficient balance for utility payment');
        }

        // Deduct from wallet
        $user->wallet->debit($amount);

        // Process with provider
        $result = $billPaymentManager->handleUtilityPayment(
            $user,
            $account,
            $service,
            $amount,
            $paymentData['reference'] ?? null
        );

        // Store bill record
        Bill::create([
            'user_id' => $user->id,
            'type' => 4,
            'status' => 'completed',
            'amount' => $amount,
            'account' => $account,
            'bundle_name' => $paymentData['bundle_name'] ?? 'Utility',
            'response' => json_encode($result),
            'reference' => $paymentData['reference'] ?? null,
            'provider_response' => json_encode($result),
        ]);

        return [
            'success' => true,
            'message' => 'Utility payment completed',
            'type' => 'utility',
            'amount' => $amount,
        ];
    }

    /**
     * Get payment status
     */
    public static function getStatus(string $processId): ?array
    {
        return cache()->get("payment.async.{$processId}");
    }

    /**
     * Generate unique process ID
     */
    protected function generateProcessId(): string
    {
        return 'PAY-' . uniqid() . '-' . now()->timestamp;
    }

    /**
     * Get pool statistics
     */
    public function getStats(): array
    {
        return [
            'max_processes' => $this->maxProcesses,
            'timeout' => $this->timeout,
            'pool_status' => 'active',
        ];
    }
}
