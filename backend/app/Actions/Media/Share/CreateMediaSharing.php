<?php

namespace App\Actions\Media\Share;

use App\Models\Employee;
use App\Models\User;
use App\Notifications\Media\MediaSharedNotification;
use App\Traits\InteractsWithMedia;
use App\Traits\WithSnackbar;
use Illuminate\Support\Facades\Validator;

class CreateMediaSharing
{
    use InteractsWithMedia, WithSnackbar;

    protected $isAutoShared = false;

    public function autoShare(): self
    {
        $this->isAutoShared = true;

        return $this;
    }

    public function create(User|Employee $user, string $media, array $params)
    {
        $media = $this->resolveMedia($media);
        $validator = Validator::make($params, [
            'member_id' => ['required'],
            'member_type' => ['required', 'in:Employee,User'],
        ]);

        if ($validator->fails()) {
            return $this->redirectBackError('Something went wrong!');
        }

        $newMember = $this->resolveMember($validator->getData());

        if ($this->isAutoShared) {
            $media->autoShareTo($newMember);
        } else {
            $media->shareTo($newMember);
            $newMember->notify(new MediaSharedNotification($media));
        }
    }
}
