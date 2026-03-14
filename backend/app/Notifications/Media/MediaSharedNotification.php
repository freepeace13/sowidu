<?php

namespace App\Notifications\Media;

use App\Transformers\UserTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class MediaSharedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Media $mediaFile)
    {
        $mediaFile->loadMissing('model');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $type = $this->mediaFile->getTypeAttribute();
        $causer = $this->mediaFile->model;

        return [
            'message' => __('media.notifications.share.created', [
                'sharer' => $causer->full_name,
                'type' => $type,
                'name' => $this->mediaFile->name,
            ]),
            'causer' => (new UserTransformer($causer))->resolve(),
            'redirectTo' => route('media.drive.index'),
        ];
    }
}
