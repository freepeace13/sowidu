<?php

namespace App\Http\Middleware;

use App\Support\Facades\Impersonate;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Passport\TokenRepository;

class AuthenticatesWebGuards
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $tokenUser = $this->retrieveGuardUser($request, 'personal');

        $webGuard = auth()->guard('web');

        if (is_null($tokenUser)) {
            Impersonate::leave();
            $webGuard->logout();
        }

        if ($tokenUser) {
            if (!$webGuard->check() || !$webGuard->user()->is($tokenUser)) {
                $webGuard->loginUsingId($tokenUser->id);
            }

            $tokenCompany = $this->retrieveGuardUser($request, 'commercial');

            if ($tokenCompany) {
                abort_unless(Impersonate::canImpersonate($tokenCompany), 403);

                Impersonate::impersonate($tokenCompany->id);
            } else {
                Impersonate::leave();
            }
        }

        $request->headers->remove('Authorization');

        return $next($request);
    }

    protected function retrieveGuardUser($request, $guard)
    {
        $accessToken = $this->retrieveToken($request, $guard);

        // if (app(TokenRepository::class)->isAccessTokenRevoked($accessToken)) {
        //     return null;
        // }

        return $this->retrieveTokenUser($request, $accessToken, $guard);
    }

    protected function retrieveToken($request, $key)
    {
        $tokens = $this->parseTokensFromCookies($request);

        $key = [
            'personal' => 'user',
            'commercial' => 'company',
        ][$key];

        return Arr::get($tokens ?? [], "auth.{$key}.accessToken");
    }

    protected function retrieveTokenUser($request, $accessToken, $guard)
    {
        try {
            $request->headers->set('Authorization', "Bearer {$accessToken}");
            $user = auth()->guard($guard)->user();
        } finally {
            $request->headers->remove('Authorization');
        }

        return $user;
    }

    protected function parseTokensFromCookies($request)
    {
        $cookie = urldecode($request->header('cookie'));
        // $cookie = str_replace('app:auth=', '', $cookie);

        $cookie = Str::between($cookie, 'app:auth=', '}; ');
        $cookie .= '}';

        return json_decode($cookie, true);
    }
}
