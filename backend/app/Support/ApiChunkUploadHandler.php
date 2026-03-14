<?php

namespace App\Support;

use Illuminate\Http\Request;
use Pion\Laravel\ChunkUpload\Handler\ChunksInRequestUploadHandler;
use Pion\Laravel\ChunkUpload\Handler\Traits\HandleParallelUploadTrait;

class ApiChunkUploadHandler extends ChunksInRequestUploadHandler
{
    use HandleParallelUploadTrait;

    const CHUNK_NUMBER_INDEX = 'x-chunk-number';
    const CHUNK_TOTAL_INDEX = 'x-chunk-total-number';
    const CHUNK_UUID_INDEX = 'x-file-identity';

    /**
     * The file uuid for unique chunk upload session.
     *
     * @var string|null
     */
    protected $fileUuid = null;

    /**
     * AbstractReceiver constructor.
     *
     * @param  UploadedFile  $file
     * @param  AbstractConfig  $config
     */
    public function __construct(Request $request, $file, $config)
    {
        parent::__construct($request, $file, $config);

        $this->fileUuid = $request->header(self::CHUNK_UUID_INDEX);
    }

    /**
     * Append the resumable file - uuid and pass the current chunk index for parallel upload.
     *
     * @return string
     */
    public function getChunkFileName()
    {
        return $this->createChunkFileName(substr($this->fileUuid, 0, 40), $this->getCurrentChunk());
    }

    /**
     * Returns current chunk from the request.
     *
     *
     * @return int
     */
    protected function getCurrentChunkFromRequest(Request $request)
    {
        return $request->header(self::CHUNK_NUMBER_INDEX);
    }

    /**
     * Returns current chunk from the request.
     *
     *
     * @return int
     */
    protected function getTotalChunksFromRequest(Request $request)
    {
        return $request->header(self::CHUNK_TOTAL_INDEX);
    }

    /**
     * Checks if the current abstract handler can be used via HandlerFactory.
     *
     *
     * @return bool
     */
    public static function canBeUsedForRequest(Request $request)
    {
        return $request->hasHeader(self::CHUNK_NUMBER_INDEX)
            && $request->hasHeader(self::CHUNK_TOTAL_INDEX)
            && $request->hasHeader(self::CHUNK_UUID_INDEX);
    }
}
