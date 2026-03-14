<?php

namespace App\Console\Commands\Invoice;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanOldInvoiceArchivesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:delete-puppeteer-screenshots
                            {--dry-run : Simulate the deletion of files} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Puppeteer screenshots for invoices';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $totalFiles = 0;
        $deletedCount = 0;
        $chunkSize = 10; // Number of files to process at once to avoid memory exhaustion

        $directories = [
            'zips/invoices',
        ];

        foreach ($directories as $directory) {
            $this->warn("Processing directory: {$directory}");

            // Get total count for this directory
            $directoryFiles = Storage::files($directory);

            $totalFiles += count($directoryFiles);

            // Process files in chunks
            $fileChunks = array_chunk($directoryFiles, $chunkSize);
            unset($directoryFiles); // Free memory

            foreach ($fileChunks as $fileChunk) {
                $deletedCount += $this->processFileChunk($fileChunk, $dryRun);
                unset($fileChunk); // Free memory
                gc_collect_cycles(); // Force garbage collection
            }

            $this->newLine();
        }

        if ($dryRun) {
            $this->info("Found {$deletedCount} files that would be deleted out of {$totalFiles} total files.");
        } else {
            $this->info("Successfully deleted {$deletedCount} files.");
        }

        return Command::SUCCESS;
    }

    /**
     * Process a chunk of files
     *
     * @return int Number of deleted files
     */
    protected function processFileChunk(array $files, bool $dryRun): int
    {
        $deletedCount = 0;
        $this->output->progressStart(count($files));

        foreach ($files as $file) {
            $fileTimestamp = Storage::lastModified($file);

            // Delete files older than 5 days
            if ($fileTimestamp >= now()->subDays(5)
                ->timestamp) {
                $this->output->progressAdvance();

                continue;
            }

            $this->line(
                basename($file) . ' is subject for deletion. Created on: ' . Carbon::parse($fileTimestamp)->diffForHumans(),
            );

            if (!$dryRun) {
                Storage::delete($file);
            }

            $deletedCount++;
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        return $deletedCount;
    }
}
