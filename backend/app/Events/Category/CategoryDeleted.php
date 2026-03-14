<?php

namespace App\Events\Category;

use App\Models\Category;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategoryDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Category $category, public User $author)
    {
        //
    }
}
