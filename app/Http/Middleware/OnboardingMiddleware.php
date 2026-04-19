<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\KycVerificationService;

/**
 * OnboardingMiddleware
 * 
 * Checks if authenticated users have completed onboarding
 * Redirects incomplete profiles to onboarding flow
 */
class OnboardingMiddleware
{
    public function __construct(protected KycVerificationService $kycService)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        // Skip for non-authenticated users
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Skip if user has completed onboarding
        if (!$this->kycService->needsOnboarding($user)) {
            return $next($request);
        }

        // Allowed routes during onboarding
        $allowedOnboardingRoutes = [
            'user.onboarding',
            'user.onboarding.personal-info',
            'user.onboarding.identity-verification',
            'user.onboarding.liveness-check',
            'user.onboarding.liveness-callback',
            'user.logout',
            'api.*', // Allow API calls
            'auth.*', // Allow auth routes
        ];

        // Check if current route is allowed during onboarding
        $isAllowedRoute = false;
        foreach ($allowedOnboardingRoutes as $route) {
            if ($request->routeIs($route)) {
                $isAllowedRoute = true;
                break;
            }
        }

        // If not allowed and onboarding incomplete, redirect to onboarding
        if (!$isAllowedRoute && $this->kycService->needsOnboarding($user)) {
            return redirect()->route('user.onboarding');
        }

        return $next($request);
    }
}
