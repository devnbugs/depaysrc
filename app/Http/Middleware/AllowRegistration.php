<?php

namespace App\Http\Middleware;

use App\Models\GeneralSetting;
use Closure;
use Throwable;

class AllowRegistration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $general = GeneralSetting::first();

            if ((int) ($general->registration ?? 1) === 0) {
                $notify[] = ['error', 'Registration is currently disabled.'];
                return back()->withNotify($notify);
            }
        } catch (Throwable $e) {
            // If the database is unavailable, let the request continue so the app can still render.
        }

        return $next($request);
    }
}
