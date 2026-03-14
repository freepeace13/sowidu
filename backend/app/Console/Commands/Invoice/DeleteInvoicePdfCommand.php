<?php

namespace App\Console\Commands\Invoice;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteInvoicePdfCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:delete-pdf
                            {--draft-only : Only delete draft invoices}
                            {--dry-run : Simulate the deletion of files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete the PDF of an invoice. This is for testing purposes only!';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $deleteDraftOnly = $this->option('draft-only');

        $this->warn('This command is for testing purposes only!');

        if (!$this->confirm('Are you want to delete the PDF files generated? This action cannot be undone!')) {
            return Command::INVALID;
        }

        $directory = 'invoices';
        $totalFiles = 0;
        $deleteFiles = 0;

        // Get total count for this directory
        $directoryFiles = Storage::files($directory);

        $totalFiles += count($directoryFiles);

        // Process files in chunks
        $chunkSize = 10;
        $fileChunks = array_chunk($directoryFiles, $chunkSize);
        unset($directoryFiles); // Free memory

        foreach ($fileChunks as $fileChunk) {
            $deleteFiles += $this->processFileChunk(
                $fileChunk,
                $dryRun,
                $deleteDraftOnly,
            );
            unset($fileChunk); // Free memory
            gc_collect_cycles(); // Force garbage collection
        }

        $this->newLine();

        if ($dryRun) {
            $this->info("Found {$deleteFiles} files that would be deleted out of {$totalFiles} total files.");
        } else {
            $this->info("Successfully deleted {$totalFiles} files.");
        }

        return Command::SUCCESS;
    }

    protected function processFileChunk(array $files, bool $dryRun, bool $deleteDraftOnly): int
    {
        $deletedCount = 0;
        $this->output->progressStart(count($files));

        foreach ($files as $file) {
            $fileName = basename($file);

            if ($deleteDraftOnly && str($fileName)->contains('TMP') === false) {
                $this->output->progressAdvance();

                continue; // Skip non-draft files
            }

            $this->line(
                $fileName . ' is subject for deletion.',
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
