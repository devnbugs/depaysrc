<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Authentication Service
 * 
 * Handles user authentication, token generation, and authorization
 * Used by API controllers for secure user operations
 */
class AuthenticationService
{
    /**
     * Authenticate user and generate token
     * 
     * @param string $username Email or username
     * @param string $password
     * @return array
     */
    public function login(string $username, string $password): array
    {
        // Find user by email or username
        $user = User::where('email', $username)
            ->orWhere('username', $username)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
                'user' => null,
                'token' => null,
            ];
        }

        // Check if user is verified
        if (!$user->email_verified_at) {
            return [
                'success' => false,
                'message' => 'Please verify your email first',
                'user' => null,
                'token' => null,
                'requires_verification' => true,
            ];
        }

        // Generate token
        $token = $user->createToken('api_token')->plainTextToken;

        // Record login
        $this->recordLogin($user);

        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'balance' => (float) $user->balance,
                'email_verified' => !is_null($user->email_verified_at),
                'sms_verified' => (int) $user->sms_verified_at ? true : false,
                'kyc_status' => $user->kycVerified,
                'avatar' => $user->image ? asset('assets/images/user/profile/' . $user->image) : null,
            ],
            'token' => $token,
        ];
    }

    /**
     * Register new user
     * 
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        // Validate input
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'string', 'unique:users,username', 'min:3', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        try {
            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'balance' => 0,
                'status' => 'active',
            ]);

            // Generate token
            $token = $user->createToken('api_token')->plainTextToken;

            return [
                'success' => true,
                'message' => 'Registration successful. Please verify your email.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                ],
                'token' => $token,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Logout user and revoke token
     * 
     * @param User $user
     * @return bool
     */
    public function logout(User $user): bool
    {
        try {
            $user->currentAccessToken()->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Refresh token
     * 
     * @param User $user
     * @return string
     */
    public function refreshToken(User $user): string
    {
        $user->tokens()->delete();
        return $user->createToken('api_token')->plainTextToken;
    }

    /**
     * Record user login
     * 
     * @param User $user
     * @return void
     */
    private function recordLogin(User $user): void
    {
        try {
            $user->update(['last_login_at' => now()]);
        } catch (\Exception $e) {
            // Silent fail for login recording
        }
    }

    /**
     * Change password
     * 
     * @param User $user
     * @param string $currentPassword
     * @param string $newPassword
     * @return array
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): array
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return [
                'success' => false,
                'message' => 'Current password is incorrect',
            ];
        }

        $user->update(['password' => Hash::make($newPassword)]);

        return [
            'success' => true,
            'message' => 'Password changed successfully',
        ];
    }

    /**
     * Verify email token
     * 
     * @param string $token
     * @return array
     */
    public function verifyEmailToken(string $token): array
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid verification token',
            ];
        }

        if (now()->diffInMinutes($user->email_verification_sent_at) > 1440) {
            return [
                'success' => false,
                'message' => 'Verification token expired',
            ];
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ]);

        return [
            'success' => true,
            'message' => 'Email verified successfully',
        ];
    }
}
