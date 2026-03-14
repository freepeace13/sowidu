<?php

namespace App\Actions\Media\AutoShare;

use App\Actions\Traits\AsAction;
use App\Models\Employee;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;
use Packages\MediaLibrary\MediaCollections\Models\MediaShare;

class RemoveAutoSharedFilesToEmployee
{
    use AsAction;

    public function handle(Employee $employee)
    {
        MediaShare::resolveRelationUsing('mediaFile', function (MediaShare $mediaShare) {
            return $mediaShare->morphTo(MediaFile::class, 'shareable_type', 'shareable_id', 'id');
        });

        // Get all media shared from this employee
        MediaShare::query()
            ->with(['mediaFile'])
            ->whereCanReadOnly()
            ->where('shareable_type', (new MediaFile)->getMorphClass())
            ->whereAccount($employee)
            ->where('is_auto_shared', true)
            ->get()
            ->each(
                fn ($mediaShare) => $mediaShare->delete(),
            );
    }
}
