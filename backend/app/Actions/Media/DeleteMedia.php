<?php

namespace App\Actions\Media;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class DeleteMedia
{
    use AuthorizesRequests;

    public function delete(User|Employee $account, Media $mediaFile)
    {
        $this->authorizeForUser($account, 'remove', $mediaFile);

        if ($mediaFile->wasSharedTo($account)) {
            $mediaFile->unshareFrom($account);
        } else {
            $mediaFile->delete();
        }
    }
}
