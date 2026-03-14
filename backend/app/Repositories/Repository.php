<?php

namespace App\Repositories;

abstract class Repository
{
    /**
     * Result per page
     *
     * @var int
     */
    public static $perPage = 12;

    /**
     * Determine if the query should filter according to status
     *
     * @var bool
     */
    protected $online;

    /**
     * If this request is search type
     *
     * @var bool
     */
    protected $isSearch = false;

    /**
     * The key to be search
     *
     * @var string
     */
    protected $searchKey = '';

    /**
     * The query result should have status online
     *
     * @return self
     */
    public function onlyThoseOnline()
    {
        $this->online = true;

        return $this;
    }

    /**
     * Search type query on contacts
     *
     * @param  string  $key
     * @return void
     */
    public function search($key)
    {
        $this->isSearch = true;
        $this->searchKey = $key;

        return $this;
    }
}
