<?php

namespace App\Exceptions\HttpExceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedException extends HttpException
{
    public function __construct(?string $message = null, array $headers = [])
    {
        parent::__construct(Response::HTTP_UNAUTHORIZED, $message, null, $headers);
    }
}
