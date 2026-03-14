<?php

namespace App\Models\Concerns;

use App\Models\Profile;

trait HasProfile
{
    public function createProfile()
    {
        if (!$this->profile()->exists()) {
            return $this->profile()->create([]);
        }
    }

    public function profile()
    {
        return $this->morphOne(Profile::class, 'model');
    }
}
