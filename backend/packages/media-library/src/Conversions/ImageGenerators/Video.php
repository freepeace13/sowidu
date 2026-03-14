<?php

namespace Packages\MediaLibrary\Conversions\ImageGenerators;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Support\Collection;
use Packages\MediaLibrary\Conversions\Conversion;

class Video extends ImageGenerator
{
    public function convert(string $file, ?Conversion $conversion = null): string
    {
        $imageFile = pathinfo($file, PATHINFO_DIRNAME) . '/' . pathinfo($file, PATHINFO_FILENAME) . '.jpg';

        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
        ]);

        $video = $ffmpeg->open($file);
        $duration = $ffmpeg->getFFProbe()->format($file)->get('duration');

        $seconds = $conversion ? $conversion->getExtractVideoFrameAtSecond() : 0;
        $seconds = $duration < $seconds ? 0 : $seconds;

        $frame = $video->frame(TimeCode::fromSeconds($seconds));
        $frame->save($imageFile);

        return $imageFile;
    }

    public function requirementsAreInstalled(): bool
    {
        return class_exists(\FFMpeg\FFMpeg::class);
    }

    public function supportedExtensions(): Collection
    {
        return new Collection(['webm', 'mov', 'mp4']);
    }

    public function supportedMimeTypes(): Collection
    {
        return new Collection(config('media-library.mime_types.videos'));
    }
}
