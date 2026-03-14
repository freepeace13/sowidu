<?php

namespace App\Listeners\Organization;

use App\Events\Organization\OrganizationMediaSettingsUpdated;
use App\Jobs\Media\AutoShareMediaBasedOnOrganizationSettingsJob;
use App\Services\MediaFileService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class OrganizationUpdatedAutoShareFileToRoles implements ShouldQueue
{
    /**
     * Share `MediaFile` based on `Organization` settings `media.auto_share.to_roles`
     */
    public function handle(OrganizationMediaSettingsUpdated $event)
    {
        $changes = $event->changes;

        if (!array_has($changes, 'settings')) {
            return;
        }

        $organization = $event->organization;

        MediaFileService::makeForCompany($organization)
            ->get()
            ->each(
                fn (MediaFile $mediaFile) => AutoShareMediaBasedOnOrganizationSettingsJob::dispatch($mediaFile, $organization),
            );
    }
}
