<?php

namespace App\Console\Commands\Invoice;

use App\Jobs\Invoice\CrawlInvoicePreviewJob;
use App\Models\Invoice;
use Illuminate\Console\Command;

class PopulateInvoicePreviewLayoutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:populate-preview-layout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates the invoice preview layout.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $totalCount = 0;

        Invoice::query()
            ->select(['id', 'preview_layout', 'uuid'])
            ->notDraft()
            ->whereNull('preview_layout')
            ->chunk(10, function ($invoices) use (&$totalCount) {
                $bar = $this->output->createProgressBar($invoices->count());
                $bar->start();

                $totalCount += $invoices->count();

                foreach ($invoices as $invoice) {
                    if (filled($invoice->preview_layout)) {
                        continue;
                    }

                    dispatch(new CrawlInvoicePreviewJob($invoice));

                    $this->info("Crawling invoice: {$invoice->id}...");
                    $bar->advance();
                }

                $bar->finish();
            });

        $this->info("\nTotal invoices processed: {$totalCount}");
        $this->info('All invoices have been processed.');

        return Command::SUCCESS;
    }
}
