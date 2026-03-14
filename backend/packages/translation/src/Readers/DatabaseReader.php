<?php

namespace Packages\Translation\Readers;

use Illuminate\Support\Facades\Schema;

class DatabaseReader implements ReaderInterface
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function read()
    {
        $output = [];

        if (Schema::hasTable((new $this->model)->getTable())) {
            $results = $this->newQuery()->get();

            foreach ($results as $entry) {
                data_fill($output, "{$entry->group}.{$entry->key}", $entry->text);
            }
        }

        return $output;
    }

    protected function newQuery()
    {
        return $this->model::query();
    }
}
