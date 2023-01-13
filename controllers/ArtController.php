<?php

namespace app\controllers;

use app\models\Video;
use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\FileHelper;
use yii\web\Controller;
use function is_dir;
use function is_file;

class ArtController extends Controller
{


    public function actionPing()
    {
        return 'Pong';
    }

    // http://192.168.0.14:8086/art/create-poster?id=2573&seconds=560
    // TODO: check request is coming from logged-in Craft user
    // TODO: -> service component
    public function actionCreatePoster()
    {
        /** @var Video $video */
        $id = Yii::$app->request->getQueryParam('id');

        if(!$id) {
            throw new InvalidArgumentException('Id missing');
        }

//        if (!$id = Yii::$app->security->validateData($id)) {
//            return $this->asErrorJson('Ungültige ID');
//        }

        $video = Video::findOne($id);
        if (!$video) {
            return $this->asJson([
                'success' => false,
                'message' => 'Video nicht gefunden'
            ]);
        }

        $seconds = Yii::$app->request->getQueryParam('seconds');
        if(!$seconds) {
            throw new \http\Exception\InvalidArgumentException('seconds missing');
        }

        if (!$this->createPoster($video, $seconds, true)) {
            return $this->asJson([
                'success' => false,
                'message' => $video->getFirstError('createPoster')
            ]);
        }

        return $this->asJson([
            'success' => true,
            'message' => 'Poster created'
        ]);
    }

    public function createPoster(Video $videoModel, $timecode = 60, $force = false)
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
            $frame = $video->frame(TimeCode::fromSeconds($timecode));
            $frame->save($posterPath);
        } catch (Exception $e) {
            $videoModel->addError('createPoster', $e->getMessage());
            return false;
        }
        return true;
    }
}