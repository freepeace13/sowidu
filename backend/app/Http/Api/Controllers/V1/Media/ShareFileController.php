<?php

namespace App\Http\Api\Controllers\V1\Media;

use App\Http\Api\Actions\Media\ShareMediaFile;
use App\Http\Api\Resources\V1\Media\UserResource;
use Illuminate\Http\Request;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class ShareFileController extends MediaController
{
    public function __invoke(Request $request, Media $media)
    {
        $user = app(ShareMediaFile::class)->share(
            $media,
            $request->query('user'),
            $request->query('scopes'),
        );

        return (new UserResource($user))
            ->withIsOwner($media)
            ->withPermissions($media);
    }
}
