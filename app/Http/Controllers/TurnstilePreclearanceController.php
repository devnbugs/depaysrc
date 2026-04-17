<?php

namespace App\Http\Controllers;

use App\Services\TurnstileService;
use Illuminate\Http\Request;

class TurnstilePreclearanceController extends Controller
{
    public function show(Request $request, TurnstileService $turnstileService)
    {
        $pageTitle = 'Security Check';

        return view(activeTemplate() . 'turnstile.preclearance', [
            'pageTitle' => $pageTitle,
            'siteKey' => $turnstileService->getSiteKey(),
            'isEnabled' => $turnstileService->isEnabled(),
        ]);
    }

    public function verify(Request $request, TurnstileService $turnstileService)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
        ]);

        $ip = $request->ip();

        $ok = $turnstileService->verifyWithProtection(
            $validated['token'],
            'preclearance',
            $ip,
            5,
            1
        );

        if (!$ok) {
            $turnstileService->trackSuspiciousActivity($ip, 'Preclearance verification failed');

            return response()->json([
                'success' => false,
                'message' => 'Security verification failed. Please try again.',
            ], 422);
        }

        $minutes = (int) config('turnstile.preclearance_minutes', 720);
        $request->session()->put('turnstile_preclearance_until', now()->addMinutes($minutes)->toIso8601String());

        $redirect = (string) $request->session()->pull('turnstile_intended', route('home'));

        return response()->json([
            'success' => true,
            'redirect' => $redirect,
            'minutes' => $minutes,
        ]);
    }
}

