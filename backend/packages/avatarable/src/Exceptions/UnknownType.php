<?php

namespace Packages\Avatarable\Exceptions;

class UnknownType extends FileCannotBeAdded
{
    public static function create(): self
    {
        return new static('Only strings and UploadedFileObjects can be imported');
    }
}
