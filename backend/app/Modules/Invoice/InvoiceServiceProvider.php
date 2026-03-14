<?php

namespace App\Modules\Invoice;

use Illuminate\Support\ServiceProvider;

class InvoiceServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'invoice');

        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    }

    public function register() {}
}
