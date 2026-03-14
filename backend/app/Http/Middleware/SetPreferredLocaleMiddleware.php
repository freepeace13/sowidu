<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Packages\Translation\Middleware\LocalizationMiddleware as Middleware;

class SetPreferredLocaleMiddleware extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            App::setLocale($this->getLocale($request));

            return $next($request);
        }

        return parent::handle($request, $next);
    }

    protected function getLocale($request)
    {
        $key = config('translation.session_key');

        if (Auth::check()) {
            return Auth::user()->locale;
        }

        if ($request->session()->has($key)) {
            return $request->session()->get($key);
        }

        return $request->getLocale();
    }
}
