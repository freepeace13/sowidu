<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FindableByUuid
{
    /**
     * Find a model by its UUID.
     */
    public static function findByUuid(string $uuid): ?static
    {
        return static::where('uuid', $uuid)->first();
    }

    /**
     * Find a model by its UUID or fail.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByUuidOrFail(string $uuid): static
    {
        return static::where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Scope a query to find models by UUID.
     */
    public function scopeWhereUuid(Builder $query, string $uuid): Builder
    {
        return $query->where('uuid', $uuid);
    }

    /**
     * Find a model by its ID or UUID.
     */
    public static function findByIdentifier(string|int $identifier): ?static
    {
        if (is_numeric($identifier)) {
            return static::find($identifier);
        }

        return static::findByUuid($identifier);
    }

    /**
     * Find a model by its ID or UUID or fail.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByIdentifierOrFail(string|int $identifier): static
    {
        if (is_numeric($identifier)) {
            return static::findOrFail($identifier);
        }

        return static::findByUuidOrFail($identifier);
    }
}
