<?php

namespace Packages\Urn;

use Illuminate\Database\Eloquent\Model;

class UrnGenerator
{
    protected $prefix;

    protected $separator;

    public function __construct($prefix, $separator)
    {
        $this->prefix = $prefix;
        $this->separator = $separator;
    }

    public function generate(Model $resource)
    {
        $namespace = UrnManager::namespace(get_class($resource));

        return $this->prefix . implode($this->separator, [
            $namespace,
            $resource->getKey(),
        ]);
    }
}
