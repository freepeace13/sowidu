<?php

namespace App\Modules\Invoice\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Modules\WorkLogs\Models\WorkLog;

class InvoiceItemService
{
    protected Invoice $invoice;

    public function __construct(protected InvoiceItem $invoiceItem) {}

    public static function make(InvoiceItem $invoiceItem)
    {
        return new static($invoiceItem);
    }

    public function isWorkLog(): bool
    {
        return same_morph_alias(WorkLog::class, $this->invoiceItem->item_type);
    }

    public function editable(): bool
    {
        return $this->invoiceItem->invoice->isDraft();
    }

    public function deletable(): bool
    {
        return $this->invoiceItem->invoice->isDraft();
    }

    public function userCanEdit(): bool
    {
        return $this->editable();
    }

    public function userCanEditQuantity(): bool
    {
        return
            $this->editable() &&
            !$this->isWorkLog();
    }

    public function userCanEditPrice(): bool
    {
        return $this->editable()
            && !$this->isWorkLog();
    }

    public function userCanDelete(): bool
    {
        return $this->deletable();
    }
}
