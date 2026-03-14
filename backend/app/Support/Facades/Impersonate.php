<?php

namespace App\Support\Facades;

use App\Services\Impersonate as Impersonation;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Database\Eloquent\Model|\Illuminate\Contracts\Auth\Authenticatable|null impersonate($tenant)
 * @method static \Illuminate\Database\Eloquent\Model|\Illuminate\Contracts\Auth\Authenticatable|null user()
 * @method static void leave()
 * @method static bool isImpersonating($tenant = null)
 * @method static bool canImpersonate($tenant)
 * @method static \App\Models\Company tenant()
 * @method static \App\Models\User|\Illuminate\Database\Eloquent\Model user()
 * @method static \App\Models\Company|\App\Models\User|\Illuminate\Database\Eloquent\Model account()
 * @method static self as($user)
 * @method static \App\Models\Employee impersonator($tenant = null)
 * @method static mixed getImpersonatedKey()
 * @method static string getSessionKey()
 *
 * @see \App\Services\Impersonate
 */
class Impersonate extends Facade
{
    public static function getFacadeAccessor()
    {
        return Impersonation::class;
    }
}
