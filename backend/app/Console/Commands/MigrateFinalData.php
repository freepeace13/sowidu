<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class MigrateFinalData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:transfer_final_data';

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

        $invoices = Invoice::whereNotNull('final_data')->get();

        $invoices->map(function ($invoice) {
            dump($invoice->final_data->get('invoice')['company']);
        });

        /*$this->withProgressBar($invoices, function ($invoice) {
            if ($items = $invoice->final_data->get('items')) {
                foreach($items as $item) {
                    $invoice->items()->where('id', $item['id'])->update([
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }
            }
        })*/
        return Command::SUCCESS;
    }
}
