<?php

namespace Packages\MediaLibrary\Support;

use Illuminate\Support\Facades\Gate;
use Packages\MediaLibrary\HasMedia;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class MediaPolicyResolver
{
    public function resolve(HasMedia $model, Media $media)
    {
        $gate = Gate::forUser($model);

        return [
            'can_share' => $gate->allows('share', $media),
            'can_rename' => $gate->allows('rename', $media),
            'can_move' => $gate->allows('move', $media),
            'can_upload_file' => $gate->allows('upload-file', $media),
            'can_create_folder' => $gate->allows('create-folder', $media),
        ];
    }
}
