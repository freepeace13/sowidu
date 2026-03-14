<?php

namespace App\Exceptions;

class EmploymentRequestException extends ConflictException
{
    public function __construct($message = 'Employment request exist exception.', array $headers = [])
    {
        parent::__construct($message, $headers);
    }
}
