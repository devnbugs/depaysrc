<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\KycVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * OnboardingController
 * 
 * Handles the complete user onboarding flow including:
 * - Personal information submission
 * - Identity verification (BVN/NIN)
 * - Liveness check
 * - Verification level progression
 */
class OnboardingController extends Controller
{
    public function __construct(protected KycVerificationService $kycService)
    {
        $this->middleware('auth');
    }

    /**
     * Show onboarding dashboard with current progress
     */
    public function show(Request $request): View
    {
        $user = auth()->user();
        $progress = $this->kycService->getOnboardingProgress($user);
        $currentStep = $this->kycService->getCurrentStep($user);

        return view('onboarding.index', [
            'user' => $user,
            'progress' => $progress,
            'currentStep' => $currentStep,
            'limits' => $this->kycService->getUserLimits($user),
            'depositRequirement' => $this->kycService->checkDepositRequirement($user),
        ]);
    }

    /**
     * Show personal information form
     */
    public function showPersonalInfoForm(Request $request): View
    {
        $user = auth()->user();

        return view('onboarding.personal-info', [
            'user' => $user,
            'requiredFields' => $this->kycService->getRequiredFields(),
            'progress' => $this->kycService->getOnboardingProgress($user),
        ]);
    }

    /**
     * Submit personal information
     */
    public function submitPersonalInfo(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'firstname' => 'required|string|min:2|max:50',
            'lastname' => 'required|string|min:2|max:50',
            'mobile' => 'required|string|min:10|max:15',
            'whatsapp_phone' => 'required|string|min:10|max:15',
            'address.address' => 'required|string|min:10|max:255',
            'address.state' => 'required|string|max:50',
            'address.city' => 'required|string|max:50',
            'address.zip' => 'nullable|string|max:10',
            'address.country' => 'required|string|max:50',
        ]);

        try {
            $this->kycService->submitPersonalInfo($user, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Personal information saved successfully',
                'next_step' => 'identity_verification',
                'redirect_url' => route('user.onboarding.identity-verification'),
            ]);
        } catch (\Exception $e) {
            Log::error('Personal info submission error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Show identity verification form (BVN/NIN)
     */
    public function showIdentityVerificationForm(Request $request): View
    {
        $user = auth()->user();
        $progress = $this->kycService->getOnboardingProgress($user);

        return view('onboarding.identity-verification', [
            'user' => $user,
            'progress' => $progress,
            'depositRequirement' => $this->kycService->checkDepositRequirement($user),
        ]);
    }

    /**
     * Submit identity verification (BVN or NIN)
     */
    public function submitIdentityVerification(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'identification_type' => 'required|in:bvn,nin',
            'identification_number' => 'required|string|min:10|max:20',
        ]);

        try {
            $result = $this->kycService->verifyIdentity(
                $user,
                $validated['identification_number'],
                $validated['identification_type']
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Identity verified successfully',
                    'next_step' => 'liveness_check',
                    'data' => $result['data'],
                    'redirect_url' => route('user.onboarding.liveness-check'),
                ]);
            }

            return response()->json(['success' => false, 'message' => $result['message']], 422);
        } catch (\Exception $e) {
            Log::error('Identity verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Show liveness check form
     */
    public function showLivenessCheckForm(Request $request): View
    {
        $user = auth()->user()->refresh();
        $progress = $this->kycService->getOnboardingProgress($user);

        if ($user->kyc_verification_level < KycVerificationService::LEVEL_ADVANCED) {
            return view('onboarding.incomplete', [
                'message' => 'Please complete identity verification first.',
            ]);
        }

        $livenessData = null;
        $livenessInitiated = false;

        if ($user->kora_liveness_id && $user->kora_liveness_status !== 'completed') {
            $livenessInitiated = true;
        }

        return view('onboarding.liveness-check', [
            'user' => $user,
            'progress' => $progress,
            'livenessInitiated' => $livenessInitiated,
            'livenessData' => $livenessData,
            'depositRequirement' => $this->kycService->checkDepositRequirement($user),
        ]);
    }

    /**
     * Initiate liveness check with Kora
     */
    public function initiateLivenessCheck(Request $request)
    {
        $user = auth()->user();

        try {
            $result = $this->kycService->initiateLivenessCheck($user);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Liveness check initiated',
                    'liveness_id' => $result['liveness_id'],
                    'redirect_url' => $result['redirect_url'] ?? null,
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Failed to initiate liveness check'], 422);
        } catch (\Exception $e) {
            Log::error('Liveness initiation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Check liveness completion status
     */
    public function checkLivenessStatus(Request $request)
    {
        $user = auth()->user()->refresh();

        try {
            $result = $this->kycService->verifyLivenessCompletion($user);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Liveness verification completed',
                    'level' => $result['level'],
                    'limits' => $result['limits'],
                    'redirect_url' => route('user.onboarding.complete'),
                ]);
            }

            return response()->json([
                'success' => false,
                'status' => $result['status'] ?? 'pending',
                'message' => $result['message'],
            ]);
        } catch (\Exception $e) {
            Log::error('Liveness status check error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Liveness callback (from Kora)
     */
    public function livenessCallback(Request $request)
    {
        $user = auth()->user();
        $livenessId = $request->input('liveness_id');
        $status = $request->input('status');

        try {
            if ($user->kora_liveness_id === $livenessId) {
                $user->update([
                    'kora_liveness_status' => $status,
                ]);

                Log::info("Liveness callback for user {$user->id}: {$status}");

                return response()->json(['success' => true, 'message' => 'Callback processed']);
            }

            return response()->json(['success' => false, 'message' => 'Invalid liveness ID'], 400);
        } catch (\Exception $e) {
            Log::error('Liveness callback error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Callback processing failed'], 422);
        }
    }

    /**
     * Show onboarding completion page
     */
    public function complete(Request $request): View
    {
        $user = auth()->user()->refresh();
        $progress = $this->kycService->getOnboardingProgress($user);
        $limits = $this->kycService->getUserLimits($user);
        $depositReq = $this->kycService->checkDepositRequirement($user);

        return view('onboarding.complete', [
            'user' => $user,
            'progress' => $progress,
            'limits' => $limits,
            'depositRequirement' => $depositReq,
            'isPremiumLevel' => $user->kyc_verification_level === KycVerificationService::LEVEL_PREMIUM,
        ]);
    }

    /**
     * Skip to next step (for testing/debugging)
     */
    public function skipStep(Request $request)
    {
        if (!app()->environment('local', 'staging')) {
            return response()->json(['success' => false, 'message' => 'Not available in production'], 403);
        }

        $user = auth()->user();
        $currentStep = $this->kycService->getCurrentStep($user);

        $stepOrder = [
            KycVerificationService::STEP_PERSONAL_INFO,
            KycVerificationService::STEP_IDENTITY_VERIFICATION,
            KycVerificationService::STEP_LIVENESS_CHECK,
            KycVerificationService::STEP_COMPLETED,
        ];

        $currentIndex = array_search($currentStep, $stepOrder);
        if ($currentIndex !== false && $currentIndex < count($stepOrder) - 1) {
            $nextStep = $stepOrder[$currentIndex + 1];
            $user->update(['onboarding_step' => $nextStep]);

            if ($nextStep === KycVerificationService::STEP_COMPLETED) {
                $user->update([
                    'kyc_verification_level' => KycVerificationService::LEVEL_PREMIUM,
                    'kyc_liveness_verified' => true,
                    'onboarding_completed_at' => now(),
                ]);
            }

            return response()->json(['success' => true, 'next_step' => $nextStep]);
        }

        return response()->json(['success' => false, 'message' => 'Cannot skip further'], 422);
    }
}
