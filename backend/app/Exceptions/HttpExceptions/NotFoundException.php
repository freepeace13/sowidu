<?php

namespace App\Exceptions\HttpExceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NotFoundException extends HttpException
{
    public function __construct(?string $message = null, array $headers = [])
    {
        parent::__construct(Response::HTTP_NOT_FOUND, $message, null, $headers);
    }
}
