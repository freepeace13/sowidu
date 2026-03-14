<?php

namespace App\Http\Controllers\Inertia\Organization\Settings;

use App\Actions\Organization\Settings\UpdateOrganizationMediaSettings;
use App\Enums\CompanySetting;
use App\Http\Controllers\Inertia\InertiaController;
use App\Traits\InteractsWithImpersonator;
use App\Traits\WithOrganizationEssentials;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrganizationMediaSettingsController extends InertiaController
{
    use InteractsWithImpersonator, WithOrganizationEssentials;

    public function index()
    {
        $organization = $this->getCurrentTeam();

        abort_if(blank($organization), 'Please login using your organization.');

        return Inertia::render('Account/Organization/Settings/MediaAutoShare', [
            'roles' => fn () => $organization
                ->roles()
                ->get(['name'])
                ->pluck('name')
                ->toArray(),
            'autoShareToRoles' => $organization
                ->settings()
                ->media()
                ->get(CompanySetting::AUTO_SHARE_TO_ROLES()),
        ]);
    }

    public function update(
        Request $request,
        string $medium,
        UpdateOrganizationMediaSettings $updater,
    ) {
        $updater->update(
            $request->user(),
            $this->getCurrentTeam(),
            $medium,
            $request->all(),
        );

        return back(303);
    }
}
