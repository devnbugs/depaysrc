<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\Api\AuthenticationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * API Authentication Controller
 * 
 * Handles user authentication, registration, and token management
 * Used by Flutter and other API clients
 */
class ApiAuthController extends Controller
{
    use ApiResponse;

    protected AuthenticationService $authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * User Login
     * 
     * POST /api/v1/auth/login
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $this->authService->login(
            $request->input('username'),
            $request->input('password')
        );

        if (!$result['success']) {
            $httpCode = $request->has('requires_verification') ? 422 : 401;
            return $this->error($result['message'], 401, $httpCode);
        }

        return $this->success($result, $result['message'], 200, 200);
    }

    /**
     * User Registration
     * 
     * POST /api/v1/auth/register
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $result = $this->authService->register($request->all());

        if (!$result['success']) {
            return $this->error(
                $result['message'],
                422,
                422,
                $result['errors'] ?? null
            );
        }

        return $this->created($result, $result['message']);
    }

    /**
     * User Logout
     * 
     * POST /api/v1/auth/logout
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        $this->authService->logout($user);

        return $this->success(null, 'Logged out successfully');
    }

    /**
     * Refresh Access Token
     * 
     * POST /api/v1/auth/refresh-token
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        $token = $this->authService->refreshToken($user);

        return $this->success([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'Token refreshed successfully');
    }

    /**
     * Change Password
     * 
     * POST /api/v1/auth/change-password
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        $result = $this->authService->changePassword(
            $user,
            $request->input('current_password'),
            $request->input('new_password')
        );

        if (!$result['success']) {
            return $this->error($result['message'], 422, 422);
        }

        return $this->success(null, $result['message']);
    }

    /**
     * Get Current User
     * 
     * GET /api/v1/auth/me
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getCurrentUser(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'balance' => (float) $user->balance,
            'email_verified' => !is_null($user->email_verified_at),
            'avatar' => $user->image ? asset('assets/images/user/profile/' . $user->image) : null,
        ], 'User profile retrieved successfully');
    }
}
