<?php

namespace App\Services;

use App\Traits\AttachmentHelper;
use App\Transformers\MediaTransformer;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use IlluminateAgnostic\Str\Support\Str;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\PdfToImage\Pdf;

class AttachmentService
{
    use AttachmentHelper;

    protected $isFile;

    protected $fileName;

    protected string $path;

    public function saveTo(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return array{
     *     name: string,
     *     mime_type: string,
     *     url: string,
     *     thumbnail_url: string,
     *     original_name: string,
     *     type: string,
     *     relative_path: string,
     *     file_path: string,
     * }
     */
    public function build(UploadedFile|int $payload): array
    {
        $attachmentData = [];
        if ($payload instanceof UploadedFile) {
            throw_unless($this->path, 'Please add path where the file will be saved.');
            $attachmentData = $this->fromFile($payload);
        } else {
            $attachmentData = $this->fromMedia($payload);
        }

        return $attachmentData;
    }

    public function fromFile(UploadedFile $file): array
    {
        $extension = $file->getClientOriginalExtension();
        $originalName = $file->getClientOriginalName();
        $fileName = Str::random() . '_' . md5(time()) . '.' . $extension;
        $mime = str_replace('/', '-', $file->getMimeType());
        $fileFolder = Str::random(10);
        $type = $this->attachmentType($mime);

        // Build the file path
        $filePath = $this->path . "$fileFolder/";
        $fileExactPath = $filePath . $fileName;

        $storage = Storage::disk('public');
        $storage->putFileAs($filePath, $file, $fileName);
        $url = $storage->url($fileExactPath);

        $relativePath = storage_path("app/public/$fileExactPath");
        $thumbnailName = 'thumbnail.png';

        if ($type == 'pdf') {
            // File is PDF
            $this->pdfGenerateThumbnail($relativePath);
        } elseif ($type == 'video') {
            $this->videoGenerateThumbnail($relativePath);
        } else {
            if ($mime == 'image-svg+xml') {
                $thumbnailName = 'thumbnail.svg';
                $storage->copy($fileExactPath, $filePath . $thumbnailName);
            } else {
                // Manipulate Image - optimize/thumbnail
                $this->imageManipulation($relativePath, $mime);
            }
        }

        $thumbnailUrl = Storage::url($filePath . $thumbnailName);

        return [
            'name' => $fileName,
            'mime_type' => $mime,
            'url' => $url,
            'thumbnail_url' => $thumbnailUrl,
            'original_name' => $originalName,
            'type' => $type,
            'is_media' => false,
            'relative_path' => $fileExactPath,
            'file_path' => $filePath,
        ];
    }

    public function fromMedia(int $mediaId): array
    {
        $media = (new MediaTransformer(MediaFile::find($mediaId)))->resolve();
        $mime = $media['mime_type'];
        $type = $this->attachmentType($mime);

        $thumbnailUrl = $media['url'];
        if (array_key_exists('thumbnail', $media['conversions'])) {
            $thumbnailUrl = $media['conversions']['thumbnail'];
        }

        return [
            'name' => $media['file_name'],
            'mime_type' => $mime,
            'url' => $media['url'],
            'thumbnail_url' => $thumbnailUrl,
            'original_name' => $media['file_name'],
            'type' => $type,
            'is_media' => true,
            'media_id' => $media['id'],
            'relative_path' => $media['file_name'],
            'file_path' => null,
        ];
    }

    protected function pdfGenerateThumbnail(string $path)
    {
        $fileDir = Str::of($path)->beforeLast('/');

        $pdf = new Pdf($path);
        $pdf->setOutputFormat('png')
            ->setCompressionQuality(20)
            ->saveImage("{$fileDir}/thumbnail.png");
    }

    protected function videoGenerateThumbnail(string $filePath)
    {
        $fileDir = Str::of($filePath)->beforeLast('/');
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('media.ffmpeg_path') ?? config('media-library.ffmpeg_path'),
            'ffprobe.binaries' => config('media.ffprobe_path') ?? config('media-library.ffprobe_path'),
        ]);

        $video = $ffmpeg->open($filePath);
        $duration = $ffmpeg->getFFProbe()->format($filePath)->get('duration');
        $frame = $video->frame(TimeCode::fromSeconds(rand(0, $duration)));
        $frame->save("{$fileDir}/thumbnail.png");
    }

    protected function imageManipulation(string $filePath)
    {
        $fileDir = Str::of($filePath)->beforeLast('/');
        $image = Image::load($filePath);

        // Optimize the image first
        $image->optimize([
            Jpegoptim::class => [
                '--all-progressive',
                '-m25',
                // set maximum quality to 85%
                '--strip-all',
                // this strips out all text information such as comments and EXIF data
            ],
            Pngquant::class => [
                '--force',
                // required parameter for this package
            ],
        ])->save();

        // Generate thumbnail for this image
        $width = ceil($image->getWidth() / 4);
        $height = ceil($image->getHeight() / 4);

        $image->fit(Manipulations::FIT_CONTAIN, $width, $height)
            ->quality(20)
            ->format(Manipulations::FORMAT_PNG)
            ->save("{$fileDir}/thumbnail.png");
    }
}
