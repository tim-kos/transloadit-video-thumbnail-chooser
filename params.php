<?php
require_once('helpers.php');
date_default_timezone_set('UTC');

$authKey    = 'YOUR-TRANSLOADIT-AUTH-KEY';
$authSecret = 'YOUR-TRANSLOADIT-AUTH-SECRET';

$step = isset($_GET['step']) ? $_GET['step'] : '1';

$params = array(
  'auth' => array(
    'expires' => gmdate('Y/m/d H:i:s+00:00', strtotime('+1 hour')),
    'key'     => $authKey,
  ),
  'steps' => array()
);

if ($step === '1') {
  $width = isset($_GET['w']) ? (int) $_GET['w'] : 400;
  $height = isset($_GET['h']) ? (int) $_GET['h'] : 300;
  $count = isset($_GET['c']) ? (int) $_GET['c'] : 8;

  $params['steps'] = array(
    "extract_thumbs" => array(
      "robot"  => "/video/thumbs",
      "use"    => ":original",
      "width"  => $width,
      "height" => $height,
      "count"  => $count
    )
  );
} else {
  $thumbUrl = isset($_GET['thumb_url']) ? urldecode($_GET['thumb_url']) : '';
  $videoUrl = isset($_GET['video_url']) ? urldecode($_GET['video_url']) : '';

  $params['steps'] = array(
    "import_thumb" => array(
      "robot" => "/http/import",
      "url"   => $thumbUrl
    ),
    "import_video" => array(
      "robot" => "/http/import",
      "url"   => $videoUrl
    ),
    "flv" => array(
      "robot"  => "/video/encode",
      "use"    => "import_video",
      "preset" => "flash",
      "width"  => 640,
      "height" => 360
    ),
    "webm" => array(
      "robot"  => "/video/encode",
      "use"    => "import_video",
      "preset" => "webm",
      "width"  => 640,
      "height" => 360
    ),
    "mp4" => array(
      "robot"  => "/video/encode",
      "use"    => "import_video",
      "preset" => "ipad-high",
      "width"  => 640,
      "height" => 360
    ),
    "ogv" => array(
      "robot"        => "/video/encode",
      "use"          => "import_video",
      "ffmpeg_stack" => "v2.0.0",
      "ffmpeg" => array(
        "vcodec" => "libtheora",
        "acodec" => "libvorbis",
        "f"      => "ogg"
      ),
      "width"  => 640,
      "height" => 360
    ),
    "image_resize_1125x620" => array(
      "robot"  => "/image/resize",
      "use"    => "import_thumb",
      "width"  => 1125,
      "height" => 620
    ),
    "image_resize_640x360" => array(
      "robot"  => "/image/resize",
      "use"    => "image_resize_1125x620",
      "width"  => 640,
      "height" => 360
    ),
    "image_resize_320x" => array(
      "robot" => "/image/resize",
      "use"   => "image_resize_640x360",
      "width" => 320
    ),
    "image_resize_200x" => array(
      "robot" => "/image/resize",
      "use"   => "image_resize_320x",
      "width" => 200
    )
  );
}

$signature = calcSignature($authSecret, $params);

header('Content-type: application/json');
echo json_encode(compact('signature', 'params'));
?>
