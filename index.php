<?php
require_once('helpers.php');

$results = array();
if (isset($_POST['transloadit'])) {
  $results = prepareTransloaditResults($_POST['transloadit']);
  pr($results);
}
?>
<!doctype html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Transloadit Video Thumbnail Chooser</title>
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <div class="container">
    <h1>Transloadit Video Thumbnail Chooser</h1>
    <p>Upload a video, choose a thumbnail from the video, and then let Transloadit encode both the video and the preview image into several formats.</p>
    <p>You can find the code for this <a href="https://github.com/tim-kos/transloadit-video-thumbnail-chooser">here</a>.</p>

    <div class="row">
      <div class="js-transloadit-upload col-md-4">
        <form class="js-video-upload-form" action="index.php" enctype="multipart/form-data" method="POST">
          <input type="file" name="my_file" accept="video/*" />
        </form>

        <ul class="js-thumbnails thumbnails"></ul>

        <form class="js-final-upload-form final-form" action="index.php" enctype="multipart/form-data" method="POST">
          <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" class="form-control" />
          </div>
          <div class="form-group">
            <label for="email_address">Email address:</label>
            <input type="text" name="email_address" id="email_address" class="form-control" />
          </div>
          <div class="form-group">
            <label for="other_form_field">Another form field:</label>
            <input type="text" name="other_form_field" id="other_form_field" class="form-control" />
          </div>

          <input type="submit" name="Submit" class="btn btn-primary" value="Save" />
        </form>
      </div>
    </div>
  </div>

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
  <script src="//assets.transloadit.com/js/jquery.transloadit2-v2-latest.js"></script>
  <script src="js/transloadit_uploader.js"></script>
</body>
</html>
