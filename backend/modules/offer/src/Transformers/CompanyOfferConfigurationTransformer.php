<?php

namespace Modules\Offer\Transformers;

/**
 * @property \Modules\Offer\Models\CompanyOfferConfiguration $resource
 */
class CompanyOfferConfigurationTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'terms_and_conditions' => $this->resource->terms_and_conditions,
        ];
    }
}
