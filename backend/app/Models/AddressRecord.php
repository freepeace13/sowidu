<?php

namespace App\Models;

use App\Models\QueryBuilders\Excludable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class AddressRecord extends Model
{
    use Excludable, HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        'complete_address',
        'details',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'details' => 'array',
    ];

    public function mediaTags()
    {
        return $this->morphedByMany(
            MediaFile::class,
            'taggable',
            'address_records_taggables',
        )->withTimestamps();
    }

    public function tagToMedia(MediaFile $mediaFile)
    {
        return $mediaFile->addressTags()->sync([$this->id]);
    }
}
