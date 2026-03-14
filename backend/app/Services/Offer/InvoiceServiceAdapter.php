<?php

declare(strict_types=1);

namespace App\Services\Offer;

use App\Models\DeductionManual;
use App\Models\Invoice;
use App\Modules\Invoice\Services\InvoiceSummaryService;
use App\Transformers\Invoice\DeductionManualTransformer;
use Illuminate\Database\Eloquent\Model;
use Modules\Offer\Contracts\External\InvoiceServiceContract;

class InvoiceServiceAdapter implements InvoiceServiceContract
{
    public function __construct(
        protected InvoiceSummaryService $invoiceSummaryService,
        protected DeductionManualTransformer $deductionManualTransformer,
    ) {}

    public function find(int $id): ?Model
    {
        return Invoice::find($id);
    }

    public function getSummary(Model $invoice): array
    {
        /** @var Invoice $invoice */
        return $this->invoiceSummaryService->getSummary($invoice);
    }

    public function transformDeduction(Model $deduction): array
    {
        /** @var DeductionManual $deduction */
        return $this->deductionManualTransformer->transform($deduction);
    }
}
