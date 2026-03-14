<?php

namespace App\Http\Middleware;

use App\Repositories\PasswordResetsRepository;
use Closure;
use Illuminate\Container\Container;

class ValidateResetPasswordToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $repository = Container::getInstance()->make(PasswordResetsRepository::class);

        $token = $request->query('token');

        if (!$repository->hasValidToken($token)) {
            abort(401);
        }

        return $next($request);
    }
}
