<?php

namespace Packages\Translation;

use Illuminate\Support\Manager;
use Packages\Translation\Contracts\Repository;

class TranslationManager extends Manager
{
    public function getDefaultDriver()
    {
        return 'files';
    }

    public function createFilesDriver(): Repository
    {
        $reader = $this->container->make('sowidu.translation.files');

        return new TranslationRepository($reader);
    }

    public function createDbDriver(): Repository
    {
        $reader = $this->container->make('sowidu.translation.db');

        return new TranslationRepository($reader);
    }

    public function locales()
    {
        return $this->getConfig('locales');
    }

    /**
     * Get the cache connection configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->container['config']["translation.{$name}"];
    }
}
