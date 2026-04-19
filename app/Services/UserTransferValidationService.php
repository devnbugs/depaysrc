<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * User Transfer Validation Service
 * 
 * Enforces tier-based transfer limits, deposit requirements, and KYC checks
 */
class UserTransferValidationService
{
    public function __construct(protected KycVerificationService $kycService)
    {
    }

    /**
     * Validate if user can perform a transfer
     */
    public function validateTransfer(User $user, float $amount, string $type = 'bank_transfer'): array
    {
        $errors = [];
        $warnings = [];

        // Check if user has completed onboarding
        if ($this->kycService->needsOnboarding($user)) {
            $errors[] = 'Please complete onboarding before transferring funds.';
        }

        // Check KYC verification level
        $level = $this->kycService->getUserLevel($user);
        $limits = $this->kycService->getUserLimits($user);

        if (!$limits['can_transfer']) {
            $errors[] = "Your account level ({$limits['level_name']}) does not allow transfers.";
        }

        // Check deposit requirement for Level 3 features
        if ($type === 'full_transfer' || $type === 'account_number') {
            if ($level < KycVerificationService::LEVEL_PREMIUM) {
                $depositReq = $this->kycService->checkDepositRequirement($user);
                if (!$depositReq['met']) {
                    $errors[] = "You need to deposit ₦{$depositReq['required']} to unlock full transfer features. Currently deposited: ₦{$depositReq['deposited']}";
                }
            }
        }

        // Check transfer limit
        if ($amount > $limits['transfer_limit']) {
            $errors[] = "Amount exceeds your transfer limit of ₦{$limits['transfer_limit']} ({$limits['level_name']} tier).";
        }

        // Check if amount is within reasonable bounds
        if ($amount <= 0) {
            $errors[] = "Transfer amount must be greater than zero.";
        }

        if ($amount > 10000000) {
            $warnings[] = "Large transfer amount. Please verify the recipient details carefully.";
        }

        return [
            'allowed' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'limit' => $limits['transfer_limit'],
            'level' => $limits['level_name'],
            'level_code' => $level,
            'user_id' => $user->id,
        ];
    }

    /**
     * Validate if user can create virtual account
     */
    public function validateAccountCreation(User $user): array
    {
        $errors = [];

        // Check if user has completed onboarding
        if ($this->kycService->needsOnboarding($user)) {
            $errors[] = 'Please complete onboarding before creating accounts.';
        }

        // Check account creation limit
        $canCreate = $this->kycService->canCreateAccount($user);
        if (!$canCreate['allowed']) {
            $errors[] = $canCreate['reason'];
        }

        // Check if user has full access
        if (!$this->kycService->hasFullAccess($user)) {
            $errors[] = 'You need to complete all KYC requirements to create virtual accounts.';
        }

        return [
            'allowed' => empty($errors),
            'errors' => $errors,
            'existing_accounts' => $canCreate['existing'] ?? 0,
            'limit' => $canCreate['limit'] ?? 1,
        ];
    }

    /**
     * Validate if user can access advanced features
     */
    public function validateAdvancedFeatures(User $user, string $feature = 'transfer'): array
    {
        $errors = [];

        // List of features requiring full access
        $fullAccessFeatures = ['account_number', 'full_transfer', 'virtual_account'];

        if (in_array($feature, $fullAccessFeatures)) {
            if (!$this->kycService->hasFullAccess($user)) {
                $errors[] = "Feature '{$feature}' requires Level 3 (Premium) verification. Please complete your KYC verification.";
            }
        }

        return [
            'allowed' => empty($errors),
            'errors' => $errors,
            'requires_level_3' => in_array($feature, $fullAccessFeatures),
        ];
    }

    /**
     * Get user's transfer eligibility summary
     */
    public function getTransferEligibility(User $user): array
    {
        $level = $this->kycService->getUserLevel($user);
        $limits = $this->kycService->getUserLimits($user);
        $depositReq = $this->kycService->checkDepositRequirement($user);
        $canTransfer = $this->kycService->canTransfer($user);

        return [
            'eligible' => $canTransfer['allowed'],
            'level' => $limits['level_name'],
            'level_code' => $level,
            'transfer_limit' => $limits['transfer_limit'],
            'daily_limit' => $limits['daily_limit'],
            'can_transfer' => $limits['can_transfer'],
            'can_create_account' => $limits['account_creation_limit'] > 0,
            'full_features_unlocked' => $limits['full_features'],
            'deposit_required_for_level_3' => !$depositReq['met'],
            'deposit_amount' => $depositReq['required'],
            'deposit_completed' => $depositReq['deposited'],
            'deposit_remaining' => $depositReq['remaining'],
            'liveness_verified' => $user->kyc_liveness_verified ?? false,
            'identity_verified' => $user->identity_verified_at !== null,
            'onboarding_complete' => $user->onboarding_completed_at !== null,
        ];
    }

    /**
     * Check if user needs to complete profile before transaction
     */
    public function requiresProfileCompletion(User $user): bool
    {
        return $this->kycService->needsOnboarding($user) || 
               blank($user->identity_verified_at) ||
               !$user->kyc_liveness_verified;
    }

    /**
     * Get profile completion percentage
     */
    public function getProfileCompletionPercentage(User $user): int
    {
        $progress = $this->kycService->getOnboardingProgress($user);
        return $progress['progress_percentage'] ?? 0;
    }

    /**
     * Log transfer attempt for audit
     */
    public function logTransferAttempt(User $user, float $amount, string $type, bool $allowed, array $reason = []): void
    {
        $logData = [
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => $type,
            'allowed' => $allowed,
            'reason' => $reason,
            'kyc_level' => $this->kycService->getUserLevel($user),
            'timestamp' => now(),
        ];

        if (!$allowed) {
            Log::warning('Transfer attempt denied', $logData);
        } else {
            Log::info('Transfer attempt allowed', $logData);
        }
    }
}
