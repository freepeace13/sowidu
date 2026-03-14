<?php

namespace App\Http\Controllers\Inertia\Media;

use App\Actions\Media\CreateMediaAddressTag;
use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class MediaTagAddressController extends InertiaController
{
    public function store(Request $request, string $media, CreateMediaAddressTag $tagger)
    {
        $media = MediaFile::whereUuid($media)->first();

        abort_unless((bool) $media, 404);

        $tagger->tag(
            $request->user(),
            $media,
            $request->all(),
            $this->getCurrentTeamId(),
        );

        return back(303);
    }

    public function destroy(Request $request)
    {
        return back(303);
    }
}
