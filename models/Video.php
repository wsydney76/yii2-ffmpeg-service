<?php

namespace app\models;

use yii\db\ActiveRecord;

class Video extends ActiveRecord
{
    public const ARTVIDEOS_VIDEOPATH = 'b:/Backups/WS4Media/Videos/art/';
    public const ARTVIDEOS_POSTERPATH = 'b:/Backups/WS4Media/videoposters/art/';
    public const ARTVIDEOS_THUMBSPATH = 'b:/Backups/WS4Media/videothumbs/art/';

    public static function tableName()
    {
        return 'art_videos';
    }

    public function getVideoPath()
    {
        return self::ARTVIDEOS_VIDEOPATH . $this->folder . '/' . $this->filename;
    }

    public function getPosterPath()
    {
        return $this->getPosterFolderPath() . '/' . $this->getPosterFilename();
    }

    public function getPosterFolderPath()
    {
        return self::ARTVIDEOS_POSTERPATH . $this->folder;
    }

    public function getPosterFilename()
    {
        $pathinfo = pathinfo($this->filename);
        return $pathinfo['filename'] . '.jpg';
    }
}