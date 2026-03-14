<?php

namespace App\Http\Api\Controllers\V1\Media;

use App\Http\Api\Actions\Media\UpdateMediaFile;
use App\Http\Api\Resources\V1\Media\MediaResource;
use App\Services\FileSharingService;
use Illuminate\Http\Request;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class UpdateFileDetailsController extends MediaController
{
    public function __construct(
        protected UpdateMediaFile $updateAction,
    ) {}

    public function __invoke(Request $request, Media $media)
    {
        $user = $this->getHasMediaUser();

        $canWrite = app(FileSharingService::class)
            ->setMedia($media)
            ->canWriteFile($user);

        abort_unless($canWrite, 403, 'Unauthorized.');

        $this->updateAction->update($media, [
            'name' => $request->name,
        ]);

        return (new MediaResource($media->fresh()))
            ->withOwner()
            ->withPolicy($user);
    }
}
