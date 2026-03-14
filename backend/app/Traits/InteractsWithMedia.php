<?php

namespace App\Traits;

use App\Enums\UserType;
use App\Models\Employee;
use App\Models\User;
use Packages\MediaLibrary\MediaCollections\Models\Media;

trait InteractsWithMedia
{
    protected function resolveMedia(string $uuid): Media
    {
        $media = Media::whereUuid($uuid)->first();

        abort_unless((bool) $media, 404);

        return $media;
    }

    protected function resolveMember(array $validated): User|Employee
    {
        $memberClass = UserType::getConstants()[$validated['member_type']];

        return $memberClass::find($validated['member_id']);
    }
}
