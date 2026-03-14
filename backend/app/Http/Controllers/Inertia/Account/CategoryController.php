<?php

namespace App\Http\Controllers\Inertia\Account;

use App\Actions\Category\CreateCategory;
use App\Actions\Category\DestroyCategory;
use App\Actions\Category\UpdateCategory;
use App\Enums\Permissions;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Category;
use App\Traits\InteractsWithImpersonator;
use App\Traits\WithOrganizationEssentials;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends InertiaController
{
    use InteractsWithImpersonator, WithOrganizationEssentials;

    public function __construct()
    {
        $this->middleware(
            'permission:' . Permissions::CAN_MANAGE_ORGANIZATION_CATEGORIES,
            ['only' => ['update', 'destroy', 'store']],
        );
    }

    public function store(Request $request, CreateCategory $creator)
    {
        $category = $creator->create($request->user(), $this->getCurrentTeam(), $request->all());

        return redirect()->route('account.categories.show', [
            'category' => $category->name,
        ]);
    }

    public function show($category)
    {
        $account = $this->account();

        return Inertia::render('Account/CategorySettings', [
            'category' => (new CategoryTransformer(
                $account->findCategoryByName($category),
            ))
                ->withAutoShareSettings()
                ->withMediaSettingsAutoSharedToRoles($account->settings()->media()->getRolesForAutoSharing())
                ->resolve(),
            'roles' => $this->retrieveOrganizationRoles(),
        ]);
    }

    public function update(Request $request, Category $category, UpdateCategory $updater)
    {
        $updatedCategory = $updater->update(
            $request->user(),
            $this->getCurrentTeam(),
            $category,
            $request->all(),
        );

        return redirect()->route(
            'account.categories.show',
            ['category' => $updatedCategory->name],
        );
    }

    public function destroy(Request $request, Category $category, DestroyCategory $destroyer)
    {
        $account = $this->account();

        $destroyer->destroy($request->user(), $category);

        return redirect()->route('account.categories.show', [
            'category' => head(
                $account->categories()
                    ->get(['name'])
                    ->pluck('name')
                    ->toArray(),
            ),
        ]);
    }
}
