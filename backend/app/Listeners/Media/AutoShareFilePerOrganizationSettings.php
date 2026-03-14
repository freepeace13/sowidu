<?php

namespace App\Listeners\Media;

use App\Actions\Media\AutoShare\OrganizationFileAutoShareToRoles;
use App\Events\Media\NewMediaAdded;
use App\Models\Employee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AutoShareFilePerOrganizationSettings implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * New `MediaFIle` has been added
     *
     * @return void
     */
    public function handle(NewMediaAdded $event)
    {
        $mediaFile = $event->mediaFile;

        if (!$event->mediaFile->model instanceof Employee) {
            return;
        }

        $employeeUploader = $event->mediaFile->model;
        $company = $employeeUploader->employer;

        (new OrganizationFileAutoShareToRoles)
            ->execute(
                $employeeUploader = $event->mediaFile->model,
                $company,
                $mediaFile,
            );
    }
}
