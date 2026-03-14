<?php

namespace App\Actions\Media\Share;

use App\Models\Employee;
use App\Models\User;
use App\Traits\InteractsWithMedia;
use App\Traits\WithSnackbar;
use Illuminate\Support\Facades\Validator;

class UpdateMediaSharing
{
    use InteractsWithMedia, WithSnackbar;

    public function update(User|Employee $user, string $media, array $params)
    {
        $media = $this->resolveMedia($media);

        $validated = Validator::make($params, [
            'member_id' => ['required'],
            'member_type' => ['required', 'in:Employee,User'],
            'member_permission' => ['required', 'in:r,rw'],
        ])->validate();

        $existingMember = $this->resolveMember($validated);

        $this->ensureMemberIsNotTheFounder($existingMember);

        $media->shareTo($existingMember, $validated['member_permission']);
    }

    public function ensureMemberIsNotTheFounder(User|Employee $existingMember)
    {
        if (!model_is($existingMember, 'Employee')) {
            return;
        }

        throw_validation_if($existingMember->employer->founder->is($existingMember), 'Cannot change permission for the organization founder.');
    }
}
