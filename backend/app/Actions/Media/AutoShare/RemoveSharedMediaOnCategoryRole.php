<?php

namespace App\Actions\Media\AutoShare;

use App\Models\Category;
use App\Models\Company;
use App\Models\Employee;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\MediaLibrary\MediaCollections\Models\MediaShare;

class RemoveSharedMediaOnCategoryRole
{
    public function remove(Company $company, Category $category, string $role)
    {
        MediaShare::resolveRelationUsing('mediaFile', function (MediaShare $mediaShare) {
            return $mediaShare->morphTo(Media::class, 'shareable_type', 'shareable_id', 'id');
        });

        $category = $category->name;

        $company
            ->employees()
            ->whereHas('roles', fn ($query) => $query->where('name', $role))
            ->get(['id', 'company_id'])
            ->each(function (Employee $employee) use ($role, $category) {
                if ($employee->roles()->where('name', $role)->doesntExist()) {
                    return;
                }

                // Get all media shared from this employee
                MediaShare::query()
                    ->with(['mediaFile'])
                    ->whereCanReadOnly()
                    ->where('shareable_type', (new Media)->getMorphClass())
                    ->whereAccount($employee)
                    ->where('is_auto_shared', true)
                    ->get()
                    ->each(function (MediaShare $mediaShare) use ($category) {
                        $mediaFile = $mediaShare['Packages\MediaLibrary\MediaCollections\Models\Media'];

                        if (blank($mediaFile?->category)) {
                            return; // Ignore media with no category
                        }

                        $mediaCategory = $mediaFile->category;

                        if (strtolower($mediaCategory) !== strtolower($category)) {
                            return; // Ignore media with different category
                        }

                        $mediaShare->delete(); // Remove media sharing from employee
                    },
                    );
            });
    }
}
