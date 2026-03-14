<?php

namespace App\Console\Commands\Invoice;

use App\Enums\InvoiceStatus;
use App\Events\Invoice\InvoiceSent;
use App\Listeners\Invoice\GenerateInvoiceFinalData;
use App\Models\Invoice;
use Illuminate\Console\Command;

class GenerateInvoiceFinalDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:generate-final-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoice final data.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Generate invoice final data
        Invoice::query()
            ->where('status', InvoiceStatus::SENT())
            ->orWhere('status', InvoiceStatus::PAID())
            ->with(['order', 'company', 'items', 'taxes'])
            ->chunk(
                10,
                function ($invoices) {
                    $invoices->each(function ($invoice) {
                        (new GenerateInvoiceFinalData)->handle(new InvoiceSent($invoice));
                        $this->info(
                            "Invoice final data generated for invoice: {$invoice->uuid}",
                        );
                    });
                },
            );

        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }
}
