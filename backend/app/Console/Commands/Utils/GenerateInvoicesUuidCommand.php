<?php

namespace App\Console\Commands\Utils;

use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateInvoicesUuidCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-invoices-uuid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate UUID for invoices table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Generating UUID for invoices table...');

        $this->withProgressBar(Invoice::all(['id']), function ($invoice) {
            $invoice->update([
                'uuid' => Str::orderedUuid(),
            ]);
        });

        return Command::SUCCESS;
    }
}
