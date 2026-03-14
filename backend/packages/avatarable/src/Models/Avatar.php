<?php

namespace Packages\Avatarable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Packages\Avatarable\PathGenerator;

class Avatar extends Model
{
    protected $table = 'avatarables';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected string $defaultPath;

    public function getUrl()
    {
        if ($this->exists) {
            return Storage::disk($this->disk)
                ->url((new PathGenerator)->getPath($this) . $this->file_name);
        }

        return url($this->getDefaultPath());
    }

    public function getPath()
    {
        if ($this->exists) {
            return Storage::disk($this->disk)
                ->path((new PathGenerator)->getPath($this) . $this->file_name);
        }

        return public_path($this->getDefaultPath());
    }

    public function getDirectory()
    {
        return md5(get_class($this->model) . $this->model->getKey());
    }

    public function getDiskDriverName(): string
    {
        return strtolower(config("filesystems.disks.{$this->disk}.driver"));
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function setDefaultPath(string $defaultPath): self
    {
        $this->defaultPath = $defaultPath;

        return $this;
    }

    public function getDefaultPath(): string
    {
        return $this?->defaultPath ?? url('images/avatar.png');
    }
}
