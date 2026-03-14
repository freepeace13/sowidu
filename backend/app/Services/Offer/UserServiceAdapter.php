<?php

declare(strict_types=1);

namespace App\Services\Offer;

use App\Models\User;
use App\Transformers\PlaceTransformer;
use Illuminate\Database\Eloquent\Model;
use Modules\Offer\Contracts\External\UserServiceContract;

use function get_user_avatar_url;

class UserServiceAdapter implements UserServiceContract
{
    public function find(int $id): ?Model
    {
        return User::find($id);
    }

    public function findOrFail(int $id): Model
    {
        return User::findOrFail($id);
    }

    public function transformForRecipient(Model $user): array
    {
        /** @var User $user */
        return [
            'name' => $user->name ?? $user->full_name,
            'email' => $user->email,
            'photo' => get_user_avatar_url($user),
            'address' => $user->currentPlace
                ? (new PlaceTransformer($user->currentPlace))->withId()->resolve()
                : null,
            'is_company' => false,
            'phone' => $user->phone ?? $user->mobile,
        ];
    }

    public function getAvatarUrl(Model $user): ?string
    {
        /** @var User $user */
        return get_user_avatar_url($user);
    }
}
