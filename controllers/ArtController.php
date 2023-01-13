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
use yii\web\ForbiddenHttpException;
use function is_dir;
use function is_file;
use function str_contains;
use function str_starts_with;

class ArtController extends Controller
{


    public function actionPing()
    {
        return 'Pong';
    }

    // http://192.168.0.14:8086/art/create-poster?id=2573&seconds=560
    // TODO: check request is coming from logged-in Craft user
    public function actionCreatePoster()
    {

        $userAgent = Yii::$app->request->getUserAgent();

        if (!str_starts_with($userAgent, 'Craft') || !str_contains($userAgent, 'Guzzle')) {
            return $this->asJson([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        /** @var Video $video */
        $id = Yii::$app->request->getQueryParam('id');

        if (!$id) {
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
        if (!$seconds) {
            throw new \http\Exception\InvalidArgumentException('seconds missing');
        }

        if (!Yii::$app->ffmpeg->createPoster($video, $seconds, true)) {
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


}