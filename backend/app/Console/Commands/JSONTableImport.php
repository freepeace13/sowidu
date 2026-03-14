<?php

namespace App\Console\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JSONTableImport extends JSONTableCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:import {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import json file data to table.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->loadDependencies();

        $filename = $this->argument('filename');

        $table = $this->validateTable(
            Str::replaceLast('.json', '', $filename),
        );

        return DB::transaction(function () use ($filename, $table) {
            $data = json_decode($this->disk->get($filename), true);

            $isTruncatable = !collect($data)->some(function ($value) {
                return Arr::has($value, 'id');
            });

            if ($isTruncatable) {
                DB::table($table)->truncate();
                DB::table($table)->insert($data);

                return;
            }

            foreach ($data as $entry) {
                DB::table($table)
                    ->where('id', $entry['id'])
                    ->update(Arr::except($entry, 'id'));
            }

            return 0;
        });
    }
}
