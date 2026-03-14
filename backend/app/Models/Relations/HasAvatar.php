<?php

namespace App\Models\Relations;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Storage;

trait HasAvatar
{
    /**
     * Get the default url
     *
     * @return string
     */
    public function getDefaultAvatar()
    {
        return Storage::disk('public')->url("{$this->getMorphClass()}.png");
    }

    /**
     * Get the existing/default avatar url
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        return optional($this->avatar)->url ?: $this->getDefaultAvatar();
    }

    /**
     * Get the existing/default avatar instance
     *
     * @return string
     */
    public function getAvatarAttribute()
    {
        return $this->avatars()->first();
    }

    /**
     * Get contactable avatars
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function avatars(): MorphToMany
    {
        return $this->morphToMany(
            Media::class,
            'ownerable',
            'avatars',
            'ownerable_id',
            'media_id',
        );
    }
}
