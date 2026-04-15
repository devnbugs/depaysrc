<?php

namespace App\Http\Controllers;

use App\Services\PaymentAuthenticationService;
use App\Models\AuthenticationVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentVerificationController extends Controller
{
    /**
     * Verify authentication for payment
     */
    public function verify(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Validate request
        $method = $request->input('method');
        
        if (!in_array($method, ['pin', '2fa', 'passkey'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid authentication method'
            ], 400);
        }

        $verified = false;
        $error = null;

        try {
            switch ($method) {
                case 'pin':
                    $pin = $request->input('pin');

                    if (empty($pin) || strlen($pin) !== 4) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid PIN format'
                        ], 400);
                    }

                    // Check if PIN is locked
                    if ($user->isPinLocked()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Too many failed attempts. Please try again later.'
                        ], 429);
                    }

                    $verified = PaymentAuthenticationService::verifyPin($user, $pin);
                    $error = $verified ? null : 'Invalid PIN';
                    break;

                case '2fa':
                    $code = $request->input('two_fa_code');

                    if (empty($code) || strlen($code) !== 6 || !ctype_digit($code)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid code format. Please enter a 6-digit code.'
                        ], 400);
                    }

                    $verified = PaymentAuthenticationService::verify2FA($user, $code);
                    $error = $verified ? null : 'Invalid or expired code';
                    break;

                case 'passkey':
                    $credentialId = $request->input('credential_id');

                    if (empty($credentialId)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Passkey authentication failed'
                        ], 400);
                    }

                    $verified = PaymentAuthenticationService::verifyPasskey($user, $credentialId);
                    $error = $verified ? null : 'Passkey verification failed';
                    break;
            }
        } catch (\Exception $e) {
            \Log::error('Payment authentication error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during verification'
            ], 500);
        }

        // Create verification record
        $verification = PaymentAuthenticationService::createVerification(
            $method,
            'payment',
            $request->input('reference_id')
        );

        if ($verified) {
            $verification->markAsVerified();
            
            // Log successful verification
            \Log::info("Payment authentication successful for user {$user->id} using {$method}");

            return response()->json([
                'success' => true,
                'message' => 'Authentication successful'
            ]);
        } else {
            $verification->markAsFailed();
            
            // Log failed verification
            \Log::warning("Payment authentication failed for user {$user->id} using {$method}");

            return response()->json([
                'success' => false,
                'message' => $error ?? 'Authentication failed'
            ], 401);
        }
    }

    /**
     * Get authentication status for payment
     */
    public function getStatus(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'authenticated' => false
            ], 401);
        }

        // Check if payment auth is required
        $requiresAuth = PaymentAuthenticationService::requiresAuthentication($user, 'payment');

        if (!$requiresAuth) {
            return response()->json([
                'method' => 'none',
                'authenticated' => true,
                'message' => 'No authentication required'
            ]);
        }

        // Determine which methods are available
        $availableMethods = [];
        
        if ($user->isPinEnabled()) {
            $availableMethods[] = 'pin';
        }

        if ($user->isTwoFactorEnabled()) {
            $availableMethods[] = '2fa';
        }

        if ($user->isPasskeyEnabled()) {
            $availableMethods[] = 'passkey';
        }

        // Determine if PIN is required based on 2FA status
        $pinRequired = PaymentAuthenticationService::isPinRequiredForPayment($user);

        return response()->json([
            'requires_auth' => true,
            'available_methods' => $availableMethods,
            'pinRequired' => $pinRequired,
            'message' => 'Authentication required for payment'
        ]);
    }

    /**
     * Get recent verification attempts
     */
    public function getRecent(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([], 401);
        }

        $verifications = $user->authenticationVerifications()
            ->where('context', 'payment')
            ->orderBy('attempted_at', 'desc')
            ->limit(10)
            ->get(['type', 'status', 'attempted_at']);

        return response()->json($verifications);
    }
}
