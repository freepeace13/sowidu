<?php

namespace App\Providers;

use App\Events;
use App\Listeners;
use App\Observers\MediaFileCategoryObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Packages\Contacts\Events\ContactAccepted;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Events\CompanyInvitationCreated::class => [
            Listeners\NotifyInvitationToUser::class,
        ],

        Events\CompanyInvitationAccepted::class => [
            Listeners\CreateUsersEmployeeAccount::class,
            Listeners\NotifyNewTeamMember::class,
        ],

        Events\NewCompanyRegistered::class => [
            Listeners\RegisterProfile::class,
            Listeners\CreateCompanyFounder::class,
            Listeners\Category\SaveAccountDefaultCategories::class,
            Listeners\Organization\SaveCompanyDefaultSettings::class,
        ],

        Events\Auth\AccountActivated::class => [
            Listeners\BroadcastAccountActivated::class,
        ],

        Events\Employment\EmployeeCreated::class => [
            Listeners\RegisterProfile::class,
            Listeners\CreateEmploymentContacts::class,
        ],

        ContactAccepted::class => [
            Listeners\ProcessAcceptedContact::class,
        ],

        Registered::class => [
            Listeners\RegisterProfile::class,
            Listeners\Category\SaveAccountDefaultCategories::class,
            SendEmailVerificationNotification::class,
        ],

        \Musonza\Chat\Eventing\AllParticipantsClearedConversation::class => [
            \App\Listeners\Chat\DeleteConversation::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        MediaFile::observe(MediaFileCategoryObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }
}
