<?php

namespace App\Http\Requests;

use App\Enums\MediaTypes;
use Illuminate\Foundation\Http\FormRequest;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class FileServeRequest extends FormRequest
{
    const INPUT = 'file';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // $validExtensions = null;
        // switch ($this->type) {
        //     case 'image':
        //         $validExtensions = 'jpg,png,jpeg,gif,svg';
        //         break;
        //     case 'video':
        //         $validExtensions = 'mp4,mov,ogg';
        //         break;
        //     case 'pdf':
        //         $validExtensions = 'pdf';
        //         break;
        // }

        return [
            // 'file' => 'file|mimes:' . $validExtensions,
            'file' => 'file',
            // 'extension' => 'required|in:' . $validExtensions,
            'resumableType' => 'string',
            // 'type' => [
            //     'required',
            //     'in:' . join(',', array_values(MediaTypes::getConstants()))
            // ]
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    // protected function prepareForValidation()
    // {
    //     $this->merge([
    //         'extension' => strtolower($this->file->getClientOriginalExtension()),
    //     ]);
    // }

    /**
     * @return bool|\Pion\Laravel\ChunkUpload\Save\AbstractSave
     */
    public function receiveChunkFiles()
    {
        $receiver = new FileReceiver(self::INPUT, $this, HandlerFactory::classFromRequest($this));

        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException;
        }

        return $receiver->receive();
    }
}
