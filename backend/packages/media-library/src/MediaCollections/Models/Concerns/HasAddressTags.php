<?php

namespace Packages\MediaLibrary\MediaCollections\Models\Concerns;

use App\Models\AddressRecord;

trait HasAddressTags
{
    public function addressTags()
    {
        return $this->morphToMany(
            AddressRecord::class,
            'taggable',
            'address_records_taggables',
            'taggable_id',
            'address_record_id',
        )->withTimestamps();
    }

    /**
     * Get single address tag
     * If relationship will change to `many_to_many` - Feel free to remove this method
     *
     * @return \App\Models\AddressRecord
     */
    public function addressTag()
    {
        $this->loadMissing('addressTags');

        return $this->addressTags->first();
    }
}
