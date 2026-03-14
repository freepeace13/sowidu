<?php

namespace Packages\Translation\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $sessionKey = config('translation.session_key');
        $locales = config('translation.locales');

        $locale = $request->session()->get($sessionKey, $request->getLocale());

        if (in_array($locale, array_keys($locales))) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
