<?php

namespace App\Exceptions;

class CatalogueNotbelongException extends UnauthorizedException
{
    public function __construct($message = 'Catalogue not belong.')
    {
        parent::__construct($message);
    }
}
