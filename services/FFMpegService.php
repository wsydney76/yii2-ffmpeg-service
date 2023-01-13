<?php

namespace app\services;

use app\models\Video;
use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use yii\helpers\FileHelper;
use function extract;
use function is_dir;
use function is_file;
use const EXTR_OVERWRITE;

class FFMpegService
{
    public function createPoster(array $paths, $seconds = 60, $force = false): array
    {
        extract($paths, EXTR_OVERWRITE);

        if (!is_file($videoPath)) {
            return [
                'success' => false,
                'message' => "$videoPath not found."
            ];
        }

        if (!$force && is_file($posterPath)) {
            return [
                'success' => false,
                'message' => "$posterPath exists and no force."
            ];
        }

        if (!is_dir($posterFolderPath)) {
            FileHelper::createDirectory($posterFolderPath);
        }

        // FileHelper::cycle($posterPath, 4);

        $ffmpeg = FFMpeg::create();
        try {
            $video = $ffmpeg->open($videoPath);
            $frame = $video->frame(TimeCode::fromSeconds($seconds));
            $frame->save($posterPath);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Error creating image, {$e->getMessage()}"
            ];
        }

        return [
            'success' => true,
            'message' => "Poster created."
        ];
    }
}