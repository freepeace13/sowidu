<?php

namespace Packages\Contacts\Actions;

use Illuminate\Database\Eloquent\Model;
use Packages\Contacts\ContactshipRepository;

class ContactAction
{
    protected $repository;

    public function __construct(ContactshipRepository $repository)
    {
        $this->repository = $repository;
    }

    public static function make(Model $model)
    {
        return new static(new ContactshipRepository($model));
    }
}
