<?php

namespace App\Exceptions;

class EmploymentNotAllowedException extends ForbiddenException
{
    public function __construct($message = 'Employment not allowed exception.')
    {
        parent::__construct($message);
    }
}
