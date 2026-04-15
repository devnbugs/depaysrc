<?php

namespace App\Http\Middleware;

use Closure;

class LanguageMiddleware
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
        $locale = $this->getCode();

        session()->put('lang', $locale);
        app()->setLocale($locale);

        return $next($request);
    }

    public function getCode()
    {
        return normalizeLocale(session('lang', defaultLocaleCode()));
    }
}
