<?php

namespace App\Console\Commands\Invoice\Utils;

use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class ResetInvoicePreviewLayoutsCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:reset-preview-layouts
                        {--force}
                        {--draft-only : Only delete draft invoices}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all invoice preview layouts to null';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return Command::INVALID;
        }

        $this->info('Resetting invoice preview layouts...');

        if ($this->option('draft-only')) {
            $this->info('Only draft invoices will be processed.');
            $invoices = Invoice::draft()->get(['id', 'preview_layout']);
            $this->processInvoices($invoices);

            $this->info('Draft invoice preview layouts have been reset.');

            return Command::SUCCESS;
        }

        $invoices = Invoice::all(['id', 'preview_layout']);

        if ($invoices->isEmpty()) {
            $this->info('No invoices found to reset.');

            return Command::SUCCESS;
        }

        $this->processInvoices($invoices);

        $this->newLine();

        $this->info('All invoice preview layouts have been reset.');

        return Command::SUCCESS;
    }

    protected function processInvoices($invoices)
    {
        $bar = $this->output->createProgressBar($invoices->count());
        $bar->start();

        $invoices->each(function ($invoice) use ($bar) {
            if ($invoice->preview_layout) {
                $invoice->update(['preview_layout' => null]);
                $this->info("Reset preview layout for invoice ID: {$invoice->id}");
            }

            $bar->advance();
        });

        $bar->finish();
    }
}
