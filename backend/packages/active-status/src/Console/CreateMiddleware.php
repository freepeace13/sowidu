<?php

namespace Packages\ActiveStatus\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CreateMiddleware extends Command
{
    protected $signature = 'active-status:middleware';

    protected $description = 'Create a new active status middleware';

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/middleware.stub';
    }

    public function handle(Filesystem $file): int
    {
        $namespace = 'App\\Http\\Middleware';
        $className = 'HandleActiveStatusMiddleware';
        $fileName = $className . '.php';

        $path = app_path("Http/Middleware/{$fileName}");

        $stub = $file->get($this->getStub());
        $stub = str_replace('{{ namespace }}', $namespace, $stub);
        $stub = str_replace('{{ class }}', $className, $stub);

        $file->replace($path, $stub);

        $this->line("<info>Created Middleware:</info> {$fileName}");

        return 0;
    }
}
