<?php

namespace App\Exceptions;

use App\Contracts\Auth\AuthorizableGroup;
use InvalidArgumentException;

class AuthorizableGroupException extends InvalidArgumentException
{
    public static function invalid($class)
    {
        $interface = AuthorizableGroup::class;

        return new AuthorizableGroupException(
            "The authorizable class [$class::class] should implement the [$interface] interface.",
        );
    }
}
