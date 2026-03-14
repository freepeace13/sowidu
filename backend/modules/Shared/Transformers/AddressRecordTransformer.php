<?php

namespace Modules\Shared\Transformers;

/**
 * @property-read \App\Models\AddressRecord $resource
 */
class AddressRecordTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'user_id' => $this->resource->user_id,
            'team_id' => $this->resource->team_id,
            'full' => $this->resource->complete_address,
            'source' => model_name($this->resource),
        ];
    }

    public function withDetails()
    {
        return $this->state(fn ($attributes) => [
            'details' => $this->resource->details,
        ]);
    }
}
