<?php

namespace App\Exceptions;

class UploadMissingFileException extends BadRequestException
{
    public function __construct(string $message = 'The request is missing a file', array $headers = [])
    {
        parent::__construct($message, $headers);
    }
}
