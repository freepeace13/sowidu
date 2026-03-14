<?php

namespace App\Console\Commands\Utils;

use App\Enums\InvoiceStatus;
use App\Models\DeductionManual;
use App\Models\Invoice;
use App\Models\InvoiceDeduction;
use App\Modules\Invoice\InvoiceService;
use App\Modules\Invoice\Services\InvoiceSummaryService;
use Illuminate\Console\Command;

class MoveInvoiceDeductionsValuesToPivotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:move-invoice-deductions-values {--truncate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (
            $this->option('truncate') && $this->confirm('Do you really want to truncate invoice_deductions table?')
        ) {

            InvoiceDeduction::truncate();
            $this->info('Truncating invoice_deductions table...');
        }

        Invoice::query()
            ->whereNotNull('deduction_invoice_id')
            ->chunk(10, function ($invoices) {
                $invoices->each(function ($invoiceDeduction) {
                    $invoiceWhereToDeduct = Invoice::find($invoiceDeduction->deduction_invoice_id);

                    InvoiceService::run($invoiceWhereToDeduct)
                        ->attachDeduction($invoiceDeduction);

                    $this->info("Added deduction to invoice {$invoiceWhereToDeduct->id}, deduction id: {$invoiceDeduction->id}");
                });
            });

        $this->info('All deductions have been moved to pivot table.');

        $this->info('Attaching manual deductions...');
        DeductionManual::query()
            ->chunk(10, function ($deductions) {
                $deductions->each(function ($deduction) {
                    $invoice = Invoice::find($deduction->invoice_id);

                    InvoiceService::run($invoice)
                        ->attachDeduction($deduction);

                    $this->info("Added manual deduction to invoice {$invoice->id}, deduction id: {$deduction->id}");
                });
            });

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

        return Command::SUCCESS;
    }
}
