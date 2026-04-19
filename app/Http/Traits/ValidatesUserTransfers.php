<?php

namespace App\Http\Traits;

use App\Services\UserTransferValidationService;
use App\Models\User;

/**
 * ValidatesUserTransfers Trait
 * 
 * Use this trait in any controller that handles transfers to automatically validate user KYC levels and limits
 */
trait ValidatesUserTransfers
{
    /**
     * Get transfer validation service instance
     */
    protected function transferValidationService(): UserTransferValidationService
    {
        return app(UserTransferValidationService::class);
    }

    /**
     * Check if user can perform transfer and return validation result
     */
    protected function checkTransferEligibility(User $user, float $amount, string $type = 'bank_transfer'): array
    {
        return $this->transferValidationService()->validateTransfer($user, $amount, $type);
    }

    /**
     * Get user's transfer eligibility summary
     */
    protected function getTransferEligibility(User $user): array
    {
        return $this->transferValidationService()->getTransferEligibility($user);
    }

    /**
     * Check if user needs to complete profile
     */
    protected function requiresProfileCompletion(User $user): bool
    {
        return $this->transferValidationService()->requiresProfileCompletion($user);
    }

    /**
     * Validate transfer request and fail if not eligible
     */
    protected function validateTransferRequest(User $user, float $amount, string $type = 'bank_transfer')
    {
        $validation = $this->checkTransferEligibility($user, $amount, $type);

        if (!$validation['allowed']) {
            $this->transferValidationService()->logTransferAttempt(
                $user, 
                $amount, 
                $type, 
                false,
                $validation['errors']
            );

            return response()->json([
                'success' => false,
                'message' => implode('. ', $validation['errors']),
                'errors' => $validation['errors'],
                'eligibility' => $this->getTransferEligibility($user),
            ], 422);
        }

        return null; // All checks passed
    }

    /**
     * Get user's profile completion percentage
     */
    protected function getProfileCompletionPercentage(User $user): int
    {
        return $this->transferValidationService()->getProfileCompletionPercentage($user);
    }

    /**
     * Log a transfer attempt for audit trail
     */
    protected function logTransferAttempt(User $user, float $amount, string $type, bool $allowed, array $reason = []): void
    {
        $this->transferValidationService()->logTransferAttempt($user, $amount, $type, $allowed, $reason);
    }
}
