<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BadRequestException extends HttpException
{
    public function __construct(string $message = 'Bad Request', array $headers = [])
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message, null, $headers);
    }
}
