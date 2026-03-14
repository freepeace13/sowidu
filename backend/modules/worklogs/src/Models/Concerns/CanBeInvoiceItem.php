<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Modules\WorkLogs\Contracts\External\ModelConfigContract;

/**
 * Trait for models that can be invoice items.
 *
 * Module-local version that uses ModelConfigContract
 * instead of importing from main app.
 */
trait CanBeInvoiceItem
{
    public function markAsPaid()
    {
        return $this->update([
            'is_paid' => true,
        ]);
    }

    public function markAsUnPaid()
    {
        return $this->update([
            'is_paid' => false,
        ]);
    }

    public function isPaid(): bool
    {
        return $this->is_paid;
    }

    public function isUnPaid(): bool
    {
        return !$this->is_paid;
    }

    public function scopeUnInvoiced(Builder $query): Builder
    {
        return $query->doesntHave('invoiceItem');
    }

    public function scopeUnPaid(Builder $query): Builder
    {
        return $query->where('is_paid', false);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('is_paid', true);
    }

    public function invoiceItem()
    {
        $modelConfig = app(ModelConfigContract::class);

        return $this->morphOne($modelConfig->getInvoiceItemModel(), 'item');
    }

    public function isInvoiced(): bool
    {
        return $this->invoiceItem()
            ->exists();
    }
}
