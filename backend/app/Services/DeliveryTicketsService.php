<?php

namespace App\Services;

use App\Models\Company;
use App\Models\DeliveryTicket;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class DeliveryTicketsService
{
    protected $query;

    public function __construct(protected User $user, protected Company $company)
    {
        $this->query = $this->newQuery();
    }

    /**
     * @return DeliveryTicket|static|Builder
     */
    public function __call($method, $parameters)
    {
        $result = $this->query->{$method}(...$parameters);

        if ($result instanceof Builder) {
            return $this;
        }

        $this->setQuery($this->newQuery());

        return $result;
    }

    /** @return Builder|static|DeliveryTicket|self */
    public static function make(User $user, Company $company): static
    {
        return new static($user, $company);
    }

    /** @return Builder|static */
    public function setQuery(Builder|\Illuminate\Database\Query\Builder $query): self
    {
        $this->query = $query;

        return $this;
    }

    /** @return Builder|static */
    public function newQuery(): Builder
    {
        return DeliveryTicket::query()
            ->where('company_id', $this->company->id);
    }

    public function filters(array $filters = []): self
    {
        $this->query->when(
            $q = $filters['q'] ?? null,
            fn (Builder $query) => $query
                ->search($q)
                ->orWhereRelation('materials', 'details', 'like', "%$q%")
                ->orWhereHas(
                    'deliveryAddress',
                    fn (Builder $query) => $query->searchFullAddress($q),
                )
                ->orWhereHas(
                    'order',
                    fn (Builder $query) => $query->search($q),
                ),
        )
            ->when(
                $type = $filters['type'] ?? null,
                fn (Builder $query) => $query->where('type', $type),
            )
            ->when(
                $invoiceStatus = $filters['invoiceStatus'] ?? null,
                fn (Builder $query) => $query->where('is_paid', $invoiceStatus),
            )
            ->when(
                $deliveryDates = $filters['deliveryDates'] ?? null,
                function (Builder $query) use ($deliveryDates) {
                    $dates = $this->getDates($deliveryDates);

                    if (!$dates) {
                        return;
                    }

                    $fromDate = data_get($dates, 'from');
                    $toDate = data_get($dates, 'to');

                    if (blank($fromDate) && blank($toDate)) {
                        return;
                    }

                    if (Carbon::parse($fromDate)->greaterThanOrEqualTo($toDate)) {
                        return $query->whereDate('delivery_date', $fromDate);
                    }

                    $this->query->where(
                        fn ($q) => $q->whereBetween(
                            'delivery_date',
                            [
                                $fromDate,
                                $toDate,
                            ],
                        ),
                    );
                },
            );

        return $this;
    }

    protected function getDates($dates): ?array
    {
        $dates = collect($dates);

        if (
            !$dates->hasAny([
                'from',
                'to',
            ])
        ) {
            return null;
        }

        return [
            'from' => $dates->get('from'),
            'to' => $dates->get('to'),
        ];
    }

    public function getTotalPurchasingAndSellingPrices(DeliveryTicket $deliveryTicket): array
    {
        $currency = CacheService::getCompanyCurrency($deliveryTicket->company);
        $purchasingPrice = $deliveryTicket->materials->sum(
            fn ($material) => $material->quantity * $material->purchasing_price,
        );

        $sellingPrice = $deliveryTicket->materials->sum(
            fn ($material) => $material->quantity * $material->selling_price,
        );

        return [
            'purchasing_price' => $purchasingPrice,
            'selling_price' => $sellingPrice,
            'purchasing_price_formatted' => number_to_money(
                $purchasingPrice,
                $currency,
            ),
            'selling_price_formatted' => number_to_money(
                $sellingPrice,
                $currency,
            ),
        ];
    }
}
