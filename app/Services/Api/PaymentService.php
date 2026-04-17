<?php

namespace App\Services\Api;

use App\Models\User;
use App\Models\Bill;
use App\Services\Bills\BillPaymentManager;
use Illuminate\Support\Facades\Validator;

/**
 * Payment Service
 * 
 * Handles bill payments, airtime, data, and utility payments
 */
class PaymentService
{
    protected BillPaymentManager $billPaymentManager;

    public function __construct(BillPaymentManager $billPaymentManager)
    {
        $this->billPaymentManager = $billPaymentManager;
    }

    /**
     * Get available payment options
     * 
     * @return array
     */
    public function getPaymentOptions(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Airtime',
                'icon' => 'phone',
                'description' => 'Buy airtime for mobile phones',
                'min_amount' => 100,
                'max_amount' => 100000,
            ],
            [
                'id' => 2,
                'name' => 'Data Bundle',
                'icon' => 'wifi',
                'description' => 'Purchase data bundles',
                'min_amount' => 100,
                'max_amount' => 50000,
            ],
            [
                'id' => 3,
                'name' => 'Cable TV',
                'icon' => 'tv',
                'description' => 'Pay for cable TV subscriptions',
                'min_amount' => 500,
                'max_amount' => 100000,
            ],
            [
                'id' => 4,
                'name' => 'Utilities',
                'icon' => 'zap',
                'description' => 'Pay bills and utilities',
                'min_amount' => 500,
                'max_amount' => 100000,
            ],
        ];
    }

    /**
     * Get payment networks
     * 
     * @return array
     */
    public function getNetworks(): array
    {
        return [
            'airtime' => [
                'mtn' => 'MTN',
                'airtel' => 'Airtel',
                'glo' => 'Globacom',
                '9mobile' => '9Mobile',
            ],
            'data' => [
                'mtn' => 'MTN',
                'airtel' => 'Airtel',
                'glo' => 'Globacom',
                '9mobile' => '9Mobile',
            ],
            'cable' => [
                'dstv' => 'DStv',
                'gotv' => 'GOtv',
                'startimes' => 'StarTimes',
            ],
        ];
    }

    /**
     * Validate payment request
     * 
     * @param User $user
     * @param array $data
     * @return array
     */
    public function validatePayment(User $user, array $data): array
    {
        $validator = Validator::make($data, [
            'type' => ['required', 'integer', 'in:1,2,3,4'],
            'phone' => ['required', 'string', 'regex:/^[\d\+\-\(\)\s]{10,15}$/'],
            'amount' => ['required', 'numeric', 'min:100', 'max:100000'],
            'network' => ['required_if:type,1,2', 'string'],
        ]);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors(),
            ];
        }

        // Check balance
        if ($user->balance < $data['amount']) {
            return [
                'valid' => false,
                'error' => 'Insufficient balance',
            ];
        }

        // Check daily limit
        $dailyTotal = Bill::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->sum('debit_amount');

        if (($dailyTotal + $data['amount']) > 1000000) {
            return [
                'valid' => false,
                'error' => 'Daily transaction limit exceeded',
            ];
        }

        return [
            'valid' => true,
            'data' => $validator->validated(),
        ];
    }

    /**
     * Process payment
     * 
     * @param User $user
     * @param array $data
     * @return array
     */
    public function processPayment(User $user, array $data): array
    {
        try {
            $validation = $this->validatePayment($user, $data);

            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => $validation['error'] ?? 'Validation failed',
                    'errors' => $validation['errors'] ?? null,
                ];
            }

            $amount = (float) $data['amount'];
            $type = (int) $data['type'];
            $phone = (string) $data['phone'];

            // Deduct from balance
            $user->decrement('balance', $amount);

            // Create bill record
            $bill = Bill::create([
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
                'phone' => $phone,
                'status' => 1,
                'token' => $this->generateReference(),
            ]);

            // Track payment event
            $this->trackPayment($user, $type, $amount);

            return [
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => [
                    'reference' => $bill->token,
                    'amount' => (float) $bill->amount,
                    'type' => $this->getPaymentType($type),
                    'status' => 'completed',
                    'created_at' => $bill->created_at->toIso8601String(),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get payment history
     * 
     * @param User $user
     * @param int $limit
     * @return array
     */
    public function getPaymentHistory(User $user, int $limit = 50): array
    {
        $payments = $user->billPaid()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($bill) {
                return [
                    'id' => $bill->id,
                    'reference' => $bill->token,
                    'type' => $this->getPaymentType($bill->type),
                    'amount' => (float) $bill->debit_amount,
                    'phone' => $bill->phone,
                    'status' => $bill->status ? 'completed' : 'failed',
                    'created_at' => $bill->created_at->toIso8601String(),
                ];
            });

        return [
            'count' => $payments->count(),
            'total' => $user->billPaid()->sum('debit_amount'),
            'payments' => $payments->toArray(),
        ];
    }

    /**
     * Get payment details
     * 
     * @param string $reference
     * @param User $user
     * @return array
     */
    public function getPaymentDetails(string $reference, User $user): array
    {
        $bill = Bill::where('token', $reference)
            ->where('user_id', $user->id)
            ->first();

        if (!$bill) {
            return [
                'found' => false,
                'message' => 'Payment not found',
            ];
        }

        return [
            'found' => true,
            'data' => [
                'id' => $bill->id,
                'reference' => $bill->token,
                'type' => $this->getPaymentType($bill->type),
                'amount' => (float) $bill->debit_amount,
                'phone' => $bill->phone,
                'status' => $bill->status ? 'completed' : 'failed',
                'network' => $bill->network ?? null,
                'created_at' => $bill->created_at->toIso8601String(),
            ],
        ];
    }

    /**
     * Get payment statistics
     * 
     * @param User $user
     * @return array
     */
    public function getPaymentStatistics(User $user): array
    {
        $allPayments = $user->billPaid();
        $todayPayments = $allPayments->whereDate('created_at', today());
        $monthPayments = $allPayments->whereMonth('created_at', now()->month);

        return [
            'total_payments' => $allPayments->count(),
            'total_spent' => (float) $allPayments->sum('debit_amount'),
            'today_payments' => $todayPayments->count(),
            'today_spent' => (float) $todayPayments->sum('debit_amount'),
            'month_payments' => $monthPayments->count(),
            'month_spent' => (float) $monthPayments->sum('debit_amount'),
            'average_payment' => (float) round($allPayments->avg('debit_amount') ?? 0, 2),
        ];
    }

    /**
     * Get payment type label
     * 
     * @param int $type
     * @return string
     */
    private function getPaymentType(int $type): string
    {
        $types = [
            1 => 'Airtime',
            2 => 'Data',
            3 => 'Cable TV',
            4 => 'Utility',
        ];

        return $types[$type] ?? 'Payment';
    }

    /**
     * Generate payment reference
     * 
     * @return string
     */
    private function generateReference(): string
    {
        return 'PAY' . strtoupper(uniqid());
    }

    /**
     * Track payment event
     * 
     * @param User $user
     * @param int $type
     * @param float $amount
     * @return void
     */
    private function trackPayment(User $user, int $type, float $amount): void
    {
        try {
            $countlyService = app('App\Services\CountlyService');
            if ($countlyService && method_exists($countlyService, 'trackPaymentEvent')) {
                $countlyService->trackPaymentEvent('payment_completed', [
                    'type' => $type,
                    'amount' => $amount,
                    'user_id' => $user->id,
                ]);
            }
        } catch (\Exception $e) {
            // Silent fail
        }
    }
}
