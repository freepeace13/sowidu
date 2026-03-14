<?php

namespace Packages\Avatarable\Traits;

use Packages\Avatarable\FileAdder;
use Packages\Avatarable\Models\Avatar;

trait HasAvatar
{
    public function avatar()
    {
        return $this->morphOne(Avatar::class, 'model')->withDefault();
    }

    public function setAvatar($file, string $diskName = ''): Avatar
    {
        return app(FileAdder::class)
            ->setSubject($this)
            ->setFile($file)
            ->save($diskName);
    }
}
