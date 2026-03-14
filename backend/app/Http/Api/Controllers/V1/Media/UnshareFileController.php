<?php

namespace App\Http\Api\Controllers\V1\Media;

use App\Http\Api\Actions\Media\UnshareMediaFile;
use Illuminate\Http\Request;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class UnshareFileController extends MediaController
{
    public function __invoke(Request $request, Media $media)
    {
        app(UnshareMediaFile::class)->unshare(
            $media,
            $request->query('user'),
        );

        return $this->response();
    }
}
