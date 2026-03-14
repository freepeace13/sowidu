<?php

namespace Modules\Offer\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait for models that are authored/owned by a user.
 * Uses config-driven class resolution for the User model.
 */
trait AuthoredByUser
{
    public function author(): BelongsTo
    {
        return $this->belongsTo($this->getUserModelClass(), 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo($this->getUserModelClass(), 'user_id');
    }

    public function isAuthoredBy(Model $user): bool
    {
        return $this->user->is($user);
    }

    protected function getUserModelClass(): string
    {
        return config('offer.models.user', \App\Models\User::class);
    }
}
