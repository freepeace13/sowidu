<?php

namespace App\Http\Api\Controllers\V1\Media;

use App\Http\Api\Resources\V1\Media\UserResource;
use App\Services\FileSharingService;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class FileUsersController extends MediaController
{
    public function __invoke(Media $media)
    {
        $users = app(FileSharingService::class)
            ->setMedia($media)
            ->getAllUsers();

        return UserResource::collection(
            $users,
            fn ($resource) => $resource
                ->withIsOwner($media)
                ->withPermissions($media),
        );
    }
}
