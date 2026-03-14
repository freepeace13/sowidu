<?php

namespace App\Transformers\Traits;

use App\Transformers\PlaceTransformer;

trait WithPlaceAttribute
{
    public function withPlace($attributeName = null)
    {
        $currentPlace = $this->resource->currentPlace;

        return $this->state(function () use ($currentPlace, $attributeName) {
            return [
                $attributeName ?? 'place' => (new PlaceTransformer($currentPlace))
                    ->withId()
                    ->resolve(),
            ];
        });
    }
}
