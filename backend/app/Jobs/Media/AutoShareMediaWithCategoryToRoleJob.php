<?php

namespace App\Jobs\Media;

use App\Actions\Media\AutoShare\AutoShareFileWithTaggedCategory;
use App\Models\Category;
use App\Services\MediaFileService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class AutoShareMediaWithCategoryToRoleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Category $category,
        protected string $role,
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
        $company = $this->category->loadMissing(['ownerable'])->ownerable;

        MediaFileService::makeForCompany($company)
            ->with(['model'])
            ->where('category', $this->category->name)
            ->get()
            ->each(
                function (MediaFile $mediaFile) use ($company) {
                    (new AutoShareFileWithTaggedCategory)->share(
                        $employeeUploader = $mediaFile->model,
                        $mediaFile,
                        $company,
                        $mediaFile->category,
                    );
                },
            );
    }
}
