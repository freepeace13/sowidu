<?php

namespace App\Jobs\Invoice;

use App\Models\Invoice;
use App\Modules\Invoice\Actions\Preview\CrawlInvoicePreview;
use App\Modules\Invoice\Services\InvoicePdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CrawlInvoicePreviewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 200; // 3 minutes

    public $tries = 1;

    public function __construct(protected Invoice $invoice) {}

    public function handle()
    {
        if (
            InvoicePdfService::make($this->invoice)->hasPdf()
            || filled($this->invoice->preview_layout)
        ) {
            return; // Invoice already has a PDF or preview layout - ignore
        }

        rescue(
            fn () => CrawlInvoicePreview::run($this->invoice),
            null,
            false,
        );
    }
}
