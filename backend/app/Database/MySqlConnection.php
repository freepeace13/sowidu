<?php

namespace App\Database;

use Illuminate\Database\MySqlConnection as BaseConnection;

class MySqlConnection extends BaseConnection
{
    /**
     * {@inheritdoc}
     */
    public function query()
    {
        return new QueryBuilder(
            $this,
            $this->getQueryGrammar(),
            $this->getPostProcessor(),
        );
    }
}
