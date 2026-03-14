<?php

namespace App\Services;

use App\Enums\Permissions;
use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use App\Policies\Traits\HandlesTeamAuthorization;
use App\Support\Facades\Impersonate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class MediaFileService
{
    use HandlesTeamAuthorization;

    /**
     * @var Media
     */
    protected $query;

    protected $user;

    public function __construct()
    {
        $this->query = $this->newQuery();
    }

    public function findByUuid(string $uuid): Media
    {
        return $this->query->where('uuid', $uuid)
            ->first();
    }

    public function findByUuidOrFail(string $uuid, array $columns = ['*']): Media
    {
        return $this->query->where('uuid', $uuid)
            ->firstOrFail($columns);
    }

    /**
     * @return MediaFile|Builder|static
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, get_class_methods(get_class()))) {
            return $this->{$method}(...$parameters);
        }

        $result = $this->query->{$method}(...$parameters);

        if ($result instanceof Builder) {
            return $this;
        }

        $this->setQuery($this->newQuery());

        return $result;
    }

    /**
     * @return MediaFile|Builder
     */
    public function newQuery()
    {
        return Media::query();
    }

    protected function setQuery(Builder $query): self
    {
        $this->query = $query;

        return $this;
    }

    public static function make($user, $teamId = null)
    {
        return (new static)->forUser($user, $teamId);
    }

    /** @return static|self */
    public static function makeForCompany(Company $company)
    {
        return (new static)->forCompany($company);
    }

    /**
     * @param  \App\Models\User|\App\Models\Employee  $user
     * @return self|MediaFile
     */
    public function forUser($user, ?int $teamId = null): self
    {
        $this->user = $user;

        $this->query = $this->query
            ->where(function (Builder $query) use ($user) {
                return $query->whereHas(
                    'shares',
                    fn ($query) => $query->whereReadableFor($user),
                )
                    ->orWhere(
                        fn ($query) => $query
                            ->where('model_id', $user->getKey())
                            ->where('model_type', $user->getMorphClass()),
                    );
            });

        return $this;
    }

    public function forCompany(Company $company)
    {
        $employeeIds = $company->employees()
            ->get(['id', 'company_id']);

        return Media::query()
            ->whereIn('model_id', $employeeIds->pluck('id')
                ->toArray(),
            )
            ->where('model_type', $employeeIds->first()
                ->getMorphClass(),
            );
    }

    /**
     * @return Builder
     */
    public function filters(array $filters = [])
    {
        return $this->query
            ->when(
                $address = $filters['address'] ?? null,
                function (Builder $query) use ($address) {
                    return $query->whereHas(
                        'addressTags',
                        fn ($query) => $query->where('complete_address', 'like', "%$address%"),
                    );
                },
            )
            ->when(
                $type = $filters['type'] ?? null,
                function (Builder $query) use ($type) {
                    $filterType = collect(self::allowedMimetypes())
                        ->filter(
                            fn ($value, $key) => in_array($key, Arr::wrap($type)),
                        )
                        ->flatten()
                        ->toArray();

                    return $query->whereIn('mime_type', $filterType);
                },
            )
            ->when(
                $category = $filters['category'] ?? [],
                function (Builder $query) use ($category) {
                    $categories = collect($category);

                    if ($categories->contains('no-category')) {
                        $categories = $categories
                            ->reject(fn ($category) => $category === 'no-category')
                            ->values();

                        return $query->whereNull('category')
                            ->when(
                                $categories->isNotEmpty(),
                                fn ($query) => $query
                                    ->orWhereIn(
                                        'category',
                                        $categories->toArray(),
                                    ),
                            );
                    }

                    return $query->whereIn('category', $category);
                },
            )
            ->when(
                $filters['noAddress'] ?? null,
                fn (Builder $query) => $query->doesntHave('addressTags'),
            )
            ->when(
                $filters['noCategory'] ?? null,
                fn (Builder $query) => $query->whereNull('category'),
            );
    }

    public function tagCategory(Media $media, User|Company $account, string $category): bool
    {
        throw_validation_unless(
            Category::query()
                ->ownedBy($account)
                ->whereName($category)
                ->exists(),
            'Category is not recognized.',
        );

        return $media->update([
            'category' => $category,
        ]);
    }

    public function removeCategoryTag(Media $media): bool
    {
        return $media->update([
            'category' => null,
        ]);
    }

    public function withCategory(): self
    {
        $this->query->whereNotNull('category');

        return $this;
    }

    public static function allowedMimetypes(?string $type = null): array
    {
        return $type ? config('media-library.mime_types')[$type] : config('media-library.mime_types');
    }

    public function canModifyMembers($auth, $media)
    {
        if (!Impersonate::isImpersonating()) {
            return $media->isOwnedBy($auth);
        }

        return $media->exists
            && ($media->isOwnedBy($auth) || $media->isWriteableFor($auth))
            || Impersonate::tenant()->isFounder($auth->user)
            || $this->canShareMedia($media, Impersonate::user(), Impersonate::tenant());
    }

    public function canModifyPermission($auth, $media)
    {
        if (!Impersonate::isImpersonating()) {
            return $media->isOwnedBy($auth);
        }

        return ($media->exists && $media->wasShared())
            && ($media->isOwnedBy($auth) || $media->isWriteableFor($auth));
    }

    public static function companyOwned(Company $company, Media $media): bool
    {
        return (new static)->ownedByCompany($media, $company);
    }

    public function ownedByCompany($media, Company $company): bool
    {
        return $this->forCompany($company)
            ->where('id', $media->getKey())
            ->exists();
    }

    public function canShareMedia($media, User $user, Company $company): bool
    {
        return $this->canRepresentTeam(
            $user,
            $company->id,
            Permissions::CAN_SHARE_MEDIA,
        )
            && $this->ownedByCompany($media, $company);
    }
}
