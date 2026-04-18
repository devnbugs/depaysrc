<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyAuthenticationForPayment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Check authentication requirements based on business rules
        
        // Rule 1: If PIN is off, Authenticator MUST be enabled
        if (!$user->isPinEnabled() && !$user->isTwoFactorEnabled()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'Security requirement not met',
                    'message' => 'You must enable either PIN or Two-Factor Authentication to make payments'
                ], 403);
            }
            
            return redirect()->route('user.user.pin.index')
                ->with('error', 'You must enable either PIN or 2FA to make payments');
        }

        return $next($request);
    }
}
