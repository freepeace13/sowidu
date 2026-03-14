<?php

namespace App\Http\Api\Controllers\V1\Media;

use App\Actions\Media\DeleteMedia;
use App\Http\Api\Resources\V1\Media\MediaResource;
use App\Services\MediaFileService;
use Illuminate\Http\Request;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class FileTrashController extends MediaController
{
    public function index(Request $request)
    {
        $user = $this->getHasMediaUser();

        $files = app(MediaFileService::class)
            ->forUser($user)
            ->onlyTrashed()
            ->filters($request->only(['type']))
            ->latest()
            ->orderBy('id', 'asc')
            ->get();

        return MediaResource::collection(
            $files,
            fn ($resource) => $resource
                ->withOwner()
                ->withPolicy($user),
        );
    }

    public function restore(string $media)
    {
        $media = Media::withTrashed()
            ->whereUuid($media)
            ->firstOrFail();

        $media->restore();

        return $this->response();
    }

    public function destroy(Media $media)
    {
        $user = $this->getHasMediaUser();

        app(DeleteMedia::class)->delete($user, $media);

        return $this->response();
    }
}
