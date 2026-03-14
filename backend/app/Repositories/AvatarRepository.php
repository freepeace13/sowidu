<?php

namespace App\Repositories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Keygen\Keygen;

class AvatarRepository
{
    /**
     * The directory name where the avatar stored
     *
     * @var string
     */
    public static $directory = 'media';

    /**
     * Get the avatar of the given model
     *
     * @return mixed
     */
    public function findModelAvatar(Model $model)
    {
        return $model->avatars()->exists()
            ? $model->avatars()->first()->url
            : $model->getDefaultAvatar();
    }

    /**
     * Helper function that set contactable avatar from uploaded file
     *
     * @param  Illuminate\Database\Eloquent\Model  $model
     * @param  Illuminate\Http\UploadedFile  $file
     * @return App\Models\Media
     */
    public function makeFromFile(Model $model, UploadedFile $file)
    {
        [$type, $filename, $size, $mimetype] = $this->extractFileDetails($file);

        $model->avatars()->detach();

        $media = $model->avatars()->create([
            'ownerable_type' => $model->getMorphClass(),
            'ownerable_id' => $model->id,
            'filename' => $filename,
            'mimetype' => $mimetype,
            'type' => $type,
            'size' => $size,
        ]);

        $file->storeAs(static::$directory, $filename, 'public');

        // Replace the existing avatar media and return the
        // newly set media instance of avatar
        return $this->makeFromMedia($model, $media);
    }

    /**
     * Get the information needed to store from the given file
     *
     * @return array
     */
    private function extractFileDetails(UploadedFile $file)
    {
        return [
            explode('/', $file->getMimeType())[0],
            Keygen::alphanum(10)->generate(),
            $file->getSize(),
            $file->getMimeType(),
        ];
    }

    /**
     * Helper function that set contactable avatar from media instance
     *
     * @param  Illuminate\Database\Eloquent\Model  $model
     * @param  App\Models\Media  $media
     * @return App\Models\Media
     */
    public function makeFromMedia(Model $model, Media $media)
    {
        $model->avatars()->detach();
        $model->avatars()->attach($media->id);

        return $media;
    }
}
