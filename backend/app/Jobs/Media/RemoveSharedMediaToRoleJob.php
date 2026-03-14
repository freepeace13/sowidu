<?php

namespace App\Jobs\Media;

use App\Actions\Media\AutoShare\RemoveAutoSharedFilesToRole;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveSharedMediaToRoleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Company $company, protected string $role) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Remove all shared media to given role
        (new RemoveAutoSharedFilesToRole)->remove($this->company, $this->role);
    }
}
