<?php

namespace Packages\Translation;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Translation\TranslationServiceProvider as ServiceProvider;
use Packages\Translation\Readers\DatabaseReader;
use Packages\Translation\Readers\FileReader;

class TranslationServiceProvider extends ServiceProvider
{
    public function register()
    {
        parent::register();

        $this->configureManager();
        $this->configureReaders();

        $this->mergeConfigFrom(__DIR__ . '/../config/translation.php', 'translation');
    }

    public function boot()
    {
        $this->publishConfigs();
        $this->publishMigrations();
    }

    protected function configureManager()
    {
        $this->app->bind('sowidu.translation', function ($app) {
            return new TranslationManager($app);
        });
    }

    protected function configureReaders()
    {
        try {
            $this->app['sowidu.translation.files'];
        } catch (BindingResolutionException $exception) {
            $this->app->bind('sowidu.translation.files', function ($app) {
                return new FileReader($app['files'], $app['path.lang']);
            });
        }

        $this->app->bind('sowidu.translation.db', function () {
            return new DatabaseReader(config('translation.model'));
        });
    }

    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new LoaderManager($app['files'], $app['path.lang']);
        });
    }

    protected function publishConfigs()
    {
        $this->publishes([
            __DIR__ . '/../config/translation.php' => config_path('translation.php'),
        ], 'config');
    }

    protected function publishMigrations()
    {
        if (!class_exists('CreateLanguageLinesTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../database/migrations/create_language_lines_table.php' => database_path('migrations/' . $timestamp . '_create_language_lines_table.php'),
            ], 'migrations');
        }
    }
}
