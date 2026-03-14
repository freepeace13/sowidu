<?php

namespace App\Jobs\Media;

use App\Actions\Media\AutoShare\OrganizationFileAutoShareToRoles;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class AutoShareMediaBasedOnOrganizationSettingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected MediaFile $mediaFile,
        protected Company $company,
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
        $uploader = $this->mediaFile->model;

        (new OrganizationFileAutoShareToRoles)->execute($uploader, $this->company, $this->mediaFile);
    }
}
