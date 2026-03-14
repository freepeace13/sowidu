<?php

namespace App\Console\Commands\Order;

use App\Models\Order;
use Illuminate\Console\Command;

class UpdateOrderNumberWhenDeleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:update-number';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hot fix for deleted order. Update order number.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Order::query()
            ->onlyTrashed()
            ->get()
            ->each(function (Order $order) {
                if (!$order->trashed()) {
                    return;
                }

                $this->info('Deleted order found, updating order number...');

                $order->update([
                    'order_number' => 'DEL-' . now()->getTimestamp(),
                ]);

                sleep(1);
            });

        return 0;
    }
}
