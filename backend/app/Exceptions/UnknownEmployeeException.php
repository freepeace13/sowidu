<?php

namespace App\Exceptions;

class UnknownEmployeeException extends UnauthorizedException
{
    public function __construct($message = 'Unknown employee.')
    {
        parent::__construct($message);
    }
}
