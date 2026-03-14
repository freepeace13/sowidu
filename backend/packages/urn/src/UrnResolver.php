<?php

namespace Packages\Urn;

use Exception;

class UrnResolver
{
    protected $parser;

    public function __construct(UrnParser $parser)
    {
        $this->parser = $parser;
    }

    public function resolve($urn)
    {
        [$namespace, $id] = $this->parser->parse($urn);

        $resource = UrnManager::resource($namespace);

        if (!class_exists($resource)) {
            throw new Exception("Invalid resource class [{$resource}].");
        }

        return $resource::find($id);
    }
}
