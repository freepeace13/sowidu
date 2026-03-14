<?php

namespace App\Observers;

use App\Events\Media\MediaTaggedWithCategory;
use App\Support\Models\InteractsWithModelChanges;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class MediaFileCategoryObserver
{
    use InteractsWithModelChanges;

    public function updated(MediaFile $mediaFile)
    {
        if ($mediaFile->isDirty('category')) {
            if (blank($mediaFile->category)) {
                return;
            }

            event(
                new MediaTaggedWithCategory(
                    $mediaFile,
                    $this->getModelColumnChanges($mediaFile),
                ),
            );
        }
    }
}
