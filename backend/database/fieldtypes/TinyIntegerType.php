<?php

namespace Database\fieldtypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TinyIntegerType extends Type
{
    const TINYINTEGER = 'tinyinteger';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'TINYINT' . (isset($fieldDeclaration['length']) ? "({$fieldDeclaration['length']})" : '');
    }

    public function getName()
    {
        return self::TINYINTEGER;
    }
}
