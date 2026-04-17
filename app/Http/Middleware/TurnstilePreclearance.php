<?php

namespace App\Http\Middleware;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TurnstilePreclearance
{
    public function __construct(private readonly TurnstileService $turnstileService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->turnstileService->isEnabled()) {
            return $next($request);
        }

        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        if ($this->isPrecleared($request)) {
            return $next($request);
        }

        $request->session()->put('turnstile_intended', $request->fullUrl());

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'code' => 'TURNSTILE_PRECLEARANCE_REQUIRED',
                'message' => 'Security preclearance required.',
                'redirect' => route('turnstile.preclearance.show'),
            ], 428);
        }

        return redirect()->route('turnstile.preclearance.show');
    }

    private function isPrecleared(Request $request): bool
    {
        $until = $request->session()->get('turnstile_preclearance_until');
        if (!$until) {
            return false;
        }

        try {
            return now()->lessThanOrEqualTo(\Carbon\Carbon::parse($until));
        } catch (\Throwable) {
            return false;
        }
    }

    private function shouldSkip(Request $request): bool
    {
        // Preclearance endpoints must always be reachable.
        if ($request->routeIs('turnstile.preclearance.*')) {
            return true;
        }

        // Public legal / verification pages (required for third-party verification).
        if ($request->routeIs('legal.*') || $request->routeIs('privacy.page')) {
            return true;
        }

        // Social login endpoints should not be interrupted.
        if ($request->routeIs('user.google.*')) {
            return true;
        }

        // Admin area excluded.
        if ($request->routeIs('admin.*') || $request->is('admin', 'admin/*')) {
            return true;
        }

        // Machine-to-machine endpoints excluded.
        if ($request->is('ipn', 'ipn/*')) {
            return true;
        }

        if ($request->is(
            'paystack/webhook',
            'budpay/webhook',
            'kora/webhook',
            'ussd/callback',
            'cron',
            'cron/*',
            'up'
        )) {
            return true;
        }

        // Exclude dashboard app services (payments/transfers/bills).
        if ($request->is(
            'user/deposit*',
            'user/withdraw*',
            'user/bill*',
            'user/transfer*',
            'user/other-transfer*',
            'user/payment*',
            'user/trx*'
        )) {
            return true;
        }

        // Exclude small internal API endpoints defined in web routes.
        if ($request->is('api', 'api/*')) {
            return true;
        }

        return false;
    }
}
