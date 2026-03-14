<?php

namespace Packages\Avatarable;

use Packages\Avatarable\Models\Avatar;

class PathGenerator
{
    public function getPath(Avatar $avatar)
    {
        return $this->getBasePath($avatar) . '/';
    }

    public function getBasePath(Avatar $avatar)
    {
        return config('avatar.directory') . DIRECTORY_SEPARATOR . $avatar->getDirectory();
    }
}
