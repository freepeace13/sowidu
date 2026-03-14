<?php

namespace App\Jobs\Company;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveRevokedRolesOnMediaSettingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Company $company, protected array $revokedRoles)
    {
        //
    }

    public function handle()
    {
        $this->company
            ->categories()
            ->get()
            ->each(function (Category $category) {
                $category->settings()
                    ->autoShare()
                    ->remove($this->revokedRoles);
            });
    }
}
