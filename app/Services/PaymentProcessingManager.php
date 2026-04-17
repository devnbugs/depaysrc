<?php

namespace App\Services;

use App\Models\User;
use App\Jobs\ProcessPaymentRequest;
use Illuminate\Support\Facades\Log;

/**
 * PaymentProcessingManager - Unified payment processing dispatcher
 * 
 * Intelligently routes payment processing to either:
 * - Queue system (for distributed processing)
 * - Spatie Async (for immediate local processing)
 * 
 * Configuration via config('services.payment.processor')
 * Options: 'queue' or 'async'
 */
class PaymentProcessingManager
{
    protected $processor;
    protected $useAsync;

    public function __construct()
    {
        $this->processor = config('services.payment.processor', 'queue');
        // Check if Spatie\Async is actually available at runtime
        $this->useAsync = $this->processor === 'async' 
            && class_exists('Spatie\Async\Pool')
            && class_exists('App\Services\AsyncPaymentProcessor');
    }

    /**
     * Dispatch payment for processing
     * 
     * @param User $user
     * @param array $paymentData
     * @return array
     */
    public function dispatch(User $user, array $paymentData): array
    {
        Log::info('Dispatching payment', [
            'processor' => $this->processor,
            'user_id' => $user->id,
            'type' => $paymentData['type'],
            'amount' => $paymentData['amount'],
        ]);

        if ($this->useAsync) {
            return $this->dispatchAsync($user, $paymentData);
        } else {
            return $this->dispatchQueue($user, $paymentData);
        }
    }

    /**
     * Dispatch to queue system
     */
    protected function dispatchQueue(User $user, array $paymentData): array
    {
        try {
            ProcessPaymentRequest::dispatch($user, $paymentData)
                ->onQueue(config('services.payment.queue', 'payments'));

            Log::info('Payment queued successfully', [
                'user_id' => $user->id,
                'type' => $paymentData['type'],
            ]);

            return [
                'success' => true,
                'processor' => 'queue',
                'message' => 'Payment queued for processing',
                'reference' => $paymentData['reference'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Queue dispatch failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Dispatch to Spatie Async
     */
    protected function dispatchAsync(User $user, array $paymentData): array
    {
        try {
            $processId = AsyncPaymentProcessor::process($user, $paymentData);

            Log::info('Payment processed asynchronously', [
                'user_id' => $user->id,
                'process_id' => $processId,
                'type' => $paymentData['type'],
            ]);

            return [
                'success' => true,
                'processor' => 'async',
                'message' => 'Payment processed successfully',
                'process_id' => $processId,
                'reference' => $paymentData['reference'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Async dispatch failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get current processor configuration
     */
    public function getConfig(): array
    {
        return [
            'processor' => $this->processor,
            'using_async' => $this->useAsync,
            'async_available' => class_exists('Spatie\Async\Pool'),
            'queue_connection' => config('queue.default'),
            'queue_name' => config('services.payment.queue', 'payments'),
            'async_processes' => config('services.async.processes', 4),
            'async_timeout' => config('services.async.timeout', 30),
        ];
    }

    /**
     * Switch processor (for testing/debugging)
     */
    public function useProcessor(string $processor): self
    {
        if (!in_array($processor, ['queue', 'async'])) {
            throw new \Exception("Invalid processor: {$processor}");
        }
        $this->processor = $processor;
        $this->useAsync = $processor === 'async' && class_exists('Spatie\Async\Pool');
        return $this;
    }
}
