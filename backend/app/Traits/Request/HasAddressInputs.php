<?php

namespace App\Traits\Request;

trait HasAddressInputs
{
    /**
     * Determine if the one of address fields are given
     *
     * @return bool
     */
    public function addressFilled()
    {
        return $this->filled('address.house_number')
            || $this->filled('address.city')
            || $this->filled('address.zipcode')
            || $this->filled('address.country')
            || $this->filled('address.state')
            || $this->filled('address.street');
    }
}
