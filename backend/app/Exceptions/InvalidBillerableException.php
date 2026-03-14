<?php

namespace App\Exceptions;

class InvalidBillerableException extends BadRequestException
{
    public function __construct($message = 'Invalid billerable exception', array $headers = [])
    {
        parent::__construct($message, $headers);
    }
}
