<?php

namespace App\Http\Controllers\Inertia\Media;

use App\Actions\Media\TagMediaWithCategory;
use App\Http\Controllers\Inertia\InertiaController;
use App\Services\MediaFileService;
use Illuminate\Http\Request;

class MediaTagCategoryController extends InertiaController
{
    public function __invoke(
        Request $request,
        string $media,
        MediaFileService $service,
        TagMediaWithCategory $tagger,
    ) {
        $tagger->tag(
            $request->user(),
            $service->findByUuidOrFail($media),
            $request->all(),
            $this->getCurrentTeamId(),
        );

        return back(303);
    }
}
