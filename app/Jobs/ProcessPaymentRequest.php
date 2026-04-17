<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Bills\BillPaymentManager;
use App\Services\WalletService;
use App\Services\CountlyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Process Payment Requests as Queued Job
 * Handles: Airtime, Data, TV, Utility, and other bill payments
 *
 * Usage:
 * ProcessPaymentRequest::dispatch($user, [
 *     'type' => 1, // 1=Airtime, 2=Data, 3=CableTV, 4=Utility
 *     'phone' => '08012345678',
 *     'network' => 'mtn',
 *     'amount' => 1000,
 *     'bundle_data' => [...],
 *     'reference' => 'TRX123',
 * ]);
 */
class ProcessPaymentRequest implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 1;

    public function __construct(
        public User $user,
        public array $paymentData,
    ) {
    }

    public function handle(
        BillPaymentManager $billPaymentManager,
        WalletService $walletService,
        CountlyService $countlyService,
    ): void {
        try {
            // Validate user state
            if ($this->user->suspended) {
                throw new \Exception('Account is suspended.');
            }

            $type = (int) $this->paymentData['type'];
            $phone = (string) $this->paymentData['phone'];
            $reference = (string) $this->paymentData['reference'];
            $amount = (float) $this->paymentData['amount'];

            // Check balance
            if ($this->user->balance < $amount) {
                throw new \Exception('Insufficient balance.');
            }

            $result = match ($type) {
                1 => $this->processAirtime($billPaymentManager, $phone),
                2 => $this->processData($billPaymentManager, $phone),
                3 => $this->processCableTV($billPaymentManager, $phone),
                4 => $this->processUtility($billPaymentManager, $phone),
                default => throw new \Exception('Invalid payment type.'),
            };

            // Process successful payment
            if (($result['status'] ?? 'failed') === 'failed') {
                throw new \Exception($result['message'] ?? 'Payment processing failed.');
            }

            // Create bill record
            $bill = $walletService->purchaseBill(
                $this->user,
                $amount,
                $this->paymentData['bundle_name'] ?? 'Payment',
                $result['reference'] ?? $reference,
                $this->buildBillData($type, $result)
            );

            // Mark gateway
            $this->markBillGateway($bill, (string) data_get($result, 'provider', 'budpay'));

            // Track successful payment event to Countly
            $this->trackPaymentSuccess($countlyService, $type, $amount);

        } catch (Throwable $e) {
            // Store failed payment attempt
            $this->storeBill($type, [
                'amount' => $amount ?? 0,
                'phone' => $phone ?? '',
                'reference' => $reference,
                'status' => 0,
                'error' => $e->getMessage(),
            ]);

            // Track failed payment event to Countly
            $this->trackPaymentFailure($countlyService, $type, $amount ?? 0, $e->getMessage());

            // Dispatch failed event or log
            \Log::error('Payment Request Failed', [
                'user_id' => $this->user->id,
                'payment_data' => $this->paymentData,
                'error' => $e->getMessage(),
            ]);

            // Fail the job
            throw $e;
        }
    }

    /**
     * Track successful payment to Countly
     */
    private function trackPaymentSuccess(CountlyService $countlyService, int $type, float $amount): void
    {
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
                'reference' => $this->paymentData['reference'] ?? null,
            ]
        );
    }

    /**
     * Track failed payment to Countly
     */
    private function trackPaymentFailure(CountlyService $countlyService, int $type, float $amount, string $error): void
    {
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
                'reference' => $this->paymentData['reference'] ?? null,
            ]
        );
    }

    private function processAirtime(BillPaymentManager $billPaymentManager, string $phone): array
    {
        $network = (string) $this->paymentData['network'];
        $amount = (float) $this->paymentData['amount'];

        return $billPaymentManager->purchaseAirtime($network, $phone, $amount);
    }

    private function processData(BillPaymentManager $billPaymentManager, string $phone): array
    {
        $bundleData = $this->paymentData['bundle_data'] ?? [];
        $bundle = (object) array_merge(
            ['cost' => $this->paymentData['amount'] ?? 0],
            $bundleData
        );

        return $billPaymentManager->purchaseData($bundle, $phone);
    }

    private function processCableTV(BillPaymentManager $billPaymentManager, string $phone): array
    {
        $smartcard = (string) $this->paymentData['smartcard'] ?? '';
        $bundleData = $this->paymentData['bundle_data'] ?? [];
        $bundle = (object) array_merge(
            ['cost' => $this->paymentData['amount'] ?? 0],
            $bundleData
        );

        return $billPaymentManager->purchaseCableTV($bundle, $smartcard, $phone);
    }

    private function processUtility(BillPaymentManager $billPaymentManager, string $phone): array
    {
        $bundleData = $this->paymentData['bundle_data'] ?? [];
        $bundle = (object) array_merge(
            ['cost' => $this->paymentData['amount'] ?? 0],
            $bundleData
        );

        return $billPaymentManager->purchaseUtility($bundle, $phone);
    }

    private function buildBillData(int $type, array $result): array
    {
        $baseData = [
            'token' => $result['reference'] ?? getTrx(),
            'debit_amount' => $this->paymentData['amount'] ?? 0,
            'charge' => 0,
            'profit' => 0,
            'phone' => $this->paymentData['phone'] ?? '',
            'type' => $type,
            'status' => ($result['status'] ?? 'success') === 'pending' ? 0 : 1,
            'response' => $result['meta'] ?? [],
            'bywho' => ucfirst((string) data_get($result, 'provider', 'BudPay')),
        ];

        // Add type-specific data
        return match ($type) {
            1 => array_merge($baseData, [
                'network' => strtoupper((string) $this->paymentData['network'] ?? ''),
                'transaction_details' => 'Airtime purchase',
            ]),
            2 => array_merge($baseData, [
                'network' => $this->paymentData['bundle_data']['network'] ?? '',
                'plan' => $this->paymentData['bundle_data']['plan'] ?? '',
                'validity' => $this->paymentData['bundle_data']['validity'] ?? '',
                'transaction_details' => 'Data purchase',
            ]),
            3 => array_merge($baseData, [
                'smartcard' => $this->paymentData['smartcard'] ?? '',
                'transaction_details' => 'Cable TV payment',
            ]),
            4 => array_merge($baseData, [
                'transaction_details' => 'Utility payment',
            ]),
            default => $baseData,
        };
    }

    private function markBillGateway(Bill $bill, string $provider): void
    {
        $bill->update(['gateway' => $provider]);
    }

    private function storeBill(int $type, array $data): void
    {
        Bill::create(array_merge($data, [
            'user_id' => $this->user->id,
            'type' => $type,
        ]));
    }
}
