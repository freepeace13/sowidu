<?php

namespace App\Console\Commands\Invoice;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Modules\Invoice\Services\InvoiceSummaryService;
use Illuminate\Console\Command;

class SaveInvoicesSummaryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:save-summaries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save invoice summaries';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Save invoice summaries
        Invoice::query()
            ->where('status', '!=', InvoiceStatus::DRAFT)
            ->chunk(10, function ($invoices) {
                $invoices->each(function ($invoice) {
                    if (!$invoice->isAlreadySent()) {
                        return;
                    }

                    InvoiceSummaryService::run($invoice)
                        ->saveInvoiceSummarries();

                    $this->info("Saved invoice summaries for invoice {$invoice->id}");
                });
            });

        $this->info('All invoice summaries have been saved.');

        return Command::SUCCESS;
    }
}
