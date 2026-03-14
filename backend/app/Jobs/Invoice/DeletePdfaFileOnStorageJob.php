<?php

namespace App\Jobs\Invoice;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeletePdfaFileOnStorageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $fileName,
        public string $token,
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if (app()->environment('local')) {
                return;
            }

            $filePath = "invoices/{$this->fileName}";

            // Delete the file if it exists
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                Log::info("PDF file deleted: {$filePath}");
            } else {
                Log::warning("PDF file not found for deletion: {$filePath}");
            }

            // Remove the token from cache
            Cache::forget("pdf_token:{$this->token}");
            Log::info("PDF token removed from cache: {$this->token}");

        } catch (\Exception $e) {
            Log::error('Error deleting PDF file: ' . $e->getMessage());
        }
    }
}
