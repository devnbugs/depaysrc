<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPaymentRequest;
use App\Http\Requests\PaymentConfirmationRequest;
use App\Services\PaymentProcessingManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Handle Payment Confirmations with PIN Verification
 *
 * This controller processes payment confirmations from the popup modal,
 * validates the PIN, and dispatches to either Queue or Spatie Async processor.
 * 
 * Processor is determined by config('services.payment.processor')
 */
class PaymentConfirmationController extends Controller
{
    /**
     * Confirm payment with PIN verification
     */
    public function confirm(PaymentConfirmationRequest $request): JsonResponse
    {
        $user = Auth::user();

        // Verify PIN if enabled
        if ((int) $user->pin_state === 1) {
            if (! hash_equals((string) $user->pin, (string) $request->pin_code)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect PIN. Please try again.',
                    'field' => 'pin_code',
                ], 422);
            }
        } elseif ((int) $user->two_factor_enabled === 1) {
            // Verify 2FA if PIN is disabled
            $response = verifyG2fa($user, $request->authenticator_code);
            if (! $response) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid 2FA code. Please try again.',
                    'field' => 'authenticator_code',
                ], 422);
            }
        }

        // Verify final balance check
        $amount = (float) $request->amount;
        if ($user->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance. Please check your wallet.',
            ], 422);
        }

        // Dispatch payment using configured processor (queue or async)
        try {
            $manager = new PaymentProcessingManager();
            $result = $manager->dispatch($user, $request->getPaymentData());

            return response()->json([
                'success' => true,
                'message' => 'Payment processing started. You will receive a confirmation shortly.',
                'redirect' => route('user.bills'),
                'processor' => $result['processor'],
                'process_id' => $result['process_id'] ?? null,
            ]);

        } catch (\Throwable $e) {
            \Log::error('Payment confirmation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your payment. Please try again.',
            ], 500);
        }
    }

    /**
     * Validate payment details before showing confirmation
     */
    public function validatePayment(Request $request): JsonResponse
    {
        $user = Auth::user();
        $validated = $request->validate([
            'type' => ['required', 'integer', 'in:1,2,3,4'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        // Check balance
        if ($user->balance < $validated['amount']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance for this transaction.',
            ], 422);
        }

        // Check rate limiting
        $recentPayments = \App\Models\Bill::whereUserId($user->id)
            ->where('created_at', '>=', now()->subHours(1))
            ->count();

        if ($recentPayments > 20) {
            return response()->json([
                'success' => false,
                'message' => 'Too many payment requests. Please wait before trying again.',
            ], 429);
        }

        return response()->json([
            'success' => true,
            'requirePin' => (int) $user->pin_state === 1,
            'require2fa' => (int) $user->two_factor_enabled === 1,
        ]);
    }
}
