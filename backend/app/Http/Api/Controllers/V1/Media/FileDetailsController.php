<?php

namespace App\Http\Api\Controllers\V1\Media;

use App\Http\Api\Resources\V1\Media\MediaResource;
use App\Services\FileSharingService;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class FileDetailsController extends MediaController
{
    public function __invoke(Media $media)
    {
        $user = $this->getHasMediaUser();

        abort_unless(app(FileSharingService::class)
            ->setMedia($media)
            ->isAccessibleTo($user), 404, 'Media not found.');

        return (new MediaResource($media))
            ->withOwner()
            ->withPolicy($user)
            ->withResponsiveImages();
    }
}
