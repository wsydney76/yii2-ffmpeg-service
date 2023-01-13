<?php

namespace app\services;

use app\models\Video;
use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use yii\helpers\FileHelper;
use function is_dir;
use function is_file;

class FFMpegService
{
    public function createPoster(Video $videoModel, $seconds = 60, $force = false)
    {
        $videoPath = $videoModel->getVideoPath();
        $posterPath = $videoModel->getPosterPath();

        if (!is_file($videoPath)) {
            $videoModel->addError('createPoster', 'Video file does not exist');
            return false;
        }

        if (!$force && is_file($posterPath)) {
            $videoModel->addError('createPoster', 'Poster file exists');
            return false;
        }

        $folderPath = $videoModel->getPosterFolderPath();
        if (!is_dir($folderPath)) {
            FileHelper::createDirectory($folderPath);
        }

        // FileHelper::cycle($posterPath, 4);

        $ffmpeg = FFMpeg::create();
        try {
            $video = $ffmpeg->open($videoPath);
            $frame = $video->frame(TimeCode::fromSeconds($seconds));
            $frame->save($posterPath);
        } catch (Exception $e) {
            $videoModel->addError('createPoster', $e->getMessage());
            return false;
        }
        return true;
    }
}