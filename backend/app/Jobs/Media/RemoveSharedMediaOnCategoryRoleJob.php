<?php

namespace App\Jobs\Media;

use App\Actions\Media\AutoShare\RemoveSharedMediaOnCategoryRole;
use App\Models\Category;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveSharedMediaOnCategoryRoleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Company $company, protected Category $category, protected string $role)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new RemoveSharedMediaOnCategoryRole)->remove($this->company, $this->category, $this->role);
    }
}
