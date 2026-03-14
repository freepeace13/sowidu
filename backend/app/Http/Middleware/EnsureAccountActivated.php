<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EnsureAccountActivated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::guard('web')->user();

        if ($user && $user->hasVerifiedEmail()) {
            if (blank($user->password)) {
                return Inertia::render('AccountActivationForm', [
                    'account' => [
                        'firstName' => $user->first_name,
                        'lastName' => $user->last_name,
                    ],
                ]);
            }
        }

        return $next($request);
    }
}
