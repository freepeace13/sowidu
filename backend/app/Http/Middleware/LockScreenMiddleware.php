<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Tightenco\Ziggy\BladeRouteGenerator;

class LockScreenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check()) {
            $key = md5(Auth::user()->email);

            if ($request->session()->has($key)) {
                BladeRouteGenerator::$generated = false;

                // $content->page['component'] = 'LockScreen';

                // Arr::set($content->page, 'component', 'LockScreen');
                $content = $response->getOriginalContent();

                if ($content instanceof View) {
                    Arr::set($content->page, 'component', 'LockScreen');

                    $response->setContent($content);

                    // dd($response->setContent($content));
                    return $response;
                }
                dd($response);

            }
        }

        return $response;
    }
}
