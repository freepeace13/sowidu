<?php

namespace App\Listeners\Category;

use App\Events\Category\CategoryDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveCategoryTagOnFiles implements ShouldQueue
{
    public function handle(CategoryDeleted $event)
    {
        //
    }
}
