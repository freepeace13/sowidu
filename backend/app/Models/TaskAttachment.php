<?php

namespace App\Models;

use App\Events\Todo\Task\TaskAttachmentUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class TaskAttachment extends Model
{
    use HasFactory;

    protected $table = 'todo_task_attachments';

    protected $fillable = [
        'task_id',
        'author_id',
        'media_file_id',
        'path',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    protected static function booted()
    {
        static::deleting(function ($taskAttachment) {
            if (!$taskAttachment->is_media) {
                Storage::deleteDirectory($taskAttachment->properties['file_path']);
            }

            TaskAttachmentUpdated::dispatch($taskAttachment);
        });

        static::created(function ($taskAttachment) {
            TaskAttachmentUpdated::dispatch($taskAttachment);
        });
    }

    public function getIsMediaAttribute()
    {
        return filled($this->media_file_id);
    }

    public function isOwner($user)
    {
        return $this->loadMissing('author.user')->author->user->is($user);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function author()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function media()
    {
        return $this->belongsTo(MediaFile::class);
    }
}
