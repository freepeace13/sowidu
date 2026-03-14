<?php

namespace App\Exceptions;

use App\Exceptions\HttpExceptions\UnauthorizedException;

class CompanyNotBelongException extends UnauthorizedException
{
    public function __construct($message = 'Company not belong.')
    {
        parent::__construct($message);
    }
}
