<?php

namespace App\Http\Controllers\Inertia\Media;

use App\Actions\Media\RemoveTagMediaCategory;
use App\Http\Controllers\Controller;
use App\Services\MediaFileService;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Http\Request;

class RemoveMediaTagCategoryController extends Controller
{
    use InteractsWithImpersonator;

    public function __invoke(Request $request, string $media, RemoveTagMediaCategory $categoryTagRemover, MediaFileService $service)
    {
        $categoryTagRemover->remove(
            $request->user(),
            $service->findByUuidOrFail($media),
            $this->getCurrentTeamId(),
        );

        return redirect()->route('media.drive.index');
    }
}
