<?php

namespace App\Actions\Media\AutoShare;

use App\Models\Employee;
use App\Models\User;
use App\Traits\InteractsWithMedia;
use App\Traits\WithSnackbar;
use Illuminate\Support\Facades\Validator;

class CreateAutoShareFileToEmployee
{
    use InteractsWithMedia, WithSnackbar;

    public function create(User|Employee $user, string $media, array $params)
    {
        $media = $this->resolveMedia($media);

        $validated = Validator::make($params, [
            'member_id' => ['required'],
            'member_type' => ['required', 'in:Employee, employee'],
        ])->validate();

        $newMember = $this->resolveMember($validated);

        $media->autoShareTo($newMember);
    }
}
