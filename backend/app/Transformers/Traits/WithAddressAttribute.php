<?php

namespace App\Transformers\Traits;

use App\Transformers\AddressTransformer;
use Packages\Addressable\Models\Address;

trait WithAddressAttribute
{
    public function withAddress()
    {
        $address = $this->address()->newestFirst() ?? new Address;

        return $this->state(function () use ($address) {
            return [
                'address' => (new AddressTransformer($address))->resolve(),
            ];
        });
    }
}
