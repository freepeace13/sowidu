<?php

namespace App\Actions\Category;

use App\Actions\Media\RemoveTagMediaCategory;
use App\Models\Company;
use App\Models\User;
use App\Services\MediaFileService;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class RemoveCategoryTagOnAllMediaOwned
{
    public function remove(Company|User $owner, string $category): void
    {
        $service = morph_is($owner, Company::class) ? MediaFileService::makeForCompany($owner) : MediaFileService::make($owner);

        $service->withTrashed()
            ->where('category', 'LIKE', "%$category%")
            ->with(['model.user'])
            ->get()
            ->each(function (Media $media) use ($owner) {
                (new RemoveTagMediaCategory)
                    ->remove($media->model->user, $media, $owner->id);
            });
    }
}
