<?php

namespace Modules\Shared\Models\Relations;

use App\Models\Address;

trait Addressable
{
    public function addresses(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Address::class, 'ownerable');
    }

    /**
     * The address attribute getter method.
     */
    public function getAddressAttribute(): ?Address
    {
        return $this->addresses()->active()->first() ?: new Address;
    }

    /**
     * Get user's shouldPromptAddress attribute value
     */
    public function getShouldPromptAddressAttribute(): bool
    {
        $skippedAt = $this->skipped_address_at;

        return is_null($this->address)
            && (is_null($this->skipped_address_at) || now()->diffInDays($skippedAt) > 15);
    }

    /**
     * Add and set as default new address of model
     */
    public function addNewAddress(array $address = []): Address
    {
        $this->addresses()->update([
            'is_active' => false,
        ]);

        return $this->addresses()->create($address);
    }
}
