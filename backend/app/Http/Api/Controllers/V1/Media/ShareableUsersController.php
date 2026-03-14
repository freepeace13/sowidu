<?php

namespace App\Http\Api\Controllers\V1\Media;

use App\Http\Api\Resources\V1\Media\UserResource;
use App\Services\FileSharingService;
use Illuminate\Http\Request;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class ShareableUsersController extends MediaController
{
    public function __invoke(Request $request, Media $media)
    {
        $keyword = $request->query('q', '');
        $limit = $request->query('limit', 5);

        $user = $this->getHasMediaUser();

        $sharedUsers = app(FileSharingService::class)
            ->setMedia($media)
            ->getAllUsers();

        $existingIds = $sharedUsers
            ->reject(fn ($person) => $person::class !== $user::class)
            ->pluck('id')
            ->values()
            ->all();

        $query = $user::search($keyword)
            ->whereNotIn('id', $existingIds)
            ->limit($limit);

        if ($team = $this->currentTeam()) {
            $query->where('company_id', $team->id);
        }

        $result = $query->get();

        return UserResource::collection($result);
    }
}
