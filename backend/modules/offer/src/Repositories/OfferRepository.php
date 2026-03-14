<?php

namespace Modules\Offer\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Modules\Offer\Enums\OfferStatus;
use Modules\Offer\Enums\OfferType;
use Modules\Offer\Models\Offer;

class OfferRepository
{
    protected Builder $query;

    public function __construct(protected Model $user, protected ?Model $company = null)
    {
        $this->query = $this->newQuery();
    }

    public static function make(Model $user, ?Model $company): static
    {
        return new static($user, $company);
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }

    public function getAuthUserRecipientableIds(Model $user): array
    {
        $addressbookClass = config('offer.models.addressbook');

        return Cache::remember(
            "auth.user.addressbook.{$user->id}.ids",
            now()->addMinutes(10),
            fn () => $addressbookClass::where('email', $user->email)->pluck('id')
                ->toArray(),
        );
    }

    public function newQuery(): Builder
    {
        $addressbookClass = config('offer.models.addressbook');

        return Offer::query()
            ->when(
                $this->company,
                function ($query) {
                    $query->where('company_id', $this->company->getKey());
                },
            )
            ->when(
                blank($this->company),
                function ($query) use ($addressbookClass) {
                    // Private user offers
                    $query->whereIn(
                        'recipientable_id',
                        $this->getAuthUserRecipientableIds($this->user),
                    )
                        ->where('recipientable_type', $addressbookClass)
                        ->whereNotIn('status', [
                            OfferStatus::DRAFT,
                        ]);
                },
            );
    }

    public function filter(array $filters): Builder
    {
        return $this->query
            ->when(
                $filters['q'] ?? null,
                function ($query, $search) {
                    $query->whereLike('title', $search)
                        ->orWhereLike('description', $search)
                        ->orWhereLike('internal_id', $search)
                        ->orWhereHas(
                            'recipientable',
                            fn ($q) => $q->search($search),
                        );
                },
            )
            ->when(
                $filters['offerDate'] ?? null,
                function ($query, $date) {
                    $query->whereDate('offer_date', $date);
                },
            )
            ->when(
                $filters['status'] ?? null,
                function ($query, $status) {
                    $query->where('status', OfferStatus::from($status));
                },
            )
            ->when(
                $filters['type'] ?? null,
                function ($query, $type) {
                    $query->where('type', OfferType::from($type));
                },
            );
    }
}
