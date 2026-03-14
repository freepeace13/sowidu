<?php

namespace Modules\Invoicify\Models;

use App\Models\InvoiceItem as BaseInvoiceItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends BaseInvoiceItem
{
    /** @return BelongsTo|Invoice */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Invoicify\Database\Factories\InvoiceItemFactory::new();
    }
}
