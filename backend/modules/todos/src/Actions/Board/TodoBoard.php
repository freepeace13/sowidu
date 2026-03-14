<?php

namespace Modules\Todos\Actions\Board;

use File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Todos\Models\Board;

class TodoBoard
{
    protected function validate($user, $params, $additionalRules = [])
    {
        return Validator::make($params, array_merge([
            'description' => 'nullable',
            'team_id' => ['nullable', 'exists:companies,id'],
            'settings' => 'array',
            'logo' => ['nullable', 'file', 'image'],
        ], $additionalRules))->after(
            $this->ensureTeamBelongsToUser($user),
        )->validateWithBag(Str::camel(class_basename($this)));
    }

    protected function ensureTeamBelongsToUser($user)
    {
        return function ($validator) use ($user) {
            $teamId = Arr::get($validator->getData(), 'team_id');

            $validator->errors()->addIf(
                $teamId && !$user->loadMissing(['teams'])->teams->contains(fn ($team) => $team->id == $teamId),
                'team_id',
                'The user is not belong to the team.',
            );
        };
    }

    protected function saveLogo(Board $board, UploadedFile $file)
    {
        // If updating - remove old logo
        if ($board->logo_path) {
            File::delete(public_path($board->logo_path));
        }

        $fileName = Str::random() . '_' . md5(time()) . '.' . $file->getClientOriginalExtension();
        $path = Storage::putFileAs("todo/boards/{$board->id}", $file, $fileName); // @TODO move to config

        $board->update(['logo_path' => "/storage/$path"]);

        return $board;
    }
}
