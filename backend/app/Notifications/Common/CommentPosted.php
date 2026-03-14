<?php

namespace App\Notifications\Common;

use Illuminate\Support\Str;
use Packages\EloquentCommentable\Comment;

class CommentPosted extends AbstractNotification
{
    /**
     * @var Packages\EloquentCommentable\Comment
     */
    protected $comment;

    /**
     * Create a new notification instance.
     *
     * @return Packages\EloquentCommentable\Comment $comment
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * The notification metadata.
     *
     * @return array
     */
    protected function meta()
    {
        return [
            'resource' => [
                'identifier' => $this->comment->commentable->getKey(),
                'type' => $this->comment->commentable->getMorphClass(),
            ],
        ];
    }

    /**
     * The notification avatar.
     *
     * @return string
     */
    protected function avatar()
    {
        return $this->comment->author->avatar_url;
    }

    /**
     * The notification message pattern.
     *
     * @return string
     */
    protected function message()
    {
        return ':subject commented on :resource.';
    }

    /**
     * The notification message pattern attributes.
     *
     * @return array
     */
    protected function attributes()
    {
        return [
            'subject' => $this->comment->author->full_name,
            'resource' => Str::singular($this->comment->commentable->getMorphClass()),
        ];
    }
}
