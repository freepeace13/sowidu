<?php

namespace App\Console\Commands\Invoice;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Modules\Invoice\InvoiceService;
use App\Modules\Invoice\Services\InvoicePaymentService;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class UpdateInvoicesStatusCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update invoices status.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->warn('This action cannot be undone.');

        if (!$this->confirm('Are you sure you want to invoice status? This will update the status of all invoices based on their payments.')) {
            $this->info('Command cancelled.');

            return Command::SUCCESS;
        }

        $this->info('Update invoices status...');

        Invoice::query()->chunk(10, function ($invoices) {
            $invoices->each(function (Invoice $invoice) {
                $invoiceService = InvoicePaymentService::run($invoice);

                $invoiceService = InvoiceService::run($invoice);

                if ($invoice->status == InvoiceStatus::PAID) {
                    // Check if no payments
                    return;
                }

                $invoiceService->updateInvoiceStatus();

                // // Check if invoice has payments
                // if ($invoiceService->isOverPaid()) {
                //     $invoice->status = InvoiceStatus::OVERPAID;
                // } elseif ($invoiceService->isFullyPaid()) {
                //     $invoice->status = InvoiceStatus::PAID;
                // } elseif ($invoiceService->hasPayments()) {
                //     $invoice->status = InvoiceStatus::PARTIALLY_PAID;
                // } else {
                //     $invoice->status = InvoiceStatus::SENT;
                // }
                // $invoice->save();
                $this->info("Invoice status updated for invoice: {$invoice->uuid}");
            });
        });

        $this->info('All invoices status has been updated!');

        return Command::SUCCESS;
    }
}
