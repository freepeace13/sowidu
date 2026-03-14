<?php

namespace App\Actions\Media\AutoShare;

use App\Models\Company;
use App\Models\Employee;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\MediaLibrary\MediaCollections\Models\MediaShare;

class RemoveAutoSharedFilesToRole
{
    public function remove(Company $company, string $role)
    {
        MediaShare::resolveRelationUsing('mediaFile', function (MediaShare $mediaShare) {
            return $mediaShare->morphTo(Media::class, 'shareable_type', 'shareable_id', 'id');
        });

        // Get employee ids from company with given `role`
        $company
            ->employees()
            ->whereHas('roles', fn ($query) => $query->where('name', $role))
            ->get()
            ->each(function (Employee $employee) use ($role) {
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
                    ->each(
                        fn ($mediaShare) => $mediaShare->delete(),
                    );
            });
    }
}
