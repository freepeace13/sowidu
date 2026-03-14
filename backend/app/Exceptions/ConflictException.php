<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ConflictException extends HttpException
{
    /**
     * Initialize the exception class
     */
    public function __construct(?string $message = null, array $headers = [])
    {
        parent::__construct(Response::HTTP_CONFLICT, $message, null, $headers);
    }
}
