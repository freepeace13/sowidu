<?php

namespace App\Http\Api\Controllers\V1\Media;

use App\Http\Api\Resources\V1\Media\MediaResource;
use App\Services\MediaFileService;
use Illuminate\Http\Request;
use Packages\RestApi\Feature;

class FileListController extends MediaController
{
    public function __invoke(Request $request)
    {
        $user = $this->getHasMediaUser();

        $files = app(MediaFileService::class)
            ->forUser($user)
            ->filters($request->only(['type']))
            ->latest();

        if (Feature::active('PaginatedResourceCollection')) {
            $files = $files->paginate($request->query('perPage') ?? 10);
        } else {
            $files = $files->get();
        }

        return MediaResource::collection(
            $files,
            fn ($resource) => $resource
                ->withOwner()
                ->withPolicy($user),
        );
    }
}
