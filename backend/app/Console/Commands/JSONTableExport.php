<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JSONTableExport extends JSONTableCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:export {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export database table data into json file.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->loadDependencies();

        $table = $this->argument('table');
        $filename = Str::finish($table, '.json');

        if ($this->disk->exists($filename)) {
            $this->disk->delete($filename);
        }

        $results = DB::table($table)->get();

        $this->disk->put($filename, $results->toJson());

        return 0;
    }
}
