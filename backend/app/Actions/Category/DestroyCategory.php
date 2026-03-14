<?php

namespace App\Actions\Category;

use App\Events\Category\CategoryDeleted;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class DestroyCategory
{
    public function destroy(User $author, Category $category)
    {
        Gate::forUser($author)->authorize('destroy', $category);

        abort_if($category->is_default, 403, 'Could not delete default category.');

        event(new CategoryDeleted($category, $author));

        $category->delete();
    }
}
