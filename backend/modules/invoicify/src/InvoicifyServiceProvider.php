<?php

namespace Modules\Invoicify;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Invoicify\Actions\AddInvoiceManualItemAction;
use Modules\Invoicify\Actions\BulkExportInvoicePdfAction;
use Modules\Invoicify\Actions\CompressInvoicePdfsAction;
use Modules\Invoicify\Actions\GenerateInvoicePdfAction;
use Modules\Invoicify\Actions\MarkAsPaidAction;
use Modules\Invoicify\Actions\Preview\UpdateInvoiceAction;
use Modules\Invoicify\Actions\SendToClientAction;
use Modules\Invoicify\Actions\UpdateInvoiceManualItemAction;
use Modules\Invoicify\Contracts\Actions\AddsInvoiceManualItems;
use Modules\Invoicify\Contracts\Actions\BulkExportsInvoicePdfs;
use Modules\Invoicify\Contracts\Actions\CompressesInvoicePdfs;
use Modules\Invoicify\Contracts\Actions\GeneratesInvoicePdf;
use Modules\Invoicify\Contracts\Actions\MarksInvoicesAsPaid;
use Modules\Invoicify\Contracts\Actions\SendsInvoiceToClient;
use Modules\Invoicify\Contracts\Actions\UpdatesInvoiceManualItems;
use Modules\Invoicify\Contracts\Actions\UpdatesInvoices;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Models\InvoiceManualItem;
use Modules\Invoicify\Policies\InvoicePolicy;
use Modules\Invoicify\Support\Pdf\DefaultPathGenerator;
use Modules\Invoicify\Support\Pdf\MpdfFactory;
use Modules\Invoicify\Support\Pdf\PathGenerator;

class InvoicifyServiceProvider extends ServiceProvider
{
    protected $policies = [
        Invoice::class => InvoicePolicy::class,
    ];

    public $bindings = [
        PathGenerator::class => DefaultPathGenerator::class,
        GeneratesInvoicePdf::class => GenerateInvoicePdfAction::class,
        BulkExportsInvoicePdfs::class => BulkExportInvoicePdfAction::class,
        CompressesInvoicePdfs::class => CompressInvoicePdfsAction::class,
        MarksInvoicesAsPaid::class => MarkAsPaidAction::class,
        SendsInvoiceToClient::class => SendToClientAction::class,
        AddsInvoiceManualItems::class => AddInvoiceManualItemAction::class,
        UpdatesInvoiceManualItems::class => UpdateInvoiceManualItemAction::class,
        UpdatesInvoices::class => UpdateInvoiceAction::class,
    ];

    public $singletons = [
        //
    ];

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/invoicify.php', 'invoicify',
        );

        $this->app->bind('invoicify.mpdf', function ($app) {
            return new MpdfFactory;
        });
    }

    public function boot()
    {
        Relation::morphMap([
            'manual_items' => InvoiceManualItem::class,
        ]);

        $this->registerPolicies();
        $this->registerRoutes();
        $this->registerViews();

        if ($this->app->runningInConsole()) {
            // $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');\

            $this->publishes([
                __DIR__ . '/../config/invoicify.php' => config_path('invoicify.php'),
            ], 'config');
        }
    }

    protected function registerRoutes()
    {
        Route::group([
            'domain' => config('invoicify.domain'),
            'prefix' => config('invoicify.prefix'),
            'middleware' => config('invoicify.middleware'),
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    protected function registerViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'invoicify');

        Blade::anonymousComponentPath(__DIR__ . '/../resources/views/components', 'invoicify');
    }

    protected function registerPolicies()
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
