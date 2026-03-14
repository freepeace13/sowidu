<?php

namespace App\Actions\Media;

use App\Models\Category;
use App\Models\User;
use App\Services\MediaFileService;
use App\Traits\InteractsWithImpersonator;
use App\Traits\WithAccountCategories;
use Illuminate\Support\Facades\Validator;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class TagMediaWithCategory
{
    use InteractsWithImpersonator, WithAccountCategories;

    public function tag(User $user, Media $media, array $inputs, ?int $teamId = null)
    {
        // TODO Check if user is allowed to tag category on this `media` (writable/readable)
        $validated = Validator::make($inputs, [
            'category' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (Category::query()
                        ->ownedBy($this->account())
                        ->whereName($value)
                        ->doesntExist()
                    ) {
                        $fail('Category not found, please refresh the page and try again.');
                    }
                },
            ],
        ])->validate();

        (new MediaFileService)->forUser($user)
            ->tagCategory($media, $this->account(), $validated['category']);
    }
}
