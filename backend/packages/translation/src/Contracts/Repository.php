<?php

namespace Packages\Translation\Contracts;

interface Repository
{
    public function all();

    public function lines(string $group);

    public function line(string $group, string $key);

    public function lineOf(string $group, string $key, string $locale);

    public function exists(string $group, string $key);
}
