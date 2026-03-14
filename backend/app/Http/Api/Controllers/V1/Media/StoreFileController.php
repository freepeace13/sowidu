<?php

namespace App\Http\Api\Controllers\V1\Media;

use App\Actions\Media\NormalizeImages;
use App\Http\Api\Actions\Media\StoreMediaFile;
use App\Http\Api\Resources\V1\Media\MediaResource;
use Illuminate\Http\Request;

class StoreFileController extends MediaController
{
    public function __invoke(Request $request, StoreMediaFile $action, NormalizeImages $normalizer)
    {

        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,tiff,tif,pdf,doc,docx,xls,xlsx,csv,zip,mp4,mov,avi,wmv,mkv|max:51200', // 20MB max
        ]);

        $user = $request->user();
        $teamId = $this->currentTeam()?->id;

        $file = $normalizer->normalize($request->file('file'));

        $media = $action->store($user, [
            'file' => $file,
            'last_modified' => $request->last_modified,
        ], $teamId);

        return (new MediaResource($media))
            ->withOwner()
            ->withPolicy($user);
    }
}
