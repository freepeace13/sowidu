<?php

namespace App\Actions\Media\AutoShare;

use App\Models\Category;
use App\Models\Company;
use App\Models\Employee;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class AutoShareFileWithTaggedCategory
{
    public function share(
        Employee $uploader,
        Media $mediaFile,
        Company $company,
        string $category,
    ) {
        // Find account `category` instance
        /** @var Category */
        $category = Category::query()
            ->ownedBy($company)
            ->whereName($category)
            ->firstOrFail();

        // Get roles where should this file will be auto-shared
        $sharableRoles = $category->settings()
            ->autoShare()
            ->all()
            ->toArray();

        // Share this file to the roles
        $company->employees()
            ->whereHas('roles', function ($query) use ($sharableRoles) {
                $query->whereIn('name', $sharableRoles);
            })
            ->get()
            ->each(
                function (Employee $employee) use ($uploader, $mediaFile) {
                    (new CreateAutoShareFileToEmployee)->create(
                        $uploader,
                        $mediaFile->uuid,
                        [
                            'member_id' => $employee->getKey(),
                            'member_type' => class_basename($employee),
                        ],
                    );
                },
            );
    }
}
