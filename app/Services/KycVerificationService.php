<?php

namespace App\Services;

use App\Models\User;
use App\Models\Deposit;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * KYC Verification Service
 * 
 * Handles complete onboarding and KYC verification flow including:
 * - Basic profile verification (BVN/NIN, Address, WhatsApp)
 * - Auto KYC via Kora Identity
 * - Kora Liveness verification
 * - Tier/Level progression based on deposits and verification
 * - User limits based on verification level
 */
class KycVerificationService
{
    // KYC Verification Levels
    const LEVEL_NONE = 0;
    const LEVEL_BASIC = 1;        // Name, Email, Phone verified
    const LEVEL_ADVANCED = 2;     // BVN/NIN verified via Kora
    const LEVEL_PREMIUM = 3;      // Liveness verified + ₦400 deposit

    // Onboarding Steps
    const STEP_PERSONAL_INFO = 'personal_info';
    const STEP_IDENTITY_VERIFICATION = 'identity_verification';
    const STEP_LIVENESS_CHECK = 'liveness_check';
    const STEP_COMPLETED = 'completed';

    // Default Limits by Level
    private $levelLimits = [
        self::LEVEL_NONE => [
            'transfer_limit' => 10000,           // ₦10,000
            'daily_limit' => 10000,
            'account_creation_limit' => 0,        // Cannot create accounts
            'can_transfer' => false,
            'can_receive' => true,
            'full_features' => false,
        ],
        self::LEVEL_BASIC => [
            'transfer_limit' => 50000,           // ₦50,000
            'daily_limit' => 50000,
            'account_creation_limit' => 1,
            'can_transfer' => true,
            'can_receive' => true,
            'full_features' => false,
        ],
        self::LEVEL_ADVANCED => [
            'transfer_limit' => 500000,          // ₦500,000
            'daily_limit' => 500000,
            'account_creation_limit' => 2,
            'can_transfer' => true,
            'can_receive' => true,
            'full_features' => false,
        ],
        self::LEVEL_PREMIUM => [
            'transfer_limit' => 5000000,         // ₦5,000,000
            'daily_limit' => 5000000,
            'account_creation_limit' => 5,
            'can_transfer' => true,
            'can_receive' => true,
            'full_features' => true,             // Full access to all features
        ],
    ];

    public function __construct(protected KoraService $kora)
    {
    }

    /**
     * Check if user needs to complete onboarding
     */
    public function needsOnboarding(User $user): bool
    {
        return $user->kyc_verification_level === self::LEVEL_NONE ||
               blank($user->onboarding_completed_at) ||
               $user->onboarding_step !== self::STEP_COMPLETED;
    }

    /**
     * Get current onboarding step for user
     */
    public function getCurrentStep(User $user): string
    {
        return $user->onboarding_step ?? self::STEP_PERSONAL_INFO;
    }

    /**
     * Get required fields for onboarding
     */
    public function getRequiredFields(): array
    {
        return [
            'firstname' => ['label' => 'First Name', 'type' => 'text', 'required' => true],
            'lastname' => ['label' => 'Last Name', 'type' => 'text', 'required' => true],
            'mobile' => ['label' => 'Phone Number', 'type' => 'tel', 'required' => true],
            'whatsapp_phone' => ['label' => 'WhatsApp Number', 'type' => 'tel', 'required' => true],
            'address' => ['label' => 'Physical Address', 'type' => 'text', 'required' => true],
        ];
    }

    /**
     * Submit personal information in onboarding
     */
    public function submitPersonalInfo(User $user, array $data): void
    {
        $validated = [
            'firstname' => $data['firstname'] ?? null,
            'lastname' => $data['lastname'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'whatsapp_phone' => $data['whatsapp_phone'] ?? null,
        ];

        // Parse address
        if (isset($data['address'])) {
            $validated['address'] = [
                'address' => $data['address']['address'] ?? '',
                'state' => $data['address']['state'] ?? '',
                'city' => $data['address']['city'] ?? '',
                'zip' => $data['address']['zip'] ?? '',
                'country' => $data['address']['country'] ?? 'Nigeria',
            ];
        }

        $user->forceFill($validated)->save();

        // Update onboarding step
        $user->update([
            'onboarding_step' => self::STEP_IDENTITY_VERIFICATION,
        ]);

        Log::info("User {$user->id} completed personal info step");
    }

    /**
     * Perform identity verification via Kora (BVN/NIN)
     */
    public function verifyIdentity(User $user, string $bvnOrNin, string $type = 'bvn'): array
    {
        $secretKey = config('services.kora.secret_key');
        if (!filled($secretKey)) {
            throw new RuntimeException('Kora identity verification is not configured.');
        }

        try {
            if ($type === 'nin') {
                $response = $this->kora->verifyNin($bvnOrNin, $secretKey);
            } else {
                $response = $this->kora->verifyBvn($bvnOrNin, $secretKey);
            }

            $body = $response->json() ?? [];

            if (!$response->successful() || !data_get($body, 'status')) {
                throw new RuntimeException(data_get($body, 'message', 'Identity verification failed.'));
            }

            $data = (array) data_get($body, 'data', []);

            // Update user with verified identity
            $user->forceFill([
                'firstname' => data_get($data, 'first_name', $user->firstname),
                'lastname' => data_get($data, 'last_name', $user->lastname),
                'identity_middle_name' => data_get($data, 'middle_name'),
                'identity_gender' => data_get($data, 'gender'),
                'identity_date_of_birth' => data_get($data, 'date_of_birth'),
                $type === 'nin' ? 'NIN' : 'BVN' => $bvnOrNin,
                'identity_source' => $type,
                'identity_payload' => $data,
                'identity_verified_at' => now(),
                'kyc_verification_level' => self::LEVEL_ADVANCED,
                'onboarding_step' => self::STEP_LIVENESS_CHECK,
            ])->save();

            Log::info("User {$user->id} verified identity via {$type}");

            return [
                'success' => true,
                'message' => 'Identity verified successfully',
                'data' => $data,
            ];
        } catch (\Exception $e) {
            Log::error("Identity verification failed for user {$user->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Initialize Kora Liveness verification
     */
    public function initiateLivenessCheck(User $user): array
    {
        if ($user->kyc_verification_level < self::LEVEL_ADVANCED) {
            throw new RuntimeException('User must complete identity verification first.');
        }

        $secretKey = config('services.kora.secret_key');
        if (!filled($secretKey)) {
            throw new RuntimeException('Kora liveness verification is not configured.');
        }

        try {
            // Initiate liveness check with Kora
            $response = $this->kora->initiateLiveness(
                $user->id,
                $user->firstname . ' ' . $user->lastname,
                $secretKey
            );

            $body = $response->json() ?? [];

            if (!$response->successful() || !data_get($body, 'status')) {
                throw new RuntimeException(data_get($body, 'message', 'Failed to initiate liveness check.'));
            }

            $livenessId = data_get($body, 'data.id') ?? data_get($body, 'data.liveness_id');

            // Store liveness reference
            $user->update([
                'kora_liveness_id' => $livenessId,
                'kora_liveness_status' => 'pending',
            ]);

            Log::info("Liveness check initiated for user {$user->id} with ID {$livenessId}");

            return [
                'success' => true,
                'liveness_id' => $livenessId,
                'redirect_url' => data_get($body, 'data.redirect_url') ?? null,
            ];
        } catch (\Exception $e) {
            Log::error("Liveness initiation failed for user {$user->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify liveness completion and upgrade to Level 3
     */
    public function verifyLivenessCompletion(User $user): array
    {
        if (!$user->kora_liveness_id) {
            throw new RuntimeException('No liveness check in progress.');
        }

        $secretKey = config('services.kora.secret_key');

        try {
            // Check liveness status with Kora
            $response = $this->kora->checkLivenessStatus(
                $user->kora_liveness_id,
                $secretKey
            );

            $body = $response->json() ?? [];

            if (!$response->successful()) {
                throw new RuntimeException(data_get($body, 'message', 'Failed to check liveness status.'));
            }

            $status = data_get($body, 'data.status') ?? data_get($body, 'status');

            if ($status !== 'completed' && $status !== 'approved') {
                return [
                    'success' => false,
                    'status' => $status,
                    'message' => 'Liveness verification not yet completed.',
                ];
            }

            // Liveness verified successfully
            $user->update([
                'kyc_liveness_verified' => true,
                'kyc_liveness_verified_at' => now(),
                'kora_liveness_status' => 'completed',
                'kyc_verification_level' => self::LEVEL_PREMIUM,
                'onboarding_step' => self::STEP_COMPLETED,
                'onboarding_completed_at' => now(),
            ]);

            Log::info("User {$user->id} completed liveness verification and reached Level 3");

            return [
                'success' => true,
                'message' => 'Liveness verification completed successfully',
                'level' => self::LEVEL_PREMIUM,
                'limits' => $this->getUserLimits($user->refresh()),
            ];
        } catch (\Exception $e) {
            Log::error("Liveness verification failed for user {$user->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if user meets deposit requirement for Level 3
     */
    public function checkDepositRequirement(User $user): array
    {
        $requirement = $user->deposit_requirement_for_level_3;
        $deposited = Deposit::where('user_id', $user->id)
            ->where('status', 1)
            ->sum('amount');

        $user->update(['total_deposited' => $deposited]);

        $metRequirement = $deposited >= $requirement;

        return [
            'required' => $requirement,
            'deposited' => (float) $deposited,
            'remaining' => max(0, $requirement - $deposited),
            'met' => $metRequirement,
            'percentage' => round(($deposited / $requirement) * 100, 2),
        ];
    }

    /**
     * Auto-upgrade user level if deposit requirement is met
     */
    public function upgradeIfDepositMet(User $user): bool
    {
        $requirement = $this->checkDepositRequirement($user);

        if ($requirement['met'] && $user->kyc_verification_level === self::LEVEL_ADVANCED) {
            // If user has completed liveness, upgrade to Level 3
            if ($user->kyc_liveness_verified) {
                $user->update(['kyc_verification_level' => self::LEVEL_PREMIUM]);
                Log::info("User {$user->id} auto-upgraded to Level 3 based on deposit");
                return true;
            }
        }

        return false;
    }

    /**
     * Get user's current verification level
     */
    public function getUserLevel(User $user): int
    {
        return $user->kyc_verification_level ?? self::LEVEL_NONE;
    }

    /**
     * Get limits for user's current level
     */
    public function getUserLimits(User $user): array
    {
        $level = $this->getUserLevel($user);
        $limits = $this->levelLimits[$level] ?? $this->levelLimits[self::LEVEL_NONE];

        return array_merge($limits, [
            'level' => $level,
            'level_name' => $this->getLevelName($level),
        ]);
    }

    /**
     * Get user limits for specific level
     */
    public function getLimitsForLevel(int $level): array
    {
        $limits = $this->levelLimits[$level] ?? $this->levelLimits[self::LEVEL_NONE];

        return array_merge($limits, [
            'level' => $level,
            'level_name' => $this->getLevelName($level),
        ]);
    }

    /**
     * Get human-readable level name
     */
    public function getLevelName(int $level): string
    {
        return match($level) {
            self::LEVEL_NONE => 'Unverified',
            self::LEVEL_BASIC => 'Basic',
            self::LEVEL_ADVANCED => 'Advanced',
            self::LEVEL_PREMIUM => 'Premium (Level 3)',
            default => 'Unknown',
        };
    }

    /**
     * Check if user can perform transfer
     */
    public function canTransfer(User $user, float $amount = 0): array
    {
        $limits = $this->getUserLimits($user);

        $canTransfer = $limits['can_transfer'] ?? false;
        $withinLimit = $amount <= $limits['transfer_limit'];

        return [
            'allowed' => $canTransfer && $withinLimit,
            'reason' => !$canTransfer ? 'Your verification level does not allow transfers' :
                       (!$withinLimit ? "Amount exceeds your limit of ₦{$limits['transfer_limit']}" : 'Allowed'),
            'limit' => $limits['transfer_limit'],
            'level' => $limits['level'],
        ];
    }

    /**
     * Check if user can create account
     */
    public function canCreateAccount(User $user): array
    {
        $limits = $this->getUserLimits($user);
        $existingAccounts = $user->linkedAccounts()->count() ?? 0;

        $canCreate = $existingAccounts < $limits['account_creation_limit'];

        return [
            'allowed' => $canCreate,
            'reason' => !$canCreate ? "You can only create {$limits['account_creation_limit']} account(s) at your level" : 'Allowed',
            'existing' => $existingAccounts,
            'limit' => $limits['account_creation_limit'],
            'level' => $limits['level'],
        ];
    }

    /**
     * Check if user has full feature access
     */
    public function hasFullAccess(User $user): bool
    {
        $limits = $this->getUserLimits($user);
        return $limits['full_features'] ?? false;
    }

    /**
     * Get onboarding progress
     */
    public function getOnboardingProgress(User $user): array
    {
        $steps = [
            self::STEP_PERSONAL_INFO => 'Personal Information',
            self::STEP_IDENTITY_VERIFICATION => 'Identity Verification',
            self::STEP_LIVENESS_CHECK => 'Liveness Verification',
            self::STEP_COMPLETED => 'Completed',
        ];

        $currentStep = $user->onboarding_step ?? self::STEP_PERSONAL_INFO;
        $currentStepIndex = array_search($currentStep, array_keys($steps));
        $totalSteps = count($steps);
        $percentage = round((($currentStepIndex + 1) / $totalSteps) * 100);

        return [
            'current_step' => $currentStep,
            'steps' => $steps,
            'completed_steps' => array_slice(array_keys($steps), 0, $currentStepIndex + 1),
            'progress_percentage' => $percentage,
            'is_complete' => $currentStep === self::STEP_COMPLETED,
        ];
    }
}
