<?php

namespace App\Http\Middleware;

use App\Services\CountlyService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Countly Analytics Middleware
 * 
 * Initializes Countly session for authenticated users
 * Applies only to user routes (not admin)
 */
class CountlyAnalytics
{
    protected CountlyService $countlyService;

    public function __construct(CountlyService $countlyService)
    {
        $this->countlyService = $countlyService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Only initialize Countly for authenticated users
        if (auth()->check() && $this->countlyService->isEnabled()) {
            // Initialize session if not already done
            $sessionKey = 'countly_session_initialized_' . auth()->id();
            if (!session()->has($sessionKey)) {
                $this->countlyService->initializeSession();
                session()->put($sessionKey, true);
            }

            // Track feature access
            if ($request->route()) {
                $routeName = $request->route()->getName();
                $feature = $this->mapRouteToFeature($routeName);
                
                if ($feature) {
                    $this->countlyService->trackFeatureEvent($feature, 'access', [
                        'route' => $routeName,
                        'method' => $request->method(),
                    ]);
                }
            }
        }

        return $next($request);
    }

    /**
     * Map route names to feature names
     */
    private function mapRouteToFeature(string $routeName): ?string
    {
        $featureMap = [
            'user.home' => 'dashboard',
            'user.deposit' => 'deposit',
            'user.withdraw' => 'withdraw',
            'user.othertransfer' => 'transfer',
            'user.internet' => 'bills',
            'user.airtime' => 'bills',
            'user.cabletv' => 'bills',
            'user.utility' => 'bills',
            'user.vcard' => 'cards',
            'user.mysavings' => 'savings',
            'user.investment.new' => 'investment',
            'user.myloan' => 'loans',
            'user.kyc.services' => 'kyc_services',
            'user.support' => 'support',
            'user.profile.setting' => 'settings',
            'user.security' => 'security',
        ];

        // Try exact match
        if (isset($featureMap[$routeName])) {
            return $featureMap[$routeName];
        }

        // Try prefix matching
        foreach ($featureMap as $route => $feature) {
            if (strpos($routeName, $route) === 0) {
                return $feature;
            }
        }

        return null;
    }
}
