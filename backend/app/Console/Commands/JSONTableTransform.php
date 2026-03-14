<?php

namespace App\Console\Commands;

class JSONTableTransform extends JSONTableCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:transform {filename} {--using=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transform the json file data through invokable class name.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->loadDependencies();

        $invokableClass = 'Database\\Transformers\\' . $this->option('using');

        if (!is_callable($transformer = new $invokableClass)) {
            return $this->error('Transformer callback should be callable class.');
        }

        $filename = $this->argument('filename');

        $data = json_decode($this->disk->get($filename), true);
        $collection = collect($data)->transform($transformer);

        $this->disk->delete($filename);
        $this->disk->put($filename, $collection->toJson());

        return 0;
    }
}
