<?php

namespace Packages\ActiveStatus\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateMigration extends Command
{
    protected $signature = 'active-status:migration';

    protected $description = 'Create migration for the (update) selected table, adding active status fields';

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/migration.stub';
    }

    public function handle(Filesystem $file): int
    {
        $table = $this->ask('What is the name of the table?');
        $table = strtolower(Str::snake(trim($table)));

        if (empty($table)) {
            $this->error('The name of the table is required!');

            return 1;
        }

        if (!Schema::hasTable($table)) {
            $this->error('Unable to find table "' . $table . '" on the current DB!');

            return 2;
        }

        $fileName = date('Y_m_d_His') . '_update_' . $table . '_table_add_active_status_fields.php';
        $path = database_path('migrations') . '/' . $fileName;
        $className = 'Update' . ucfirst($table) . 'TableAddActiveStatusFields';

        $stub = $file->get($this->getStub());
        $stub = str_replace('{{ class }}', $className, $stub);
        $stub = str_replace('{{ table }}', $table, $stub);

        $file->replace($path, $stub);

        $this->line("<info>Created Migration:</info> {$fileName}");

        return 0;
    }
}
