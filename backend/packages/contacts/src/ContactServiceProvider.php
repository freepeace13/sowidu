<?php

namespace Packages\Contacts;

use Illuminate\Support\ServiceProvider;

class ContactServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->publishMigrations();
    }

    protected function publishMigrations()
    {
        if (!class_exists('CreateContactshipsTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../database/migrations/create_contactships_table.php' => database_path('migrations/' . $timestamp . '_create_contactships_table.php'),
            ], 'migrations');
        }
    }
}
