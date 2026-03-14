<?php

namespace App\Providers;

use App\Policies\MediaPolicy;
use App\Repositories\PasswordResetsRepository;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Models\Message;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Models\Addressbook::class => \App\Policies\AddressbookPolicy::class,
        \App\Models\Employee::class => \App\Policies\EmployeePolicy::class,
        \App\Models\Company::class => \App\Policies\CompanyPolicy::class,
        \App\Models\Contact::class => \App\Policies\ContactPolicy::class,
        Conversation::class => \App\Policies\ConversationPolicy::class,
        Message::class => \App\Policies\MessagePolicy::class,
        \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
        \App\Models\Order::class => \App\Policies\OrderPolicy::class,
        \App\Models\CatalogItem::class => \App\Policies\CatalogItemPolicy::class,
        \Packages\MediaLibrary\MediaCollections\Models\Media::class => MediaPolicy::class,
        \App\Models\DeliveryTicket::class => \App\Policies\DeliveryTicketPolicy::class,
        \App\Models\Invoice::class => \App\Policies\InvoicePolicy::class,
    ];

    public function register()
    {
        //
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Gate::define('view', [MediaPolicy::class, 'view']);

        Gate::before(function ($user, $permission) {
            if (method_exists($user, 'checkPermissionTo')) {

                if ($user->checkPermissionTo($permission)) {
                    return true;
                }
            }

            // TODO: Review commented code below if app required it
            // if ($user instanceof Employee) {
            //     return $user->employer->user->is($user->user) ?: null;
            // }
        });

        $this->app->singleton(PasswordResetsRepository::class, function ($app) {
            return new PasswordResetsRepository('password_resets', 60);
        });

        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return url(route('auth.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        });
    }
}
