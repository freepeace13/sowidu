<?php

namespace App\Actions\Media\AutoShare;

use App\Actions\Media\Share\CreateMediaSharing;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Repositories\RoleRepository;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class OrganizationFileAutoShareToRoles
{
    public function execute(User|Employee $uploader, Company $company, Media $mediaFile)
    {
        // Employee uploader is not the owner - Share this file!
        $settings = $company->settings()->media();

        // Get organization settings where this file will be shared
        $shareToRoles = $settings->getRolesForAutoSharing();

        $roleRepository = RoleRepository::createFor($company);

        $shareToRolesIds = $roleRepository
            ->getRolesFrom($shareToRoles)
            ->pluck('id');

        // Get members using roles defined on organization settings
        $company
            ->employees()
            ->whereHas('roles', fn ($query) => $query->whereIn('id', $shareToRolesIds))
            ->get()
            ->each(function ($employee) use ($uploader, $mediaFile) {
                if ($employee->is($uploader)) {
                    return;
                }

                (new CreateMediaSharing)
                    ->autoShare()
                    ->create(
                        $uploader,
                        $mediaFile->uuid,
                        [
                            'member_id' => $employee->getKey(),
                            'member_type' => class_basename($employee),
                        ],
                    );
            });
    }
}
