<?php

namespace App\Listeners\Media;

use App\Actions\Media\AutoShare\AutoShareFileWithTaggedCategory;
use App\Events\Media\MediaTaggedWithCategory;
use App\Models\Employee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;

class AutoShareFileWithCategory implements ShouldQueue
{
    /**
     * Handle MediaFile if it will be shared to organization employees
     *
     * @return void
     */
    public function handle(MediaTaggedWithCategory $event)
    {
        $mediaFile = $event->mediaFile;
        // Check if the update includes the `category` OR `Uploader` is not an employee instance
        if (
            !Arr::has($event->changes, 'category')
            || !$mediaFile->model instanceof Employee
            || blank($mediaFile->category)
        ) {
            return;
        }

        (new AutoShareFileWithTaggedCategory)->share(
            $employeeUploader = $mediaFile->model,
            $mediaFile,
            $employeeUploader->employer,
            $mediaFile->category,
        );
    }
}
