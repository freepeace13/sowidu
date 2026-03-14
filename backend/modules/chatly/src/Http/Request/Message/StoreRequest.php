<?php

namespace Modules\Chatly\Http\Request\Message;

use App\Services\MediaFileService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $supportedMimetypes = array_reduce(MediaFileService::allowedMimetypes(), function ($a, $b) {
            return array_merge($a, (array) $b);
        }, []);

        return [
            'message' => [
                'required_without_all:file,media_id',
                'array',
            ],
            'message.body' => [
                'required_without_all:file,media_id',
            ],
            'media_id' => [
                'nullable',
                'exists:media_files,id',
            ],
            'file' => [
                'nullable',
                'file',
            ],
            'body' => [
                'nullable',
                'string',
            ],
            'resumableType' => [
                'nullable',
                Rule::in($supportedMimetypes),
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'message.required_without' => 'Message or an attachment is required.',
            'message.body.required_without' => 'Message or an attachment is required.',
            'resumableType.in' => 'File type given is not yet supported.',
        ];
    }

    /**
     * Receive chunk files
     *
     * @return \Pion\Laravel\ChunkUpload\Save\AbstractSave
     */
    public function receiveChunkFiles()
    {
        $receiver = new FileReceiver(
            'file',
            $this,
            HandlerFactory::classFromRequest($this),
        );

        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException;
        }

        return $receiver->receive();
    }
}
