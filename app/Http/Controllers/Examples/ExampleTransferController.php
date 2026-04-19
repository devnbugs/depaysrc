<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Http\Traits\ValidatesUserTransfers;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * ExampleTransferController
 * 
 * This is an EXAMPLE showing how to implement transfer validation
 * in your actual transfer controllers using the ValidatesUserTransfers trait.
 * 
 * IMPORTANT: This is NOT a real controller to be routed - it's a guide.
 * Copy the patterns to your actual transfer controller(s).
 */
class ExampleTransferController extends Controller
{
    use ValidatesUserTransfers;

    /**
     * Show transfer form
     */
    public function showTransferForm(Request $request)
    {
        $user = auth()->user();

        // Get user's transfer eligibility
        $eligibility = $this->getTransferEligibility($user);

        // Get profile completion percentage
        $profileCompletion = $this->getProfileCompletionPercentage($user);

        return view('transfer.form', [
            'user' => $user,
            'eligibility' => $eligibility,
            'profileCompletion' => $profileCompletion,
        ]);
    }

    /**
     * Initiate a bank transfer
     */
    public function initiateBankTransfer(Request $request)
    {
        $user = auth()->user();

        // Validate input
        $validated = $request->validate([
            'recipient_account' => 'required|string',
            'recipient_name' => 'required|string',
            'recipient_bank' => 'required|string',
            'amount' => 'required|numeric|min:100',
            'narration' => 'nullable|string|max:200',
        ]);

        // Check if user profile is complete
        if ($this->requiresProfileCompletion($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete your profile before transferring',
                'redirect_to_onboarding' => true,
                'onboarding_url' => route('user.onboarding'),
            ], 422);
        }

        // Validate transfer eligibility
        $validationResult = $this->validateTransferRequest($user, (float)$validated['amount'], 'bank_transfer');
        if ($validationResult) {
            return $validationResult; // Returns error response if not eligible
        }

        // All checks passed - proceed with transfer
        try {
            $transfer = Transfer::create([
                'user_id' => $user->id,
                'recipient_account' => $validated['recipient_account'],
                'recipient_name' => $validated['recipient_name'],
                'recipient_bank' => $validated['recipient_bank'],
                'amount' => $validated['amount'],
                'narration' => $validated['narration'] ?? null,
                'status' => 'pending',
            ]);

            // Log successful attempt
            $this->logTransferAttempt($user, (float)$validated['amount'], 'bank_transfer', true);

            // Process transfer (call your payment processor)
            // $this->processWithPaymentProvider($transfer);

            return response()->json([
                'success' => true,
                'message' => 'Transfer initiated successfully',
                'transfer_id' => $transfer->id,
                'status' => $transfer->status,
            ]);

        } catch (\Exception $e) {
            Log::error('Transfer initiation error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            // Log failed attempt
            $this->logTransferAttempt($user, (float)$validated['amount'], 'bank_transfer', false, [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Transfer initiation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a virtual account (requires full access)
     */
    public function createVirtualAccount(Request $request)
    {
        $user = auth()->user();

        // Validate account creation eligibility
        $validation = $this->transferValidationService()->validateAccountCreation($user);
        if (!$validation['allowed']) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot create account: ' . implode('. ', $validation['errors']),
                'errors' => $validation['errors'],
                'existing_accounts' => $validation['existing_accounts'],
                'account_limit' => $validation['limit'],
            ], 422);
        }

        // Proceed with account creation
        // ...

        return response()->json([
            'success' => true,
            'message' => 'Virtual account created successfully',
        ]);
    }

    /**
     * Get user's transfer summary/dashboard
     */
    public function getTransferSummary(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'user_id' => $user->id,
            'eligibility' => $this->getTransferEligibility($user),
            'profile_completion' => $this->getProfileCompletionPercentage($user),
            'needs_completion' => $this->requiresProfileCompletion($user),
            'onboarding_url' => $this->requiresProfileCompletion($user) ? route('user.onboarding') : null,
        ]);
    }

    /**
     * Check if specific amount can be transferred
     */
    public function checkTransferEligibility(Request $request)
    {
        $user = auth()->user();
        $amount = $request->input('amount', 0);
        $type = $request->input('type', 'bank_transfer');

        $result = $this->checkTransferEligibility($user, (float)$amount, $type);

        return response()->json($result);
    }

    /**
     * Get user's transfer limits and features
     */
    public function getLimitsAndFeatures(Request $request)
    {
        $user = auth()->user();
        $eligibility = $this->getTransferEligibility($user);

        return response()->json([
            'level' => $eligibility['level'],
            'level_code' => $eligibility['level_code'],
            'transfer_limit' => $eligibility['transfer_limit'],
            'daily_limit' => $eligibility['daily_limit'],
            'features' => [
                'can_transfer' => $eligibility['can_transfer'],
                'can_create_account' => $eligibility['can_create_account'],
                'full_features' => $eligibility['full_features_unlocked'],
            ],
            'kyc_requirements' => [
                'identity_verified' => $eligibility['identity_verified'],
                'liveness_verified' => $eligibility['liveness_verified'],
                'onboarding_complete' => $eligibility['onboarding_complete'],
            ],
            'deposit_status' => [
                'required' => $eligibility['deposit_required_for_level_3'],
                'amount_required' => $eligibility['deposit_amount'],
                'amount_deposited' => $eligibility['deposit_completed'],
                'amount_remaining' => $eligibility['deposit_remaining'],
            ],
        ]);
    }
}

/**
 * INTEGRATION GUIDE
 * ================
 * 
 * To use this in your actual controllers, follow these steps:
 * 
 * 1. Add the trait to your controller:
 *    use App\Http\Traits\ValidatesUserTransfers;
 * 
 * 2. In any method handling transfers, validate eligibility:
 *    $validationResult = $this->validateTransferRequest($user, $amount);
 *    if ($validationResult) return $validationResult;
 * 
 * 3. Use helper methods for checks:
 *    - $this->getTransferEligibility($user)
 *    - $this->checkTransferEligibility($user, $amount)
 *    - $this->requiresProfileCompletion($user)
 *    - $this->getProfileCompletionPercentage($user)
 * 
 * 4. Log transfer attempts:
 *    $this->logTransferAttempt($user, $amount, $type, $allowed);
 * 
 * Example in your TransferController:
 * 
 *    class TransferController extends Controller
 *    {
 *        use ValidatesUserTransfers;
 *        
 *        public function store(Request $request)
 *        {
 *            $user = auth()->user();
 *            $amount = $request->amount;
 *            
 *            // Validate
 *            $validation = $this->validateTransferRequest($user, $amount);
 *            if ($validation) return $validation;
 *            
 *            // Process transfer...
 *        }
 *    }
 * 
 * That's it! Your transfer endpoints are now KYC-aware.
 */
