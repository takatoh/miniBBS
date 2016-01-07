<?php
  $dir = opendir('data');
  $files = array();

  while (false !== ($f = readdir($dir))) {
    $f = 'data/' . $f;
    if (is_file($f)) {
      array_push($files, $f);
    }
  }

  $files = files_sort_by_mtime($files);


  function files_sort_by_mtime($files) {
    usort($files, "order_by_mtime");
    return $files;
  }

  function order_by_mtime($a, $b) {
    $mtime_a = filemtime($a);
    $mtime_b = filemtime($b);
    if ($mtime_a > $mtime_b) {
      return -1;
    } else if ($mtime_a < $mtime_b) {
      return 1;
    } else {
      return 0;
    }
  }
?>
<html>
  <head>
    <title>mini BBS</title>
  </head>
  <body>
    <div id="container">
      <div id="header">
        <h1>mini BBS</h1>
      </div>

      <div id="main">
        <div id="post_form">
          <form method="POST" action="index.php">
            Name: <input type="text" name="name" value="no name" /><br />
            Subject: <input type="text" name="subject" value="untitled" /><br />
            Content: <textarea name="content" rows=8 cols=50 /></textarea><br />
            <input type="submit" value="Post" />
          </form>
        </div>

        <div id="messages">
          <ol>
            <?php foreach ($files as $file) { ?>
            <li><?php echo $file; ?></li>
            <?php } ?>
          </ol>
        </div>

      </div>
    </dib>
  </body>
</html>
