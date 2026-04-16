<?php

namespace App\Http\Middleware;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Turnstile Dashboard Middleware
 * 
 * Protects authenticated routes from:
 * - Multiple concurrent requests (anti-spam)
 * - Request flooding
 * - Unauthorized access attempts
 * - Payment/Transfer abuse
 */
class TurnstileDashboard
{
    protected TurnstileService $turnstileService;

    public function __construct(TurnstileService $turnstileService)
    {
        $this->turnstileService = $turnstileService;
    }

    /**
     * Handle an incoming request
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $ip = $request->ip();
        $userId = auth()->id();

        // Check if IP is blocked
        if ($this->turnstileService->isIPBlocked($ip)) {
            $this->turnstileService->logSecurityEvent('Blocked IP Attempted Access', [
                'ip' => $ip,
                'user_id' => $userId,
                'url' => $request->path(),
                'reason' => $this->turnstileService->getBlockReason($ip),
            ]);

            return response()->view('errors.blocked', [
                'reason' => $this->turnstileService->getBlockReason($ip),
            ], Response::HTTP_FORBIDDEN);
        }

        // Detect rapid requests from same user
        if (!$this->detectRapidRequests($userId, $ip, $request)) {
            return $this->tooManyRequestsResponse($request);
        }

        // Check for suspicious patterns
        if ($this->detectSuspiciousPattern($userId, $ip, $request)) {
            $this->turnstileService->trackSuspiciousActivity(
                $ip,
                'Suspicious pattern detected in dashboard access'
            );

            return response()->json([
                'success' => false,
                'message' => 'Suspicious activity detected. Please refresh and try again.',
                'code' => 'SUSPICIOUS_ACTIVITY',
            ], Response::HTTP_FORBIDDEN);
        }

        // Log successful request
        $this->logRequest($userId, $ip, $request);

        return $next($request);
    }

    /**
     * Detect rapid requests from the same user
     * Protects against flooding attacks
     */
    protected function detectRapidRequests(int $userId, string $ip, Request $request): bool
    {
        // Allow higher limits for GET requests
        $maxRequests = $request->isMethod('GET') ? 30 : 10;
        $decaySeconds = 60;

        $key = "dashboard_requests:{$userId}";
        
        if (\Cache::has($key) && \Cache::get($key) >= $maxRequests) {
            return false;
        }

        $count = \Cache::increment($key);
        if ($count === 1) {
            \Cache::put($key, $count, now()->addSeconds($decaySeconds));
        }

        return true;
    }

    /**
     * Detect suspicious access patterns
     */
    protected function detectSuspiciousPattern(int $userId, string $ip, Request $request): bool
    {
        $sensitiveRoutes = [
            'dashboard.payment',
            'dashboard.transfer',
            'dashboard.airtime',
            'dashboard.data',
            'dashboard.bills',
            'dashboard.pin',
        ];

        // Check if route is sensitive
        $route = $request->route()?->getName();
        if (!in_array($route, $sensitiveRoutes)) {
            return false;
        }

        // Check for missing Turnstile token in sensitive operations
        if ($request->isMethod('POST') && !$request->filled('invisible-turnstile-token')) {
            return true;
        }

        // Check for rapid consecutive sensitive requests
        $sensitiveKey = "sensitive_requests:{$userId}";
        $count = \Cache::increment($sensitiveKey);
        
        if ($count === 1) {
            \Cache::put($sensitiveKey, $count, now()->addMinutes(5));
        }

        // Allow max 5 sensitive requests per 5 minutes
        return $count > 5;
    }

    /**
     * Log successful request
     */
    protected function logRequest(int $userId, string $ip, Request $request): void
    {
        // Only log sensitive operations
        if ($request->isMethod(['POST', 'PUT', 'DELETE'])) {
            $this->turnstileService->logSecurityEvent('Dashboard Request', [
                'user_id' => $userId,
                'ip' => $ip,
                'method' => $request->getMethod(),
                'route' => $request->route()?->getName(),
                'url' => $request->path(),
            ]);
        }
    }

    /**
     * Return too many requests response
     */
    protected function tooManyRequestsResponse(Request $request)
    {
        $this->turnstileService->logSecurityEvent('Rate Limit Exceeded', [
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
            'route' => $request->route()?->getName(),
        ]);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please slow down and try again in a moment.',
                'code' => 'RATE_LIMIT_EXCEEDED',
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        return response()->view('errors.rate-limited', [], Response::HTTP_TOO_MANY_REQUESTS);
    }
}
