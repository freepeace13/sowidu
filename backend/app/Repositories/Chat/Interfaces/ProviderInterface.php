<?php

namespace App\Repositories\Chat\Interfaces;

interface ProviderInterface
{
    public function create();

    public function get();

    public function destroy();
}
