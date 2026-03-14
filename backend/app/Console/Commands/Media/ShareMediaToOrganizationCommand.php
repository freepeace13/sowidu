<?php

namespace App\Console\Commands\Media;

use App\Actions\Media\Share\CreateMediaSharing;
use App\Models\Employee;
use Illuminate\Console\Command;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class ShareMediaToOrganizationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:share-to-organization';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will fetch all media and will share to the organization\'s employees.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Media::query()
            ->where('model_type', 'employees')
            ->with(['model.employer'])
            ->get()
            ->each(function (Media $mediaFile) {
                $this->info("Sharing {$mediaFile->file_name} to employees...");

                $uploader = $mediaFile->model;

                if (!$uploader) {
                    return;
                }

                if (!$uploader->employer) {
                    return;
                }

                $uploader->employer
                    ->employees()
                    ->with(['user'])
                    ->get()
                    ->each(function (Employee $employee) use ($mediaFile, $uploader) {
                        if ($employee->is($uploader)) {
                            return;
                        } // Skip the uploader

                        (new CreateMediaSharing)
                            ->autoShare()
                            ->create(
                                $uploader,
                                $mediaFile->uuid,
                                [
                                    'member_id' => $employee->getKey(),
                                    'member_type' => 'Employee',
                                ],
                            );
                        $this->comment("{$mediaFile->file_name} has been shared to {$employee->user->full_name}.");
                    });
            });

        return Command::SUCCESS;
    }
}
