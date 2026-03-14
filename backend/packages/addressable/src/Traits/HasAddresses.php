<?php

namespace Packages\Addressable\Traits;

use Packages\Addressable\AddressRepository;
use Packages\Addressable\Models\Address;

trait HasAddresses
{
    public function addresses()
    {
        return $this->morphMany(Address::class, 'model');
    }

    public function address()
    {
        return (new AddressRepository)->setSubject($this);
    }
}
