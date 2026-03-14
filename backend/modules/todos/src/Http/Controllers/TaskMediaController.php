<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Transformers\MediaTransformer;
use Illuminate\Http\Request;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

/**
 * @deprecated use `media.drive.files.index` instead or go to
 * @see \App\Http\Controllers\Json\Media\MediaFileListController
 *
 * TODO Remove! Not been used
 */
class TaskMediaController extends Controller
{
    public function __invoke(Request $request)
    {
        return response()->json(
            $request->user()
                ->getMedia()
                ->getRootFiles(['type' => $request->query('type')])
                ->map(fn (MediaFile $media) => (new MediaTransformer($media))
                    ->resolve()),
        );
    }
}
