<?php

namespace App\Listeners\Media;

use App\Actions\Media\Share\RemoveMediaSharing;
use App\Events\Media\MediaTaggedWithCategory;
use App\Models\Category;
use App\Models\Employee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;

class VerifyAutoSharedFileCategory implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(MediaTaggedWithCategory $event)
    {
        /** @var \Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile */
        $mediaFile = $event->mediaFile;
        // Check if the update includes the `category` OR `Uploader` is not an employee instance
        if (
            !Arr::has($event->changes, 'category')
            || !$mediaFile->model instanceof Employee
        ) {
            return;
        }

        $employeeUploader = $mediaFile->model;
        $company = $employeeUploader->employer;

        $category = Category::query()
            ->ownedBy($company)
            ->whereName($mediaFile->category)
            ->firstOrFail();

        $validSharableRoles = $category->settings()
            ->autoShare()
            ->all()
            ->toArray();

        $companySettings = $company->settings()
            ->media()
            ->get('auto_share_to_roles');

        $validSharableRoles = array_merge($validSharableRoles, $companySettings);

        // Iterate on mediaFile->auto_shared_to and remove sharing_instance to employee if role is not valid anymore
        $mediaFile->shares()
            ->where('is_auto_shared', true)
            ->with(['person'])
            ->get()
            ->each(function ($mediaShare) use ($validSharableRoles, $employeeUploader, $mediaFile) {
                /** @var \App\Models\Employee */
                $employee = $mediaShare->person;

                // Check employee has roles
                if ($employee->roles()->whereNotIn('name', $validSharableRoles)->doesntExist()) {
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
