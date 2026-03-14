<?php

namespace Modules\Shared\Models\Relations\Inverse;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasCreator
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereCreator(Builder $query, Model $creator)
    {
        return $query->where([
            'creator_id' => $creator->getKey(),
            'creator_type' => $creator->getMorphClass(),
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function creator()
    {
        return $this->morphTo();
    }

    /**
     * @return self
     */
    public static function makeWithCreator(Model $creator)
    {
        return static::make()->creator()->associate($creator);
    }
}
