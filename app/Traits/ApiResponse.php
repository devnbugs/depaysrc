<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * API Response Trait
 * 
 * Provides consistent JSON response formatting for all API endpoints
 * Used across all API controllers for standardized responses
 */
trait ApiResponse
{
    /**
     * Success Response
     * 
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @param int $httpCode
     * @return JsonResponse
     */
    protected function success(
        $data = null,
        string $message = 'Operation successful',
        int $code = 200,
        int $httpCode = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'code' => $code,
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ], $httpCode);
    }

    /**
     * Error Response
     * 
     * @param string $message
     * @param int $code
     * @param int $httpCode
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function error(
        string $message = 'Operation failed',
        int $code = 400,
        int $httpCode = 400,
        $errors = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'code' => $code,
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toIso8601String(),
        ], $httpCode);
    }

    /**
     * Not Found Response
     * 
     * @param string $message
     * @return JsonResponse
     */
    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, 404, 404);
    }

    /**
     * Unauthorized Response
     * 
     * @param string $message
     * @return JsonResponse
     */
    protected function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, 401, 401);
    }

    /**
     * Forbidden Response
     * 
     * @param string $message
     * @return JsonResponse
     */
    protected function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->error($message, 403, 403);
    }

    /**
     * Validation Error Response
     * 
     * @param array $errors
     * @param string $message
     * @return JsonResponse
     */
    protected function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->error($message, 422, 422, $errors);
    }

    /**
     * Paginated Response
     * 
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    protected function paginated($data, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'code' => 200,
            'status' => 'success',
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'count' => $data->count(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Created Response
     * 
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    protected function created($data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->success($data, $message, 201, 201);
    }

    /**
     * No Content Response
     * 
     * @return JsonResponse
     */
    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Server Error Response
     * 
     * @param string $message
     * @return JsonResponse
     */
    protected function serverError(string $message = 'Internal server error'): JsonResponse
    {
        return $this->error($message, 500, 500);
    }
}
