<?php

namespace Modules\Todos\Actions\Board\Task\Attachment;

use App\Services\AttachmentService;
use App\Services\MediaFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Todos\Models\Task;
use Modules\Todos\Models\TaskAttachment;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class CreateTaskAttachment
{
    public function create($user, Task $task, array $params)
    {
        Gate::forUser($user)->authorize('addAttachment', $task);

        $validated = $this->validate($params);

        $author = $task->board->getSubscription($user);

        $path = null;
        $attachment = null;

        if (Arr::get($params, 'file')) {
            $attachment = $this->attachFile($task);
            if ($attachment instanceof \Illuminate\Http\JsonResponse) {
                return $attachment;
            }
            $path = $attachment['relative_path'];
        }

        if ($mediaId = Arr::get($validated, 'media_id', null)) {
            $attachment = $this->attachMedia($mediaId);
            $path = $attachment['url'];
        }

        $taskAttachment = tap(
            new TaskAttachment([
                'path' => $path,
                'properties' => $attachment,
            ]),
            fn ($taskAttachment) => $taskAttachment->author()->associate($author),
        );

        $task->attachments()->save($taskAttachment);

        return $taskAttachment;
    }

    protected function validate(array $params)
    {
        $supportedMimetypes = array_reduce(MediaFileService::allowedMimetypes(), function ($a, $b) {
            return array_merge($a, (array) $b);
        }, []);

        return Validator::make($params, [
            'media_id' => [
                'required_without:resumableType',
                'nullable',
                'exists:media_files,id',
            ],
            'resumableType' => [
                'required_without:media_id',
                'nullable',
                Rule::in($supportedMimetypes),
            ],
        ], [
            'media_id.required_without' => 'Your file is missing, please upload an image or choose from your media files.',
            'resumableType.in' => 'File type given is not yet supported.',
            'resumableType.required_without' => 'Your file is missing, please upload an image or choose from your media files.',
        ])->validateWithBag('createTaskAttachment');
    }

    protected function attachFile(Task $task): JsonResponse|array
    {
        $receiver = new FileReceiver('file', request(), HandlerFactory::classFromRequest(request()));

        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException;
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            return (new AttachmentService)
                ->saveTo("todo/boards/{$task->board_id}/{$task->id}/")
                ->build($save->getFile());
        }

        /** @var AbstractHandler $handler */
        $handler = $save->handler();

        return response()->json([
            'progress' => $handler->getPercentageDone(),
        ]);
    }

    protected function attachMedia(int $mediaId)
    {
        return (new AttachmentService)->build($mediaId);
    }
}
