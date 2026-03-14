<?php

namespace App\Transformers;

class AddressTransformer extends Transformer
{
    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        $address = [
            'street' => $this->resource->street,
            'state' => $this->resource->state,
            'city' => $this->resource->city,
            'country' => $this->resource->country,
            'house_number' => $this->resource->house_number,
            'zipcode' => $this->resource->zipcode,
        ];

        return array_merge($address, [
            'full' => implode(', ', array_filter($address)),
        ]);
    }
}
