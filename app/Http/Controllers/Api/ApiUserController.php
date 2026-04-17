<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\Api\UserProfileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * API User Profile Controller
 * 
 * Handles user profile operations, wallet info, and verification status
 */
class ApiUserController extends Controller
{
    use ApiResponse;

    protected UserProfileService $profileService;

    public function __construct(UserProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Get User Profile
     * 
     * GET /api/v1/user/profile
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        $profile = $this->profileService->getProfile($user);

        return $this->success($profile, 'User profile retrieved successfully');
    }

    /**
     * Update User Profile
     * 
     * PUT /api/v1/user/profile
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        $result = $this->profileService->updateProfile($user, $request->all());

        if (!$result['success']) {
            return $this->error(
                $result['message'],
                422,
                422,
                $result['errors'] ?? null
            );
        }

        return $this->success($result['data'], $result['message']);
    }

    /**
     * Get Wallet Information
     * 
     * GET /api/v1/user/wallet
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getWallet(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        $wallet = $this->profileService->getWalletInfo($user);

        return $this->success($wallet, 'Wallet information retrieved successfully');
    }

    /**
     * Get Verification Status
     * 
     * GET /api/v1/user/verification
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getVerificationStatus(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        $status = $this->profileService->getVerificationStatus($user);

        return $this->success($status, 'Verification status retrieved successfully');
    }

    /**
     * Get Transaction History
     * 
     * GET /api/v1/user/transactions
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getTransactions(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        $limit = $request->input('limit', 50);
        $transactions = $this->profileService->getTransactionHistory($user, $limit);

        return $this->success($transactions, 'Transaction history retrieved successfully');
    }

    /**
     * Check Action Permission
     * 
     * POST /api/v1/user/check-permission
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function checkPermission(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        $request->validate([
            'action' => 'required|string',
        ]);

        $permission = $this->profileService->checkActionPermission(
            $user,
            $request->input('action')
        );

        return $this->success($permission, 'Permission check completed');
    }
}
