<?php

namespace App\Actions\Media;

use App\Events\Media\MediaTagCategoryRemoved;
use App\Models\User;
use App\Services\MediaFileService;
use App\Traits\InteractsWithImpersonator;
use App\Traits\WithAccountCategories;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class RemoveTagMediaCategory
{
    use InteractsWithImpersonator, WithAccountCategories;

    public function remove(User $user, Media $media, ?int $teamId = null)
    {
        // TODO: Check if user is `Authorized` on this action

        (new MediaFileService)->forUser($user, $teamId)
            ->removeCategoryTag($media);

        event(new MediaTagCategoryRemoved($media));
    }
}
