<?php

namespace App\Listeners\Category;

use App\Events\Category\CategoryCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class SaveCategoryAutoSharedSettings implements ShouldQueue
{
    public $afterCommit = true;

    public function handle(CategoryCreated $event)
    {
        $category = $event->category;
        $company = $event->category->ownerable;

        // Fetch all roles enabled on `media_settings`
        $mediaSettingsAutoSharedRoles = collect($company->settings()
            ->media()
            ->getRolesForAutoSharing())
            ->map(fn ($role) => $company->roles()->where('name', $role)->value('name'))
            ->toArray();

        $category
            ->settings()
            ->autoShare()
            ->update($mediaSettingsAutoSharedRoles);
    }
}
