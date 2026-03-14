<?php

namespace App\Exceptions;

use App\Exceptions\HttpExceptions\ForbiddenException;

class InvalidCredentialsException extends ForbiddenException
{
    public function __construct($message = 'Invalid Credentails.')
    {
        parent::__construct($message);
    }
}
