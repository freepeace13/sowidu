<?php

namespace App\Console\Commands\Invoice\Utils;

use App\Models\Invoice;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class GenerateFinalInvoiceNumberCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:generate-final-invoice-number';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate final invoice number for all invoices';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Generating final invoice number...');

        Invoice::chunk(10, function ($invoices) {
            foreach ($invoices as $invoice) {
                $internalId = $invoice->internal_id;

                if (!str($internalId)->startsWith('TMP') || $invoice->isDraft()) {
                    continue;
                }

                $this->info("Generating final invoice number for invoice ID: {$invoice->id}. Internal ID: {$internalId}");

                $invoice->load(['contractor', 'order', 'order.client']);

                $permanentInternalId = InvoiceService::run($invoice)
                    ->savePermanentInternalId();

                $checker = Invoice::query()
                    ->where('internal_id', $permanentInternalId)
                    ->count();

                if ($checker > 1) {
                    $this->error("Duplicate invoice number detected for invoice ID: {$invoice->id}. Internal ID: {$permanentInternalId}");

                    // Rollback the transaction
                    $invoice->update([
                        'internal_id' => $internalId,
                    ]);

                    continue;
                }

                $this->info("Final invoice number generated: {$permanentInternalId}");
            }
        });

        $this->info('Final invoice number generation completed.');

        return Command::SUCCESS;
    }
}
