<?php

namespace App\Transformers\Addressbook;

use App\Transformers\Transformer;

class AddressTransformer extends Transformer
{
    public function toArray($request)
    {
        $address = [
            'house_number' => $this->resource?->house_number,
            'street' => $this->resource?->street,
            'state' => $this->resource?->state,
            'city' => $this->resource?->city,
            'country' => $this->resource?->country,
            'zipcode' => $this->resource?->zipcode,
        ];

        return array_merge($address, [
            'full' => implode(', ', array_filter($address)),
        ]);
    }
}
