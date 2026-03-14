<?php

namespace App\Enums\MetaProperties;

use ArchTech\Enums\Meta\MetaProperty;
use Attribute;

#[Attribute]
class Trans extends MetaProperty
{
    protected function transform(mixed $value): mixed
    {
        return trans($value);
    }
}
