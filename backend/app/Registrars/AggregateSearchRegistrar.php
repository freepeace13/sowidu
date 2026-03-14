<?php

namespace App\Registrars;

class AggregateSearchRegistrar
{
    /**
     * @var array
     */
    protected $models = [];

    /**
     * Create new registrar instance.
     *
     * @return void
     */
    public function __construct(array $models)
    {
        $this->models = $models;
    }

    /**
     * Get the model key value list
     *
     * @return array
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * Get the model class name according to the given type
     *
     * @return string
     */
    public function getModelClass(string $type)
    {
        return $this->getModels()[$type];
    }

    /**
     * Register new model key and class
     *
     * @return $this
     */
    public function register(string $type, string $modelClass)
    {
        $this->mapTypes[$type] = $modelClass;

        return $this;
    }
}
