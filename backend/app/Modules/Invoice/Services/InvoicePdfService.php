<?php

namespace App\Modules\Invoice\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\File;

class InvoicePdfService
{
    public function __construct(protected Invoice $invoice) {}

    public static function make(Invoice $invoice): static
    {
        return new static($invoice);
    }

    protected function fileName(): string
    {
        return $this->invoice->internal_id . '.pdf';
    }

    protected function storagePath(): string
    {
        return storage_path('app/public/invoices/');
    }

    public function getFullPath(): string
    {
        return $this->storagePath() . $this->fileName();
    }

    public function getStoragePath(): string
    {
        return 'invoices/' . $this->fileName();
    }

    public function getDomPdfPath(): string
    {
        return $this->storagePath() . str($this->fileName())
            ->replace('.pdf', '-dompdf.pdf');
    }

    public function hasPdf(): bool
    {
        // Always generate when invoice is draft
        if ($this->invoice->isDraft()) {
            return false;
        }

        return File::exists($this->getFullPath());
    }

    public function pdfa()
    {
        // PdfaInvoiceBuilder::make($invoice, $domPdfPath)
        //     ->setDestinationPath($finalPdfPath)
        //     ->merge();
    }
}
