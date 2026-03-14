<?php

namespace Packages\Countries\Exceptions;

use Exception;

class CountryException extends Exception
{
    public static function notFound($countryCode)
    {
        return new static(sprintf('Country [%s] not found.', $countryCode));
    }
}
