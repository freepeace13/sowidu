<?php

namespace App\Models\Relations;

use App\Models\User;

trait AuthoredByUser
{
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isAuthoredBy(User $user): bool
    {
        return $this->user->is($user);
    }
}
