<?php

namespace App\Providers;

use App\Actions\Invoice\SaveInvoiceAsPdf;
use App\Contracts\Actions\SavesInvoiceAsPdfs;
use App\Models;
use App\Repositories\Chat\ChatRepository;
use App\Repositories\Chat\Interfaces\ChatRepositoryInterface;
use App\Support\Facades\Impersonate;
use App\Support\TranslationFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Modules\WorkLogs\Models\WorkLog;
use Opcodes\LogViewer\Facades\LogViewer;
use Packages\RestApi\RestApi;
use Packages\Translation\Facades\Translation;
use Packages\Translation\TranslationServiceProvider;
use Sowidu\SharedData\Facades\SharedData;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ChatRepositoryInterface::class, ChatRepository::class);

        /**
         * @return \App\Models\Company
         */
        Request::macro('company', function () {
            return Impersonate::tenant();
        });

        $this->registerActions();

        // Conditionally register TelescopeServiceProvider if Telescope is installed
        if (class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        }
    }

    protected function registerActions()
    {
        /**
         * Lets normalize the action IoC bindings where every action implements a contract.
         * It can easily swap out with new implementation incase of any reason.
         */
        $this->app->bind(SavesInvoiceAsPdfs::class, SaveInvoiceAsPdf::class);
        /**
         * ...more action bindings here
         */
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        RestApi::configure($this->app);

        if (!app()->isProduction() && config('app.development.eloquent_strict_mode')) {
            Model::preventLazyLoading();
            // TODO: Many attributes are accessed even though they do not exist
            // Model::shouldBeStrict();
        }

        $this->bootSharedData();
        $this->bootTranslations();
        $this->bootRelations();

        RedirectResponse::macro('message', function ($type, $message) {
            return $this->with('flash', [
                'type' => $type,
                'message' => $message,
            ]);
        });

        LogViewer::auth(function ($request) {
            return filled($request->user());
        });
    }

    protected function bootRelations()
    {
        Relation::morphMap([
            'companies' => Models\Company::class,
            'employees' => Models\Employee::class,
            'users' => Models\User::class,
            'delivery_tickets' => Models\DeliveryTicket::class,
            'delivery_ticket_materials' => Models\DeliveryTicketMaterial::class,
            'catalog_item' => Models\CatalogItem::class,
            'tasks' => Models\Task::class,
            'address' => Models\Address::class,
            'contacts' => Models\Contact::class,
            'customers' => Models\Customer::class,
            'invitations' => Models\Invitation::class,
            'addressbooks' => Models\Addressbook::class,
            'invoices' => Models\Invoice::class,
            'notifications' => DatabaseNotification::class,
            'orders' => Models\Order::class,
            'order_product' => Models\OrderProduct::class,
            'work_logs' => WorkLog::class,
            'manual_deductions' => Models\DeductionManual::class,
            'manual_items' => \Modules\Invoicify\Models\InvoiceManualItem::class,
        ]);
    }

    protected function bootTranslations()
    {
        $this->app->registerDeferredProvider(TranslationServiceProvider::class);

        SharedData::put('translation.locales', config('translation.locales'));

        $formatter = new TranslationFormatter;
        $locals = $formatter->format($this->getTranslationFiles());
        $db = $formatter->format(
            Translation::driver('db')->all(),
        );

        $messages = array_replace_recursive($locals, $db);
        SharedData::put('translation.messages', $messages);
    }

    protected function getTranslationFiles(): array
    {
        return Arr::except(
            Translation::driver('files')->all(),
            config('translation.exclude'),
        );
    }

    /**
     * Register shared data to the client
     *
     * @return void
     */
    protected function bootSharedData()
    {
        SharedData::put([
            'app' => [
                'name' => $this->app->config['app.name'],
                'environment' => $this->app->config['app.env'],
                'isLocal' => $this->app->isLocal(),
            ],
            'locales' => $this->app->config['translation.locales'],
            'defaults' => [
                'avatars' => [
                    'unset' => Storage::disk('public')->url('unset.png'),
                    'company' => Storage::disk('public')->url('companies.png'),
                    'employee' => Storage::disk('public')->url('employees.png'),
                    'user' => Storage::disk('public')->url('users.png'),
                    'board' => Storage::disk('public')->url('board-logo.png'),
                ],
                'currency' => [
                    'symbol' => '€',
                    'code' => 'EUR',
                ],
            ],
            // You can data you needed
            // @todo must put all these values on the `config` so it can be cached
        ]);
    }
}
