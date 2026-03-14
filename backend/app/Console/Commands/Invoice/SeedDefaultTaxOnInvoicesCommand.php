<?php

namespace App\Console\Commands\Invoice;

use App\Models\Invoice;
use Illuminate\Console\Command;

class SeedDefaultTaxOnInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:seed-default-tax-on-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed default tax on invoices.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Seeding default tax on invoices...');

        Invoice::with(['company'])->chunk(5, function ($invoices) {
            $invoices->each(function (Invoice $invoice) {
                $this->info("Seeding default tax on invoice {$invoice->uuid}...");
                $defaultTax = $invoice->company->taxes()
                    ->default()
                    ->first();

                if (!$defaultTax) {
                    $this->warn("No default tax found for company {$invoice->company->name}.");

                    return;
                }

                $invoice->taxes()
                    ->syncWithoutDetaching($defaultTax);
            });
        });

        return Command::SUCCESS;
    }
}
