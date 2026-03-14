<?php

namespace Packages\Avatarable\Exceptions;

class DiskDoesNotExist extends FileCannotBeAdded
{
    public static function create(string $diskName): self
    {
        return new static("There is no filesystem disk named `{$diskName}`");
    }
}
