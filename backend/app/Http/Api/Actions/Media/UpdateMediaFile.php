<?php

namespace App\Http\Api\Actions\Media;

use Packages\MediaLibrary\MediaCollections\Filesystem;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\RestApi\RestApiAction;

class UpdateMediaFile extends RestApiAction
{
    protected $rules = [
        'name' => ['required', 'min:3'],
    ];

    public function update(Media $media, array $data = [], $errorBag = null)
    {
        $validated = $this->validate($data, $errorBag);

        $mediaName = pathinfo($validated['name'], PATHINFO_FILENAME);
        $extension = pathinfo($validated['name'], PATHINFO_EXTENSION);

        if (blank($extension)) {
            $extension = $media->extension;
        }

        $fileName = "{$mediaName}.{$extension}";

        app(Filesystem::class)->syncFileNames($media->forceFill([
            'file_name' => $fileName,
            'name' => $mediaName,
        ]));

        $media->save();

        return $media;
    }
}
