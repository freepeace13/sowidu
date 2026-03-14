<?php

namespace App\Exceptions;

class ContactRequestExistException extends BadRequestException
{
    public function __construct(string $message = 'Contact request already exist.', array $headers = [])
    {
        parent::__construct($message, $headers);
    }
}
