<?php

namespace App\Models\Relations;

use App\Models\Media;

trait OwnsMedia
{
    /**
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'ownerable');
    }
}
