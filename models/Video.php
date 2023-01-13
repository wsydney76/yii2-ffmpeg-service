<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Video extends ActiveRecord
{
    public static function tableName()
    {
        return 'art_videos';
    }

    public function getVideoPath()
    {
        return Yii::$app->params['videoPath'] . $this->folder . '/' . $this->filename;
    }

    public function getPosterPath()
    {
        return $this->getPosterFolderPath() . '/' . $this->getPosterFilename();
    }

    public function getPosterFolderPath()
    {
        return Yii::$app->params['posterPath'] . $this->folder;
    }

    public function getPosterFilename()
    {
        $pathinfo = pathinfo($this->filename);
        return $pathinfo['filename'] . '.jpg';
    }
}