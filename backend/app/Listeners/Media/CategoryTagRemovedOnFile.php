<?php

namespace App\Listeners\Media;

use App\Actions\Media\Share\RemoveMediaSharing;
use App\Events\Media\MediaTagCategoryRemoved;
use App\Models\Employee;
use Illuminate\Contracts\Queue\ShouldQueue;

class CategoryTagRemovedOnFile implements ShouldQueue
{
    /**
     * Remove auto-sharing of file by category
     */
    public function handle(MediaTagCategoryRemoved $event)
    {
        $mediaFile = $event->mediaFile;

        if (!$mediaFile->model instanceof Employee) {
            return;
        }

        $employeeUploader = $mediaFile->model;
        $organization = $employeeUploader->employer;

        // Remove auto_shared file except to those roles on `auto_share`
        $autoSharedToRoles = $organization->settings()->media()->getRolesForAutoSharing();

        $mediaFile->shares()
            ->where('is_auto_shared', true)
            ->with(['person'])
            ->get()
            ->each(function ($mediaShare) use ($autoSharedToRoles, $employeeUploader, $mediaFile) {
                /** @var \App\Models\Employee */
                $employee = $mediaShare->person;

                // Check employee has roles
                if ($employee->roles()->whereNotIn('name', $autoSharedToRoles)->doesntExist()) {
                    // This employee role is still valid for auto_sharing
                    return;
                }
                // Remove auto_shared_file to this employee
                (new RemoveMediaSharing)->remove(
                    $employeeUploader,
                    $mediaFile->uuid,
                    [
                        'member_id' => $employee->getKey(),
                        'member_type' => class_basename($employee),
                    ],
                );
            });
    }
}
