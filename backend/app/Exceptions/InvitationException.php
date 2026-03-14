<?php

namespace App\Exceptions;

use RuntimeException;

class InvitationException extends RuntimeException
{
    public static function sender($sender)
    {
        return new static('Invalid invitation sender ' . model_morphs_stringify($sender));
    }
}
