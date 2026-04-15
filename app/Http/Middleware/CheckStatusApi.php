<?php

namespace App\Http\Middleware;

use Closure;

class CheckStatusApi
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
        $user = auth()->user();

        if (! $user) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => ['error' => ['Unauthenticated user']],
            ], 401);
        }

        if ($user->status  && $user->ev  && $user->sv  && $user->tv) {
            return $next($request);
        }

        return response()->json([
            'status' => 'unauthorized',
            'message' => ['error' => ['Account requires authorization']],
        ], 403);
    }
}
