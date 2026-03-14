<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maestroerror\HeicToJpg;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class FileUploadRequest extends FormRequest
{
    const INPUT = 'file';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if ($this->isChunkedUpload()) {
            return [
                'file' => 'required|file',
            ];
        }

        return [
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,tiff,tif,pdf,doc,docx,xls,xlsx,csv,zip,mp4,mov,avi,wmv,mkv|max:51200',
        ];
    }

    public function receiveChunkFiles()
    {
        $receiver = new FileReceiver(self::INPUT, $this, HandlerFactory::classFromRequest($this));

        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException;
        }

        $progress = $receiver->receive();

        if ($progress->isFinished()) {
            $this->validateCompletedFile($progress->getFile(), $progress);

            $file = $progress->getFile();

            if (HeicToJpg::isHeic($file->getRealPath())) {
                $converted = HeicToJpg::convert($file->getRealPath())->get();
                $this->merge(['file' => $converted]);
            }
        }

        return $progress;
    }

    protected function validateCompletedFile($file, $progress)
    {
        $validator = Validator::make(
            ['file' => $file],
            ['file' => 'mimes:jpg,jpeg,png,webp,tiff,tif,pdf,doc,docx,xls,xlsx,csv,zip,mp4,mov,avi,wmv,mkv|max:51200'],
        );

        if ($validator->fails()) {
            $progress->handler()->cleanup();
            throw new ValidationException($validator);
        }
    }

    protected function isChunkedUpload(): bool
    {
        return $this->header('content-range') !== null ||
               $this->header('x-content-range') !== null ||
               $this->input('resumableChunkNumber') !== null;
    }
}
