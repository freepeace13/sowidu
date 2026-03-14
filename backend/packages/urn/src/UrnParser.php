<?php

namespace Packages\Urn;

use Illuminate\Support\Str;

class UrnParser
{
    protected $prefix;

    protected $separator;

    public function __construct($prefix, $separator)
    {
        $this->prefix = $prefix;
        $this->separator = $separator;
    }

    public function parse(string $urn)
    {
        if (Str::startsWith($urn, $this->prefix)) {
            $urn = str_replace($this->prefix, '', $urn);
        }

        return explode($this->separator, $urn);
    }
}
