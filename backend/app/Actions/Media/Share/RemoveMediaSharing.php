<?php

namespace App\Actions\Media\Share;

use App\Models\Employee;
use App\Models\User;
use App\Support\Facades\Impersonate;
use App\Traits\InteractsWithMedia;
use App\Traits\WithSnackbar;
use Illuminate\Support\Facades\Validator;

class RemoveMediaSharing
{
    use InteractsWithMedia, WithSnackbar;

    public function remove(User|Employee $user, string $media, array $params)
    {
        $media = $this->resolveMedia($media);

        $validated = Validator::make($params, [
            'member_id' => ['required'],
            'member_type' => ['required', 'in:Employee,User'],
        ])->validate();

        $member = $this->resolveMember($validated);

        if (Impersonate::isImpersonating()) {
            $this->ensureMemberIsNotTheFounder($member);
        }

        $media->unshareFrom($member);
    }

    public function ensureMemberIsNotTheFounder(User|Employee $existingMember)
    {
        if (!model_is($existingMember, 'Employee')) {
            return;
        }

        throw_validation_if($existingMember->employer->founder->is($existingMember), 'Cannot change permission for the organization founder.');
    }
}
