<?php

namespace App\Listeners\Category;

use App\Actions\Category\SaveDefaultCategoriesToAccount;
use Illuminate\Contracts\Queue\ShouldQueue;

class SaveAccountDefaultCategories implements ShouldQueue
{
    public function handle($event)
    {
        $account = $event->company ?? $event->user;
        (new SaveDefaultCategoriesToAccount)->save($account);
    }
}
