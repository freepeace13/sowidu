<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

abstract class JSONTableCommand extends Command
{
    /**
     * @var \Illuminate\Filesystem\FilesystemAdapter
     */
    protected $disk;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    protected function loadDependencies()
    {
        $this->disk = Storage::disk('json_exports');
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    protected function validateTable(string $table)
    {
        if (!Schema::hasTable($table)) {
            throw new Exception("Database table '{$table}' not exist.");
        }

        return $table;
    }
}
