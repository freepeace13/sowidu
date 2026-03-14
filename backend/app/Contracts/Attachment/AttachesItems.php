<?php

namespace App\Contracts\Attachment;

use Illuminate\Database\Eloquent\Relations\Relation;

interface AttachesItems
{
    /**
     * The relational method for items
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function items(): Relation;
}
