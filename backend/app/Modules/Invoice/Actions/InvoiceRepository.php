<?php

namespace App\Modules\Invoice;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Builder
 */
class InvoiceRepository
{
    protected $query;

    public function __construct(
        protected User $user,
        protected Company $company,
    ) {
        $this->query = $this->newQuery();
    }

    /**
     * @return Invoice|static|Builder
     */
    public function __call(
        $method,
        $parameters,
    ) {
        $result = $this->query->{$method}(...$parameters);

        if ($result instanceof Builder) {
            return $this;
        }

        $this->setQuery($this->newQuery());

        return $result;
    }

    /** @return Builder|static|Invoice|self */
    public static function make(
        User $user,
        Company $company,
    ): static {
        return new static(
            $user,
            $company,
        );
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
        return Invoice::query()
            ->where(
                'company_id',
                $this->company->id,
            );
    }

    public function filters(array $filters): self
    {
        $this->query
            ->when(
                $type = $filters['type'] ?? null,
                fn (Builder $query) => $query->where('type', $type),
            )
            ->when(
                filled($invoiceStatus = data_get($filters, 'invoiceStatus')),
                fn (Builder $query) => $query->where('status', $invoiceStatus),
            )
            ->when(
                $q = $filters['q'] ?? null,
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query->whereLike(
                        'notes',
                        "%$q%",
                    )
                        ->orWhereLike('subject', "%$q%")
                        ->orWhereLike('description', "%$q%")
                        ->orWhereLike('internal_id', "%$q%")
                        ->orWhereLike('external_id', "%$q%")
                        ->orWhereHas(
                            'deliveryAddress',
                            fn (Builder $query) => $query->searchFullAddress($q),
                        )
                        ->orWhereHas(
                            'order',
                            fn (Builder $query) => $query->search($q),
                        ),
                ),
            )
            ->when(
                $paymentDate = $filters['paymentDate'] ?? null,
                function (Builder $query) use ($paymentDate) {
                    $dates = $this->getDates($paymentDate);

                    if (!$dates) {
                        return;
                    }

                    $fromDate = data_get($dates, 'from');
                    $toDate = data_get($dates, 'to');

                    if (blank($fromDate) && blank($toDate)) {
                        return;
                    }

                    if (Carbon::parse($fromDate)->greaterThanOrEqualTo($toDate)) {
                        return $query->whereDate('payment_date', $fromDate);
                    }

                    // Single Date
                    if (
                        Carbon::parse($fromDate)->equalTo(Carbon::parse($toDate))
                    ) {
                        return $query->where(
                            fn ($q) => $q->whereDate('payment_date', $fromDate)
                                ->orWhereDate('payment_date', $toDate),
                        );
                    }

                    $this->query->where(
                        fn ($q) => $q->whereBetween(
                            'payment_date',
                            [
                                $fromDate,
                                $toDate,
                            ],
                        ),
                    );
                },
            )
            ->when(
                $dateAdded = $filters['dateAdded'] ?? null,
                function (Builder $query) use ($dateAdded) {
                    $dates = $this->getDates($dateAdded);

                    $fromDate = data_get($dates, 'from');

                    if (!$dates || blank($fromDate)) {
                        return;
                    }

                    $toDate = data_get($dates, 'to') ?? today()->toDateString();

                    if (blank($fromDate) && blank($toDate)) {
                        return;
                    }

                    if (Carbon::parse($fromDate)->greaterThanOrEqualTo($toDate)) {
                        return $query->whereDate('created_at', $fromDate);
                    }

                    // Single Date
                    if (
                        Carbon::parse($fromDate)->equalTo(Carbon::parse($toDate))
                    ) {
                        return $query->where(
                            fn ($q) => $q->whereDate('created_at', $fromDate)
                                ->orWhereDate('created_at', $toDate),
                        );
                    }

                    $this->query->where(
                        fn ($q) => $q->whereBetween(
                            'created_at',
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

    public function deductionCandidates(Invoice $invoice)
    {
        $invoiceDeductionIds = $invoice->deductions()
            ->where('deductible_type', get_morph_class(Invoice::class))
            ->pluck('deductible_id');

        $this->query->where('order_id', $invoice->order_id)
            ->whereNot('id', $invoice->id)
            ->notDraft()
            ->doesntHave('deductible')
            ->whereNotIn('id', $invoiceDeductionIds)
            // ->where('payment_date', '>=', $invoice->payment_date)
            ->latest();

        return $this->query;
    }
}
