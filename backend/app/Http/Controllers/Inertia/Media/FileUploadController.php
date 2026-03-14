<?php

namespace App\Http\Controllers\Inertia\Media;

use App\Actions\Media\NormalizeImages;
use App\Events\Media\NewMediaAdded;
use App\Http\Requests\FileUploadRequest;
use App\Transformers\MediaTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class FileUploadController extends BaseController
{
    public function __invoke(FileUploadRequest $request, ?Media $file = null)
    {
        try {
            $progress = $request->receiveChunkFiles();

            if ($progress->isFinished()) {
                /** @var \Illuminate\Http\UploadedFile */
                $file = app(NormalizeImages::class)->normalize($progress->getFile());

                DB::beginTransaction();

                [$lastModified, $nativeFileName] = explode('_', $file->getClientOriginalName());

                $media = $this->user()
                    ->addMedia($file)
                    ->setFileName($nativeFileName)
                    ->setName(pathinfo($nativeFileName, PATHINFO_FILENAME))
                    ->withResponsiveImages()
                    ->withCustomProperties([
                        'source' => $request->source,
                        'lastModified' => Carbon::create($lastModified)->toDateTimeString(),
                    ])
                    ->save();

                DB::commit();

                NewMediaAdded::dispatch($media);

                // TODO: Batch job instead of dispatching two jobs each format
                // foreach ([Video::WEBM, Video::WMV] as $format) {
                //     TranscodeVideo::dispatch($media, $format)->onQueue('transcoding');
                // }

                return response()->json([
                    'filename' => $media->file_name,
                    'file' => (new MediaTransformer($media))
                        ->withPolicies($this->user())
                        ->withCreatedAt()
                        ->withOwner()
                        ->resolve(),
                ]);
            }

            return response()->json([
                'progress' => $progress->handler()->getPercentageDone(),
            ]);
        } catch (\Exception $exception) {
            DB::rollback();

            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}
