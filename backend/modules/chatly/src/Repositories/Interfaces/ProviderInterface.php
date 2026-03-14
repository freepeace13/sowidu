<?php

namespace Modules\Chatly\Repositories\Interfaces;

interface ProviderInterface
{
    public function create();

    public function get();

    public function destroy();
}
