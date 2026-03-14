<?php

namespace App\Listeners\Organization;

use App\Actions\Media\AutoShare\RemoveAutoSharedFilesToEmployee;
use App\Events\Organization\EmployeeRolesUpdated;
use App\Jobs\Media\AutoShareMediaBasedOnOrganizationSettingsJob;
use App\Services\MediaFileService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class CheckEmployeeRolesForAutoSharing implements ShouldQueue
{
    public function handle(EmployeeRolesUpdated $event)
    {
        // Check if employee role is valid for auto-sharing
        $employee = $event->employee;
        $company = $employee->employer;

        // Get organization settings where this file will be shared
        $autoShareToRoles = $company->settings()->media()->getRolesForAutoSharing();

        $previousRoles = $event->previousRoles;

        if (!$employee->hasRole($autoShareToRoles)) {
            // Check if previous roles are shared to this employee
            $previousRoles = $event->previousRoles;

            if (collect($previousRoles)->contains(fn ($role) => in_array($role, $autoShareToRoles))) {
                // This employee role has revoked from auto-sharing
                // Remove shared-file from this employee
                RemoveAutoSharedFilesToEmployee::run($employee);
            }

            return;
        }

        // Make sure that this employee has shared-media
        MediaFileService::makeForCompany($company)
            ->get()
            ->each(
                fn (MediaFile $mediaFile) => AutoShareMediaBasedOnOrganizationSettingsJob::dispatch($mediaFile, $company),
            );
    }
}
