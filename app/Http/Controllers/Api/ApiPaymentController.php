<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\Api\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * API Payment Controller
 * 
 * Handles payment processing, bill payments, and transaction history
 */
class ApiPaymentController extends Controller
{
    use ApiResponse;

    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Get Available Payment Options
     * 
     * GET /api/v1/payments/options
     * 
     * @return JsonResponse
     */
    public function getPaymentOptions(): JsonResponse
    {
        $options = $this->paymentService->getPaymentOptions();

        return $this->success($options, 'Payment options retrieved successfully');
    }

    /**
     * Get Payment Networks
     * 
     * GET /api/v1/payments/networks
     * 
     * @return JsonResponse
     */
    public function getNetworks(): JsonResponse
    {
        $networks = $this->paymentService->getNetworks();

        return $this->success($networks, 'Networks retrieved successfully');
    }

    /**
     * Validate Payment
     * 
     * POST /api/v1/payments/validate
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function validatePayment(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        $validation = $this->paymentService->validatePayment($user, $request->all());

        if (!$validation['valid']) {
            return $this->error(
                $validation['error'] ?? 'Validation failed',
                422,
                422,
                $validation['errors'] ?? null
            );
        }

        return $this->success([
            'valid' => true,
            'message' => 'Payment data is valid',
        ], 'Payment validated successfully');
    }

    /**
     * Process Payment
     * 
     * POST /api/v1/payments/process
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function processPayment(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        $request->validate([
            'type' => 'required|integer|in:1,2,3,4',
            'phone' => 'required|string',
            'amount' => 'required|numeric|min:100|max:100000',
            'network' => 'required_if:type,1,2|string',
        ]);

        $result = $this->paymentService->processPayment($user, $request->all());

        if (!$result['success']) {
            return $this->error(
                $result['message'],
                400,
                400,
                $result['errors'] ?? null
            );
        }

        return $this->success($result['data'], $result['message']);
    }

    /**
     * Get Payment History
     * 
     * GET /api/v1/payments/history
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getPaymentHistory(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        $limit = $request->input('limit', 50);
        $history = $this->paymentService->getPaymentHistory($user, $limit);

        return $this->success($history, 'Payment history retrieved successfully');
    }

    /**
     * Get Payment Details
     * 
     * GET /api/v1/payments/{reference}
     * 
     * @param Request $request
     * @param string $reference
     * @return JsonResponse
     */
    public function getPaymentDetails(Request $request, string $reference): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        $result = $this->paymentService->getPaymentDetails($reference, $user);

        if (!$result['found']) {
            return $this->notFound($result['message']);
        }

        return $this->success($result['data'], 'Payment details retrieved successfully');
    }

    /**
     * Get Payment Statistics
     * 
     * GET /api/v1/payments/statistics
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized();
        }

        $statistics = $this->paymentService->getPaymentStatistics($user);

        return $this->success($statistics, 'Payment statistics retrieved successfully');
    }
}
