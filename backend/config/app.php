<?php

use Illuminate\Support\Facades\Facade;

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'id' => env('APP_ID'),

    'name' => env('APP_NAME', 'Laravel'),

    'logo' => env('APP_LOGO', 'app_logo.png'),

    'asset_url' => env('ASSET_URL'),

    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'CET'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => env('APP_DEFAULT_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [
        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /**
         * Third Party Package Service Providers
         */
        EloquentFilter\ServiceProvider::class,
        Bugsnag\BugsnagLaravel\BugsnagServiceProvider::class,
        STS\ZipStream\ZipStreamServiceProvider::class,

        /*
         * Internal package Service Providers
         */
        Packages\MediaLibrary\MediaLibraryServiceProvider::class,

        /*
         * Application Service Providers...
         */
        Bugsnag\BugsnagLaravel\BugsnagServiceProvider::class,
        App\Providers\AppServiceProvider::class,
        // App\Providers\DatabaseServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\WebSocketServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\HorizonServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\HelperServiceProvider::class,
        App\Providers\SearchServiceProvider::class,
        App\Providers\AdminServiceProvider::class,
        App\Providers\UrnServiceProvider::class,
        App\Providers\AddressbookServiceProvider::class,
        App\Providers\ApiServiceProvider::class,
        App\Providers\TelescopeServiceProvider::class,

        // Modules service provider
        App\Modules\Invoice\InvoiceServiceProvider::class,
        App\Providers\ChatServiceProvider::class,
        App\Providers\InvoicifyServiceProvider::class,
        App\Providers\CatalogServiceProvider::class,
        App\Providers\WorkLogsServiceProvider::class,
        Modules\Offer\OfferServiceProvider::class,
        Modules\Offer\OfferEventServiceProvider::class,
        App\Providers\OfferAdapterServiceProvider::class,
        App\Providers\DeliveryTicketServiceProvider::class,
        App\Providers\CatalogServiceProvider::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */
    'aliases' => Facade::defaultAliases()->merge([
        /**
         * Third Party Package Aliases
         */
        'Account' => App\Support\Facades\Factories\Account::class,
        'Attachment' => App\Support\Facades\Repositories\Attachment::class,
        'Company' => App\Support\Facades\Repositories\Company::class,
        'Impersonate' => App\Support\Facades\Impersonate::class,
        'PDF' => Barryvdh\DomPDF\Facade\Pdf::class,
    ])
        ->toArray(),

    'default' => [
        'permissions' => [
            'manage permissions',
            'add member',
            'change avatar',
            'update settings',
            'can access media',
            'can access chat',
            'can access todo',
            'can access address book',
            'can manage address book',
            'can manage organization settings',
            'can manage organization categories',
            'can access order',
            'can create order',
            'can confirm order',
            'can cancel order',
            'can access work logs',
            'can share media',
            'can access catalog',
            'can create catalog items',
            'can view selling price',
            'can view purchasing price',
            'can delete catalog items',
        ],

        'employee_permissions_only' => [
            'access_employees',
            'can access work logs',
        ],

        'categories' => explode(
            ',',
            env('APP_DEFAULT_CATEGORIES', 'invoice,offer,order,delivery'),
        ),

        'organization' => [
            'settings' => [
                'media' => [
                    'auto_share_to_roles' => [
                        \App\Models\Company::FOUNDER_ROLE_NAME,
                    ],
                ],
                'employee_rate' => [
                    'currency' => 'EUR',
                    'rate' => 1,
                ],
            ],
        ],

        'currencies' => [
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
        ],

        'timezone' => env('APP_DEFAULT_TIMEZONE', 'CET'),

        'currency' => 'EUR',
    ],

    'company' => [
        'commercial_registers' => [
            'HRB',
            'HRA',
        ],
    ],

    'reminders' => [
        'enabled' => env('REMINDERS_ENABLED', false),
    ],

    'super_admins' => explode(
        ',',
        env(
            'SUPER_ADMIN_EMAILS',
            'sg@gs-goebel-haustechnik.de,rj.fabiania.wamal@gmail.com,freepeace13@gmail.com,jdltechworks@gmail.com',
        ),
    ),

    'invoice' => [

        /**
         * Configuration setting to determine whether to send invoice emails to clients.
         *
         * @var bool
         */
        'send_mail_to_client' => env('INVOICE_SEND_MAIL_TO_CLIENT', false),
    ],

    'development' => [
        'eloquent_strict_mode' => env('DEVELOPMENT_ELOQUENT_STRICT_MODE', false),
    ],

    'executable_path' => [
        'node' => env('NODE_EXECUTABLE_PATH', '/usr/bin/node'),
        'npm' => env('NPM_EXECUTABLE_PATH', '/usr/bin/npm'),
        'chrome' => env('CHROME_EXECUTABLE_PATH', '/usr/bin/google-chrome'),
    ],
];
