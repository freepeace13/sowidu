<?php

namespace Modules\Invoicify\Models;

use App\Models\InvoiceManualItem as BaseInvoiceManualItem;

/** @todo Will transfer manual item model in this module soon... */
class InvoiceManualItem extends BaseInvoiceManualItem
{
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Invoicify\Database\Factories\InvoiceManualItemFactory::new();
    }
}
