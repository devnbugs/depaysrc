<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

/**
 * User Profile Service
 * 
 * Handles user profile operations and data management
 */
class UserProfileService
{
    /**
     * Get user profile with all details
     * 
     * @param User $user
     * @return array
     */
    public function getProfile(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'phone' => $user->phone,
            'balance' => (float) $user->balance,
            'status' => $user->status,
            'avatar' => $user->image ? asset('assets/images/user/profile/' . $user->image) : null,
            'email_verified' => !is_null($user->email_verified_at),
            'sms_verified' => !is_null($user->sms_verified_at),
            'kyc_status' => $user->kycVerified ?? 'pending',
            'two_factor_enabled' => (bool) $user->two_factor_enabled,
            'pin_enabled' => (int) $user->pin_state === 1,
            'created_at' => $user->created_at->toIso8601String(),
            'updated_at' => $user->updated_at->toIso8601String(),
        ];
    }

    /**
     * Update user profile
     * 
     * @param User $user
     * @param array $data
     * @return array
     */
    public function updateProfile(User $user, array $data): array
    {
        $validator = Validator::make($data, [
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'country' => ['sometimes', 'string'],
            'city' => ['sometimes', 'string'],
            'state' => ['sometimes', 'string'],
            'zip_code' => ['sometimes', 'string'],
            'address' => ['sometimes', 'string'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        try {
            $user->update($validator->validated());

            return [
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $this->getProfile($user),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get user balance and wallet info
     * 
     * @param User $user
     * @return array
     */
    public function getWalletInfo(User $user): array
    {
        return [
            'balance' => (float) $user->balance,
            'currency' => config('app.currency', 'NGN'),
            'last_transaction' => $user->billPaid()->latest()->first()?->created_at?->toIso8601String(),
            'total_transactions' => (int) $user->billPaid()->count(),
            'account_status' => $user->status,
            'verified' => !is_null($user->email_verified_at),
        ];
    }

    /**
     * Get user verification status
     * 
     * @param User $user
     * @return array
     */
    public function getVerificationStatus(User $user): array
    {
        return [
            'email_verified' => !is_null($user->email_verified_at),
            'sms_verified' => !is_null($user->sms_verified_at),
            'kyc_verified' => $user->kycVerified === 'approved',
            'kyc_status' => $user->kycVerified ?? 'pending',
            'two_factor_enabled' => (bool) $user->two_factor_enabled,
            'pin_set' => (int) $user->pin_state === 1,
            'verification_level' => $this->calculateVerificationLevel($user),
        ];
    }

    /**
     * Get transaction history
     * 
     * @param User $user
     * @param int $limit
     * @return array
     */
    public function getTransactionHistory(User $user, int $limit = 50): array
    {
        $transactions = $user->billPaid()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($bill) {
                return [
                    'id' => $bill->id,
                    'type' => $this->getTransactionType($bill),
                    'amount' => (float) $bill->debit_amount,
                    'status' => $bill->status ? 'completed' : 'failed',
                    'reference' => $bill->token,
                    'description' => $bill->phone ?? '',
                    'created_at' => $bill->created_at->toIso8601String(),
                ];
            });

        return [
            'count' => $transactions->count(),
            'transactions' => $transactions->toArray(),
        ];
    }

    /**
     * Calculate verification level
     * 
     * @param User $user
     * @return int
     */
    private function calculateVerificationLevel(User $user): int
    {
        $level = 0;

        if (!is_null($user->email_verified_at)) $level++;
        if (!is_null($user->sms_verified_at)) $level++;
        if ($user->kycVerified === 'approved') $level++;

        return $level;
    }

    /**
     * Get transaction type label
     * 
     * @param mixed $bill
     * @return string
     */
    private function getTransactionType($bill): string
    {
        $types = [
            1 => 'Airtime',
            2 => 'Data',
            3 => 'Cable TV',
            4 => 'Utility',
        ];

        return $types[$bill->type] ?? 'Payment';
    }

    /**
     * Check if user can perform action
     * 
     * @param User $user
     * @param string $action
     * @return array
     */
    public function checkActionPermission(User $user, string $action): array
    {
        $canPerform = true;
        $reason = null;

        if ($user->status === 'suspended') {
            $canPerform = false;
            $reason = 'Account is suspended';
        }

        if ($action === 'payment' && $user->balance <= 0) {
            $canPerform = false;
            $reason = 'Insufficient balance';
        }

        return [
            'can_perform' => $canPerform,
            'reason' => $reason,
        ];
    }
}
