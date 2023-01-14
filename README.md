# Yii FFmpeg Service

Temporary workaround.

This is a service that lets you create poster images from videos in case your app runs in an environment where FFmpeg is not installed.

Scenario: Your app runs in a DDEV container, this service runs on the host machine.

Call it like this:

```php
$result = Craft::createGuzzleClient()
    ->get(UrlHelper::url(Env::FFMPEG_SERVICE_BASE_URL . 'create-poster', [
        'paths' => [
             // File paths how this service can access the assets directories 
            'videoPath' => $video->getVideoPath(),
            'posterPath' => $video->getPosterPath(),
            'posterFolderPath' => $video->getPosterFolderPath()
        ],
        'seconds' => $seconds
    ]));

// check for error

$data = Json::decodeIfJson($result->getBody()->getContents());
```

## Todos

* Better check for requests not coming from the Craft app.

