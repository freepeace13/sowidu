<?php

namespace App\Transformers\Traits;

use Packages\Urn\UrnManager;

trait WithUrnAttribute
{
    public function withURN()
    {
        return $this->state(function () {
            return [
                'urn' => UrnManager::generate($this->resource),
            ];
        });
    }
}
